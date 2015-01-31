<?php
/**
 * Extension: Database Informations.
 * Displays overview of all features in Oracle database and their usage.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (19-Jul-2011)
 * $Id: dbinfo_used_features.php 4 2011-09-12 19:14:40Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 * @todo Add selector for DBID
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Used_features">
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Feature usage statistics">
<caption>Oracle Feature usage statistics</caption>
<thead><tr>
  <th>DBID</th>
  <th>Feature name</th>
  <th>DB Version</th>
  <th># usages</th>
  <th>In use</th>
  <th>First used on</th>
  <th>Last used on</th>
</tr></thead>
<tbody>
<?php
// Get the images base dir from OIS2:
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
$BOOL = array( 'FALSE'  => '<img src="'.$OIS_IMG.'cross.png" alt="No" border="0">',
               'TRUE'   => '<img src="'.$OIS_IMG.'tick.png" alt="Yes" border="0">');
$lv = 0;
$db->QueryResult("SELECT DBID,NAME,VERSION,DETECTED_USAGES,CURRENTLY_USED,TO_CHAR(FIRST_USAGE_DATE,'DD-Mon-YYYY HH24:MI:SS') AS FUD,TO_CHAR(LAST_USAGE_DATE,'DD-Mon-YYYY HH24:MI:SS') AS LUD,DESCRIPTION FROM DBA_FEATURE_USAGE_STATISTICS");
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
  printf("<tr class=\"%s\">\n",$myback);
  printf("  <td>%s</td>\n",$d['DBID']);
  printf("  <td title=\"%s\">%s</td>\n",htmlentities($d['DESCRIPTION'],ENT_COMPAT,'utf-8'),$d['NAME']);
  printf("  <td>%s</td>\n",$d['VERSION']);
  printf("  <td class=\"td_number\">%s</td>\n",$SGLFUNC->FormatNumber($d['DETECTED_USAGES']));
  printf("  <td class=\"td_txt_c\">%s</td>\n",$BOOL[$d['CURRENTLY_USED']]);
  printf("  <td class=\"td_txt_r\">%s</td>\n",$d['FUD']);
  printf("  <td class=\"td_txt_r\">%s</td>\n",$d['LUD']);
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
<?php
printf("<strong>%s</strong> feature(s) listed.",$SGLFUNC->FormatNumber($lv));
?>
</div>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
