<?php
/**
 * Extension: Schema viewer.
 * Displays an overview of ALL objects for a given schema including the size for all of the objects found.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (20-Jul-2011)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'viewer.php');
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$schema = $SGLFUNC->GetRequestParam('SCHEMA');
$OIS2EXT->PrintExtHeader('Size of schema &quot;'.$schema.'&quot;','',TRUE);
?>
<div id="fullpage_content">
<?php
// Make first a list of all segments summarized from DBA_SEGMENTS view + the informations from all PL/SQL code, to be found in SOURCE_
$SEGMENTS = array();
$SQL=<<<EOM
SELECT SUM(BYTES) AS BYTES,COUNT(*) AS CNT,SEGMENT_TYPE FROM DBA_SEGMENTS WHERE OWNER=:sch GROUP BY SEGMENT_TYPE
UNION
SELECT SUM(DECODE(SOURCE_SIZE,0,CODE_SIZE,SOURCE_SIZE)) AS BYTES,COUNT(*) AS CNT,TYPE AS SEGMENT_TYPE FROM DBA_OBJECT_SIZE WHERE OWNER=:sch GROUP BY TYPE
ORDER BY 1 DESC,3
EOM;
$sp   = array('sch' => $schema);
$db->QueryResultHash($SQL,$sp);
while($d = $db->FetchResult())
  {
  $SEGMENTS[$d['SEGMENT_TYPE']]['COUNT'] = $d['CNT'];
  $SEGMENTS[$d['SEGMENT_TYPE']]['BYTES'] = $d['BYTES'];
  }
$db->FreeResult();
?>
<table summary="List of segments for a given Schema" class="datatable" style="margin-left:auto;margin-right:auto;">
<thead><tr>
  <th>Segment type</th>
  <th>Count</th>
  <th>Bytes</th>
</tr></thead>
<tbody>
<?php
$lv = 0;
$totsize = 0;
foreach($SEGMENTS AS $sname => $sdata)
  {
  if($lv % 2) $cl = 'td_even';
  else $cl = 'td_odd';
  printf("<tr class=\"%s\">\n",$cl);
  printf("  <td>%s</td>\n",$sname);
  printf("  <td class=\"td_number\">%s</td>\n",$SGLFUNC->FormatNumber($sdata['COUNT']));
  printf("  <td class=\"td_number\" title=\"%s\">%s</td>\n",$SGLFUNC->FormatSize($sdata['BYTES']),$SGLFUNC->FormatNumber($sdata['BYTES']));
  echo("</tr>\n");
  $lv++;
  $totsize+=$sdata['BYTES'];
  }
?>
</tbody>
<tfoot>
<tr>
  <td colspan="2">Total:</td>
  <td class="td_number" title="<?php echo($SGLFUNC->FormatSize($totsize));?>"><?php echo($SGLFUNC->FormatNumber($totsize));?></td>
</tr>
</tfoot>
</table>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter('<div align="center"><a href="javascript:self.close()" class="btn">Close window</a></div>');
?>
