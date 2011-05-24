<?php
/**
 * Extension: Tablespace Informations.
 * Displays all tablespaces.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (06-Sep-2009)
 * $Id: tablespaces.php,v 1.3 2010/07/18 22:24:56 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$addHeader=<<<EOM
<style type="text/css">
#ts_overview {
  float         : left;
  margin-right  : 20px;
}

#ts_graph {
  margin-top    : 16px;
}
</style>
EOM;
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
$jpgraph  = $OIS2EXT->Get_JPGraph_Path();
$pic_x = 320;
$pic_y = 200;
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_DBA_Flag() == FALSE || $OIS2EXT->Get_V_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access use this plugin - aborting!');
  }

// Get the images base dir from OIS2:
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();

$addfield1 = $addfield2 = $addfield3 = '';

if($OIS2EXT->Get_Oracle_Version() >= 10)
  {
  $addfield1 = ',BIGFILE';
  $addfield2 = ',t1.BIGFILE';
  $addfield3 = ',t2.BIGFILE';
  }
else
  {
  $addfield1 = $addfield2 = $addfield3 = '';
  }
if($OIS2EXT->Get_Oracle_Version() >= 9)
  {
  $addfield1.= ',BLOCK_SIZE';
  $addfield2.= ',t1.BLOCK_SIZE';
  $addfield3.= ',t2.BLOCK_SIZE';
  }
else
  {
  $addfield1.=  ',0 AS BLOCK_SIZE';
  $addfield1.=  ',0 AS BLOCK_SIZE';
  $addfield1.=  ',0 AS BLOCK_SIZE';
  }
// Query three DBA Views to gather all required Tablespace informations.
$myquery=<<<EOM
SELECT  COUNT(H1) AS H1CNT,
        H2,
        SUM(H3) AS H3SUM,
        SUM(H4) AS H4SUM,
        H5,
        H6,
        H7,
        H8
        $addfield1
  FROM
  (
SELECT  t1.TABLESPACE_NAME AS H1,
        t1.TABLESPACE_NAME AS H2,
        d1.BYTES AS H3,
        (SELECT SUM(f1.BYTES) FROM DBA_FREE_SPACE f1 WHERE f1.FILE_ID=d1.FILE_ID) as H4,
        LOWER(t1.STATUS) AS H5,
        DECODE(t1.LOGGING,'LOGGING','YES','NO') AS H6,
        DECODE(t1.CONTENTS,'TEMPORARY','YES','NO') AS H7,
        LOWER(t1.EXTENT_MANAGEMENT) AS H8
        $addfield2
  FROM  DBA_DATA_FILES d1, DBA_TABLESPACES t1
 WHERE  t1.TABLESPACE_NAME=d1.TABLESPACE_NAME
UNION
SELECT  t2.TABLESPACE_NAME H1,
        t2.TABLESPACE_NAME H2,
        d2.BYTES H3,
        (SELECT SUM(f2.BYTES) FROM DBA_FREE_SPACE f2 WHERE f2.FILE_ID=d2.FILE_ID) H4,
        LOWER(t2.STATUS) AS H5,
        DECODE(t2.LOGGING,'LOGGING','YES','NO') H6,
        DECODE(t2.CONTENTS,'TEMPORARY','YES','NO') H7,
        LOWER(t2.EXTENT_MANAGEMENT) H8
        $addfield3
  FROM  DBA_TEMP_FILES d2, DBA_TABLESPACES t2
 WHERE  t2.TABLESPACE_NAME=d2.TABLESPACE_NAME
   )
 GROUP  BY H2,H5,H6,H7,H8 $addfield1
 ORDER  BY H2
EOM;
$db->QueryResult($myquery);
?>
<div>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="ts_overview" summary="Overview of all tablespaces">
<caption>Click on Tablespace name to view datafile details. Click on mag to view objects in this tablespace.</caption>
<thead><tr>
  <th>#</th>
  <th>Tablespace<br>Name</th>
  <th>Size</th>
  <th>Used</th>
  <th>Free</th>
  <th>% full</th>
  <th>Status</th>
  <th>Logg<br>ing</th>
  <th>Temp<br>orary</th>
  <th>Big<br>File?</th>
  <th>Manage<br>ment</th>
  <th>Block<br>Size</th>
  <th>&nbsp;</th>
</tr></thead>
<tbody>
<?php
$datafiles  = 0.00;
$totalbytes = 0.00;
$totalfree  = 0.00;
$yesno = array( 'YES' => '<img src="'.$OIS_IMG.'tick.png" alt="Yes" border="0" title="%s">',
                'NO'  => '<img src="'.$OIS_IMG.'cross.png" alt="No" border="0" title="%s">'
              );
$lv=0;
// These two arrays are used to store both tablespaces and their sizes. They will be written to the session for the jpgraph below.
$ts_names = array();
$ts_sizes = array();
while($r = $db->FetchResult())
  {
  $ts_names[]=$r['H2'];
  $ts_sizes[]=$r['H3SUM'];
  if(isset($r['BIGFILE']) == false)       // BIGFILE is only available from 10g+
    {
    $r['BIGFILE'] = 'NO';
    }
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }
  if(100-(floatval($r['H4SUM']) * 100) / floatval($r['H3SUM']) >=95 && $r['H7'] != "Yes")
    {
    $mywarn = $OIS_IMG.'led_red.gif';
    $wtext  = 'Tablespace size is grown to 95% !';
    }
  else
    {
    $mywarn = $OIS_IMG.'led_green.gif';
    $wtext  = 'Tablespace size is okay.';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"center\" title=\"Number of datafiles for this tablespace\">".$r['H1CNT']."</td>\n");
  echo("  <td align=\"left\"><a href=\"tablespaces_datafiles.php?TS=".URLEncode($r['H2'])."&amp;TMP=".$r['H7']."\">".$r['H2']."</a></td>\n");
  echo("  <td align=\"right\" nowrap title=\"Total tablespace size\">".$SGLFUNC->FormatSize($r['H3SUM'])."&nbsp;</td>\n");
  $diff = ($r['H3SUM']-$r['H4SUM']);
  if($diff < 0)
    {
    $diff = $r['H4SUM'];
    }
  echo("  <td align=\"right\" nowrap title=\"Used space in this tablespace\">".$SGLFUNC->FormatSize($diff)."&nbsp;</td>\n");
  printf("  <td align=\"right\" nowrap title=\"Free space in this tablespace\">%s&nbsp;</td>\n",$SGLFUNC->FormatSize($r['H4SUM']));
  if($r['H3SUM'])
    {
    $pcnt = 100-(floatval($r['H4SUM']) * 100) / floatval($r['H3SUM']);
    }
  else
    {
    $pcnt = 0;
    }
  printf("  <td align=\"right\" nowrap>%5.2f%%</td>\n",$pcnt);
  echo("  <td align=\"center\">".$r['H5']."</td>\n");
  echo("  <td align=\"center\">".sprintf($yesno[$r['H6']],'Logging: '.StrToLower($r['H6']))."</td>\n");
  echo("  <td align=\"center\">".sprintf($yesno[$r['H7']],'Temporary: '.StrToLower($r['H7']))."</td>\n");
  echo("  <td align=\"center\">".sprintf($yesno[$r['BIGFILE']],'Bigfile tablespace: '.StrToLower($r['BIGFILE']))."</td>\n");
  if(StrToUpper($r['H8'])=='LOCAL')
    {
    echo("  <td align=\"center\">".UCWords($r['H8'])."</td>\n");
    }
  else
    {
    echo("  <td align=\"center\"><b>".UCWords($r['H8'])."</b></td>\n");
    }
  echo("  <td align=\"right\">".$SGLFUNC->FormatSize($r['BLOCK_SIZE'])."</td>\n");
  echo("  <td align=\"center\"><a href=\"tablespaces_objects.php?TS=".URLEncode($r['H2'])."\"><img src=\"".$OIS_IMG."viewmag.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" title=\"List objects stored in tablespace ".$r['H2']."\"></a></td>\n");
  echo("</tr>\n");
  $lv++;
  $totalbytes = floatval($totalbytes) +floatval($r['H3SUM']);
  $totalfree  = floatval($totalfree) + floatval($r['H4SUM']);
  $datafiles  = floatval($datafiles) + floatval($r['H1CNT']);
  }
$db->FreeResult();

// Now write these values to our session (only if JPGraph is activated!)

if($jpgraph != '')
  {
  $_SESSION['TS_NAMES'] = $ts_names;
  $_SESSION['TS_SIZES'] = $ts_sizes;
  @session_write_close();
  }
echo("</tbody>\n<tfoot>\n");
if($lv % 2)
  {
  $myback = 'td_odd';
  }
else
  {
  $myback = 'td_even';
  }
echo("<tr class=\"".$myback."\">\n");
echo("  <td colspan=\"2\" align=\"right\">Total:</td>\n");
echo("  <td align=\"right\" nowrap>".$SGLFUNC->FormatSize($totalbytes)."&nbsp;</td>\n");
echo("  <td align=\"right\" nowrap>".$SGLFUNC->FormatSize($totalbytes-$totalfree)."&nbsp;</td>\n");
echo("  <td align=\"right\" nowrap>".$SGLFUNC->FormatSize($totalfree)."&nbsp;</td>\n");
if($totalbytes)
  {
  $tpcnt = 100-(floatval($totalfree) * 100) / floatval($totalbytes);
  }
else
  {
  $tpcnt = 0;
  }
printf("  <td align=\"right\" nowrap>%5.2f%%</td>\n",$tpcnt);
echo("  <td colspan=\"7\" align=\"right\">Tablespaces: ".$lv." | Datafiles: ".$datafiles."&nbsp;</td>\n");
echo("</tr>\n");
?>
</tfoot>
</table>
<?php
if($jpgraph == '')
  {
  $img = '<img src="'.$OIS_IMG.'trans.gif" width="10" height="200" border="0" alt="JPGRAPH NOT AVAILABLE" title="JPGraph is not available / not configured.">';
  }
else
  {
  $img = '<img src="tablespaces_graph.php?W='.$pic_x.'&amp;H='.$pic_y.'" width="'.$pic_x.'" height="'.$pic_y.'" border="0" id="ts_graph" alt="JPGraph" title="Overview of all tablespaces">';
  }
echo($img);
$myquery=<<<EOM
SELECT SUBSTR(df.file#,1,2) AS ID,
       NAME AS FILENAME,
       PHYRDS AS PHYREADS,
       PHYWRTS AS PHYWRITES,
       PHYBLKRD AS BLKREADS,
       PHYBLKWRT AS BLKWRITES,
       READTIM AS READTIME,
       WRITETIM AS WRITETIME,
       (SUM(PHYRDS+PHYWRTS+PHYBLKRD+PHYBLKWRT+READTIM)) AS FILETOTAL
  FROM V\$FILESTAT fs, V\$DATAFILE df
 WHERE fs.file# = df.file#
 GROUP BY df.file#, df.name, phyrds, phywrts, phyblkrd, phyblkwrt, readtim, writetim
 ORDER BY SUM(PHYRDS+PHYWRTS+PHYBLKRD+PHYBLKWRT+READTIM) DESC, df.name
EOM;
?>
</div>
<div class="clear"></div>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Lists disc activity by tablespaces">
<caption>Disc activity by tablespace datafiles</caption>
<thead><tr>
  <th>ID#</th>
  <th>Filename</th>
  <th>Physical<br>reads</th>
  <th>Physical<br>writes</th>
  <th>Block<br>reads</th>
  <th>Block<br>writes</th>
  <th>Read<br>time</th>
  <th>Write<br>time</th>
  <th>File<br>totals</th>
</tr></thead>
<tbody>
<?php
$db->QueryResult($myquery);
$lv = 0;
while($d = $db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"center\">".$d['ID']."</td>\n");
  echo("  <td align=\"left\">".$d['FILENAME']."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($d['PHYREADS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($d['PHYWRITES'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($d['BLKREADS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($d['BLKWRITES'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($d['READTIME'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($d['WRITETIME'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($d['FILETOTAL'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
