<?php
/**
 * Extension: Database Informations.
 * Displays SGA usage informations of the active instance.
 * @package OIS2\Extension\DBInfo
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.03 (31-Jan-2015)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$jpgraph= $OIS2EXT->Get_JPGraph_Path();
$pic_x  = 300;
$pic_y  = 150;
?>
<div id="SGA_usage">

<div id="div_sga">
<?php
if($jpgraph != '')
  {
  $img = 'dbinfo_graph_sga.php?TYPE=0&amp;W=300&amp;H=150';
  }
else
  {
  $img = $OIS2EXT->Get_OIS2_Image_URL().'trans.gif';
  $pic_x = 10;
  $pic_y = 150;
  }
echo("<img src=\"".$img."\" width=\"".$pic_x."\" height=\"".$pic_y."\" class=\"graph_sga\" alt=\"SGA Overview\">\n");
?>
<table id="sga_mem" class="datatable">
<caption>SGA Memory</caption>
<thead><tr>
  <th>SGA-Segment</th>
  <th>Allocation</th>
</tr></thead>
<?php
/*
 *  Query memory allocations of SGA
 */
$myquery = "SELECT NAME,VALUE FROM V\$SGA ORDER BY VALUE";
$db->QueryResult($myquery);
$totalmem = 0;
$lv = 0;
while($d = $db->FetchResult())
  {
  if($lv % 2) $myback = 'td_even';
  else $myback = 'td_odd';
  echo("<tbody><tr class=\"".$myback."\">\n");
  echo("  <td class=\"td_txt_l\">".$d['NAME']."</td>\n");
  echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatSize($d['VALUE'])."</td>\n");
  echo("</tr></tbody>\n");
  $lv++;
  $totalmem+=$d['VALUE'];
  }
if($lv % 2) $myback = 'td_even';
else $myback = 'td_odd';
echo("<tbody><tr class=\"".$myback."\">\n");
echo("  <td class=\"td_txt_l\">SGA total:</td>\n");
echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatSize($totalmem)."</td>\n");
echo("</tr></tbody>\n");
$db->FreeResult();
?>
</table>
</div>

<div id="div_free_sga">
<?php
if($jpgraph != '')
  {
  $img = 'dbinfo_graph_sga.php?TYPE=1&amp;W=300&amp;H=150';
  }
else
  {
  $img = $OIS2EXT->Get_OIS2_Image_URL().'trans.gif';
  $pic_x = 10;
  $pic_y = 150;
  }
echo("<img src=\"".$img."\" width=\"".$pic_x."\" height=\"".$pic_y."\" class=\"graph_sga\" alt=\"Free SGA Memory\">\n");
?>
<table id="sga_free_mem" class="datatable">
<caption>Free SGA Memory</caption>
<thead><tr>
  <th>SGA-Segment</th>
  <th>Free Memory</th>
</tr></thead>
<?php
/*
 *  Query free memory of SGA
 */
$myquery = "SELECT INITCAP(POOL) POOL,BYTES FROM V\$SGASTAT WHERE NAME='free memory' ORDER BY POOL";
$totalmem = 0;
$lv = 0;
$db->QueryResult($myquery);
while($d = $db->FetchResult())
  {
  if($lv % 2) $myback = 'td_even';
  else $myback = 'td_odd';
  echo("<tbody><tr class=\"".$myback."\">\n");
  echo("  <td class=\"td_txt_l\">".$d['POOL']."</td>\n");
  echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatSize($d['BYTES'])."</td>\n");
  echo("</tr></tbody>\n");
  $lv++;
  $totalmem+=$d['BYTES'];
  }
$db->FreeResult();
?>
</table>
</div>
<div class="clear"></div>

<?php
/*
COMPONENT       VARCHAR2(64)  Component name
CURRENT_SIZE    NUMBER        Current size of the component
MIN_SIZE        NUMBER        Minimum size of the component since instance startup
MAX_SIZE        NUMBER        Maximum size of the component since instance startup
OPER_COUNT      NUMBER        Number of operations since instance startup
LAST_OPER_TYPE  VARCHAR2(6)   Last completed operation for the component: n GROW n SHRINK
LAST_OPER_MODE  VARCHAR2(6)   Mode of the last completed operation: n MANUAL n AUTO
LAST_OPER_TIME  DATE          Start time of the last completed operation
GRANULE_SIZE    NUMBER        Granularity of the grow or the shrink operation

9i introduced a new view called V$SGA_DYNAMIC_COMPONENTS which doesn't exist in 8i, so check first database:
*/
$data = $db->Query('SELECT VERSION FROM V$INSTANCE');
if(intval($data['VERSION']) >= 9)
  {
  $db->QueryResult("SELECT COMPONENT,CURRENT_SIZE,MIN_SIZE,MAX_SIZE,OPER_COUNT,LAST_OPER_TYPE,LAST_OPER_MODE,LAST_OPER_TIME,GRANULE_SIZE FROM V\$SGA_DYNAMIC_COMPONENTS");
  echo<<<EOM
<table id="sga_dynamic" class="datatable">
<caption>Dynamic SGA components:</caption>
<thead><tr>
  <th>Component</th>
  <th title="Current size of the component">Current<br>size</th>
  <th title="Minimum size of the component since instance startup">Min.<br>size</th>
  <th title="Maximum size of the component since instance startup">Max.<br>size</th>
  <th title="Number of operations since instance startup"># of<br>oper.</th>
  <th>Last oper<br>type / mode</th>
  <th title="Granularity of the grow or the shrink operation">Granule<br>size</th>
</tr></thead>
EOM;
  $lv = 0;
  while($d = $db->FetchResult())
    {
    if($lv % 2)
      {
      $myback = 'td_even';
      }
    else
      {
      $myback = 'td_odd';
      }
    echo("<tbody><tr class=\"".$myback."\">\n");
    echo("  <td>".$d['COMPONENT']."</td>\n");
    echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatSize($d['CURRENT_SIZE'])."</td>\n");
    echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatSize($d['MIN_SIZE'])."</td>\n");
    echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatSize($d['MAX_SIZE'])."</td>\n");
    echo("  <td class=\"td_txt_c\">".$d['OPER_COUNT']."</td>\n");
    echo("  <td class=\"td_txt_c\">".(isset($d['OPER_TYPE']) ? $d['OPER_TYPE'] : '---')." / ".(isset($d['OPER_MODE']) ? $d['OPER_MODE'] : '---')."</td>\n");
    echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatSize($d['GRANULE_SIZE'])."</td>\n");
    echo("</tr></tbody>\n");
    $lv++;
    }
  $db->FreeResult();
  echo("</table>\n");
  }
?>

<table id="sga_lib_sum" class="datatable">
<caption>Current SGA Library Summary (Pin HitRatio should be 1)</caption>
<thead><tr>
  <th>Library name</th>
  <th>Gets</th>
  <th>Get Hit<br>Ratio</th>
  <th>Pins</th>
  <th>Pin Hit<br>Ratio</th>
  <th>Reloads</th>
  <th>Invalidations</th>
</tr></thead>
<tbody>
<?php
$myquery=<<<EOM
SELECT INITCAP(NAMESPACE) AS LIBRARY,
       GETS,
       GETHITRATIO,
       PINS,
       PINHITRATIO,
       RELOADS, INVALIDATIONS
  FROM V\$LIBRARYCACHE
EOM;
$db->QueryResult($myquery);
$lv = 0;
while($d = $db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_even';
    }
  else
    {
    $myback = 'td_odd';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td class=\"td_txt_l\">".$d['LIBRARY']."</td>\n");
  echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatNumber($d['GETS'])."</td>\n");
  printf("  <td class=\"td_txt_r\">%s</td>\n",$SGLFUNC->FormatNumber(preg_replace("/,/",".",$d['GETHITRATIO']),2));
  echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatNumber($d['PINS'])."</td>\n");
  printf("  <td class=\"td_txt_r\">%s</td>\n",$SGLFUNC->FormatNumber(preg_replace("/,/",".",$d['PINHITRATIO']),2));
  echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatNumber($d['RELOADS'])."</td>\n");
  echo("  <td class=\"td_txt_r\">".$SGLFUNC->FormatNumber($d['INVALIDATIONS'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
<div class="clear"></div>

<table id="sga_shared_pool" class="datatable">
<caption>Shared Pool&#39;s Library Cache Information</caption>
<thead><tr>
  <th>Memory Area</th>
  <th>Value</th>
</tr></thead>
<?php
flush();
$sharedpoolinfo=<<<EOM
SELECT
  (SELECT VALUE FROM V\$PARAMETER WHERE NAME='shared_pool_size') AS SHAREDPOOLSIZE,
  (SELECT SUM(sharable_mem+persistent_mem+runtime_mem) FROM V\$SQLAREA) AS USED,
  (SELECT SUM(sharable_mem) FROM V\$SQLAREA) AS SHAREABLE,
  (SELECT SUM(persistent_mem) FROM V\$SQLAREA) AS PERSISTENT,
  (SELECT SUM(runtime_mem) FROM V\$SQLAREA) AS RUNTIME,
  (SELECT COUNT(*) FROM V\$SQLAREA) AS SQLSTATEMENTS,
  (SELECT COUNT(*) FROM V\$DB_OBJECT_CACHE) AS NUMBEROBJECTS,
  (SELECT DECODE(COUNT(*),'',0,COUNT(*)) FROM V\$DB_OBJECT_CACHE WHERE KEPT='YES') AS NOCHUNKS,
  (SELECT DECODE(SUM(sharable_mem),'',0,SUM(sharable_mem)) FROM V\$DB_OBJECT_CACHE WHERE  KEPT='YES') AS CHUNKSSIZE,
  (SELECT COUNT(*) FROM V\$SESSION A, V\$SQLTEXT B WHERE A.SQL_ADDRESS||A.SQL_HASH_VALUE = B.ADDRESS||B.HASH_VALUE) AS PINNED,
  (SELECT SUM(sharable_mem+persistent_mem+runtime_mem) FROM V\$SESSION A, V\$SQLTEXT B, V\$SQLAREA C WHERE A.SQL_ADDRESS||A.SQL_HASH_VALUE = B.ADDRESS||B.HASH_VALUE AND B.ADDRESS||B.HASH_VALUE = C.ADDRESS||C.HASH_VALUE) AS PINNEDSIZE
 FROM DUAL
EOM;
$shpool = $db->Query($sharedpoolinfo);
$shared_pool_size_available = $shpool['SHAREDPOOLSIZE'] - $shpool['USED'];
if($shpool['SHAREDPOOLSIZE'] == 0) $shpoolsize = 'AUTO';
else $shpoolsize = $SGLFUNC->FormatSize($shpool['SHAREDPOOLSIZE']);
?>
<tr class="td_even">
  <td class="td_txt_l">Shared Pool Size:</td>
  <td class="td_txt_r"><?php echo($shpoolsize);?></td>
</tr>
<tr class="td_odd">
  <td class="td_txt_l">Shared Pool used (total):</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatSize($shpool['USED']));?></td>
</tr>
<tr class="td_even">
  <td class="td_txt_l">Shared Pool used (sharable):</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatSize($shpool['SHAREABLE']));?></td>
</tr>
<tr class="td_odd">
  <td class="td_txt_l">Shared Pool used (persistent):</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatSize($shpool['PERSISTENT']));?></td>
</tr>
<tr class="td_even">
  <TD class="td_txt_l">Shared Pool used (runtime):</td>
  <TD class="td_txt_r"><?php echo($SGLFUNC->FormatSize($shpool['RUNTIME']));?></td>
</TR>
<tr class="td_odd">
  <td class="td_txt_l">Shared Pool available:</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatSize($shared_pool_size_available));?></td>
</TR>
<tr class="td_even">
  <td class="td_txt_l">Number of SQL Statements:</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatNumber($shpool['SQLSTATEMENTS']));?></td>
</tr>
<tr class="td_odd">
  <td class="td_txt_l">Number of programmatic constructs:</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatNumber($shpool['NUMBEROBJECTS']));?></td>
</tr>
<tr class="td_even">
  <td class="td_txt_l">Kept programmatic construct chunks:</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatNumber($shpool['NOCHUNKS']));?></td>
</tr>
<tr class="td_odd">
  <td class="td_txt_l">Kept programmatic construct chunks size:</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatSize($shpool['CHUNKSSIZE']));?></td>
</tr>
<tr class="td_even">
  <td class="td_txt_l">Pinned Statements:</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatNumber($shpool['PINNED']));?></td>
</tr>
<tr class="td_odd">
  <td class="td_txt_l">Pinned Statements Size:</td>
  <td class="td_txt_r"><?php echo($SGLFUNC->FormatSize($shpool['PINNEDSIZE']));?></td>
</tr>
</table>

<?php
$myquery=<<<EOM
SELECT (SELECT SUM(pins) FROM V\$LIBRARYCACHE) AS EXECUTIONS,
       (SELECT SUM(reloads) FROM V\$LIBRARYCACHE) AS CACHEMISSES,
       (SELECT SUM(gets) FROM V\$ROWCACHE) AS DDG,
       (SELECT SUM(getmisses) FROM V\$ROWCACHE) AS GM
  FROM DUAL
EOM;
$d = $db->Query($myquery);
$ratio = sprintf("%2.2f",(floatval($d['CACHEMISSES']) / floatval($d['EXECUTIONS'])*100));
$ratio2 = sprintf("%2.2f",100*(floatval($d['GM']) / floatval($d['DDG'])));
?>
<table class="datatable">
<caption>Shared Pool Size - Gets and Misses</caption>
<thead><tr>
  <th>Executions</th>
  <th>Cache misses<br>executions</th>
  <th>% Ratio<BR>(STAY UNDER 1%)</th>
  <th>Data<BR>Dictionary Gets</th>
  <th>Get Misses</th>
  <th>% Ratio<BR>(STAY UNDER 12%)</th>
</tr></thead>
<tbody><tr class="td_even">
  <td class="td_txt_c"><?php echo($SGLFUNC->FormatNumber($d['EXECUTIONS']));?></td>
  <td class="td_txt_c"><?php echo($SGLFUNC->FormatNumber($d['CACHEMISSES']));?></td>
  <td class="td_txt_c"><?php echo($ratio);?>%</td>
  <td class="td_txt_c"><?php echo($SGLFUNC->FormatNumber($d['DDG']));?></td>
  <td class="td_txt_c"><?php echo($SGLFUNC->FormatNumber($d['GM']));?></td>
  <td class="td_txt_c"><?php echo($ratio2);?>%</td>
</tr></tbody>
</table>
<div class="clear"></div>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
