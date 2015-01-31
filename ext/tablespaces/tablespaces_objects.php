<?php
/**
 * Extension: Tablespace Informations.
 * Displays objects stored in a given tablespace.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (07-Sep-2009)
 * $Id: tablespaces_objects.php 2 2011-06-30 18:10:40Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension. As we are a sub-page we have to tell navigation and GetExtInfo() our parent:
define('NAV_OVERRIDE_FILE' , 'tablespaces.php');
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'tablespaces.php');

// Get parameter
$sp['tsname'] = (isset($_GET['TS']))  ? strip_tags($_GET['TS']) : '';
$addHeader = '';
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$OIS2EXT->PrintExtHeader('Display objects stored in tablespace &quot;'.$sp['tsname'].'&quot;',$addHeader);
if($sp['tsname'] == '')
  {
  $OIS2EXT->ErrorExit('ERROR: Invalid Tablespace name retrieved - aborting!');
  }
?>
<div id="page_content">
<a href="tablespaces.php" class="btn">Back to tablespace overview</a><br>
<br>
<?php
$stats = $db->QueryHash('SELECT COUNT(*) AS CNT FROM DBA_SEGMENTS WHERE TABLESPACE_NAME=:tsname',OCI_ASSOC,0,$sp);
printf("<b>%s</b> objects found.<br>\n<br>\n",$SGLFUNC->FormatNumber($stats['CNT']));
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of tablespace objects">
<thead><tr>
  <th>Owner</th>
  <th>Segment name</th>
  <th>Type</th>
  <th>Partition name</th>
  <th>Bytes</th>
</tr></thead>
<tbody>
<?php
$query=<<<EOM
SELECT  OWNER,
        SEGMENT_NAME,
        PARTITION_NAME,
        SEGMENT_TYPE,
        BYTES
  FROM  DBA_SEGMENTS
 WHERE  TABLESPACE_NAME=:tsname
 ORDER BY OWNER, SEGMENT_NAME
EOM;
$db->QueryResultHash($query,$sp);
$lv = 0;
$totsize = 0;
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
  if($d['PARTITION_NAME'] == '') $d['PARTITION_NAME'] = '---';
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td>".$d['OWNER']."</td>\n");
  echo("  <td>".$d['SEGMENT_NAME']."</td>\n");
  echo("  <td>".$d['SEGMENT_TYPE']."</td>\n");
  echo("  <td>".$d['PARTITION_NAME']."</td>\n");
  echo("  <td align=\"right\" title=\"".$SGLFUNC->FormatSize($d['BYTES'])."\">".$SGLFUNC->FormatNumber($d['BYTES'])."</td>\n");
  echo("</tr>\n");
  $totsize+=$d['BYTES'];
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
<tfoot>
<?php
if($lv % 2)
  {
  $myback = 'td_odd';
  }
else
  {
  $myback = 'td_even';
  }
echo("<tr class=\"".$myback."\">\n");
echo("  <td colspan=\"4\" align=\"left\">Total: ".$SGLFUNC->FormatNumber($lv)." Objects</td>\n");
echo("  <td align=\"right\">".$SGLFUNC->FormatSize($totsize)."</td>\n");
echo("</tr>\n");
?>
</tfoot>
</table>
<br>
<a href="tablespaces.php" class="btn">Back to tablespace overview</a>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
