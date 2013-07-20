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
$OIS2EXT->PrintExtHeader('Show schema overview for &quot;'.$schema.'&quot;','',TRUE);
$SQL=<<<EOM
SELECT 	SEGMENT_TYPE,
		    ROUND( bytes/1024/1024,2 ) MBYTES,
     	  ROUND( RATIO_TO_REPORT(BYTES) OVER () * 100, 2 ) PCT
  FROM (
        SELECT  SUM(BYTES) BYTES,
                SEGMENT_TYPE
          FROM  DBA_EXTENTS
         WHERE  OWNER = :o
         GROUP BY SEGMENT_TYPE
       )
   ORDER BY SEGMENT_TYPE
EOM;
$sp = array('o' => $schema);
$db->QueryResultHash($SQL,$sp);
?>
<div id="fullpage_content">
<table class="datatable">
<caption>Allocated segment size of schema objects</caption>
<thead><tr>
  <th>Segment type</th>
  <th>Size in MB</th>
  <th>Pct</th>
</tr></thead>
<tbody>
<?php
$lv = 0;
$mb = 0.00;
while($d = $db->FetchResult())
  {
  if($lv % 2) $mycl = 'td_odd';
  else $mycl = 'td_even';
  echo("<tr class=\"".$mycl."\">\n");
  printf("  <td>%s</td>\n",$d['SEGMENT_TYPE']);
  printf("  <td align=\"right\">%s MB</td>\n",$SGLFUNC->FormatNumber($d['MBYTES'],2));
  printf("  <td align=\"right\">%s%%</td>\n",$SGLFUNC->FormatNumber($d['PCT'],2));
  echo("</tr>\n");
  $lv++;
  $mb+=$d['MBYTES'];
  }
$db->FreeResult();
if($lv % 2) $mycl = 'td_odd';
else $mycl = 'td_even';
echo("<tr class=\"".$mycl."_bold\">\n");
echo("  <td>Total:</td>\n");
printf("  <td align=\"right\">%s MB</td>\n",$SGLFUNC->FormatNumber($mb,2));
echo("  <td>&nbsp;</td>\n");
echo("</tr>\n");
?>
</tbody>
</table>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter('<div align="center"><a href="javascript:self.close()" class="btn">Close window</a></div>');
?>
