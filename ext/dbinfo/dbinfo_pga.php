<?php
/**
 * Extension: Database Informations.
 * Displays PGA usage informations of the active instance.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (05-Sep-2009)
 * $Id: dbinfo_pga.php,v 1.3 2010/12/20 23:27:06 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="PGA_usage">
<?php
if($OIS2EXT->Get_Oracle_Version() < 9)
  {
  $OIS2EXT->ErrorExit('The View $PGASTAT is only available from Oracle 9i or higher!');
  }
?>
<table cellspacing="1" cellpadding="4" border="0" id="pga_stats" class="datatable" summary="PGA statistics">
<caption>PGA statistics</caption>
<thead><tr>
  <th>Parameter</th>
  <th>Value</th>
</tr></thead>
<tbody>
<?php
$db->QueryResult('SELECT NAME,VALUE,UNIT FROM V$PGASTAT');
$lv = 0;
while($m = $db->FetchResult())
  {
  if($lv % 2) $myback = 'td_even';
  else $myback = 'td_odd';
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"left\">".$m['NAME']."</td>\n");
  switch($m['UNIT'])
    {
    case  'bytes':
          $val = $SGLFUNC->FormatSize($m['VALUE']);
          break;
    case  'percent':
          $val = $m['VALUE'].'%';
          break;
    default:
          $val = $SGLFUNC->FormatNumber($m['VALUE']);
    }
  echo("  <td align=\"right\">".$val."</td>\n");
  echo("</tr>\n");

  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
