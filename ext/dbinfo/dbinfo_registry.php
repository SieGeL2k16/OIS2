<?php
/**
 * Extension: Database Informations.
 * Displays overview of all features in Oracle database and their usage.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (19-Jul-2011)
 * $Id$
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
<div id="Display_Registry">
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Lists registry entries">
<caption>Registry overview</caption>
<thead><tr>
  <th>Component name</th>
  <th>Version</th>
  <th>Status</th>
  <th>Modified</th>
</tr></thead>
<tbody>
<?php
$SQL = "SELECT COMP_NAME,VERSION,STATUS,MODIFIED FROM DBA_REGISTRY ORDER BY COMP_NAME";
$lv = 0;
$db->QueryResult($SQL);
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
  printf("  <td>%s</td>\n",$d['COMP_NAME']);
  printf("  <td>%s</td>\n",$d['VERSION']);
  printf("  <td>%s</td>\n",$d['STATUS']);
  printf("  <td>%s</td>\n",$d['MODIFIED']);
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Lists registry history entries">
<caption>Registry History</caption>
<thead><tr>
  <th>Action time</th>
  <th>Action</th>
  <th>Namespace</th>
  <th>Version</th>
  <th>ID</th>
  <th>Comments</th>
</tr></thead>
<tbody>
<?php
$lv = 0;
$SQL = "SELECT TO_CHAR(ACTION_TIME,'DD-Mon-YYYY HH24:MI:SS') AS AT,ACTION,NAMESPACE,VERSION,ID,COMMENTS FROM DBA_REGISTRY_HISTORY ORDER BY ACTION_TIME";
$db->QueryResult($SQL);
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
  printf("  <td>%s</td>\n",$d['AT']);
  printf("  <td>%s</td>\n",$d['ACTION']);
  printf("  <td>%s</td>\n",$d['NAMESPACE']);
  printf("  <td>%s</td>\n",$d['VERSION']);
  printf("  <td>%s</td>\n",$d['ID']);
  printf("  <td>%s</td>\n",$d['COMMENTS']);
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
</div>
<?php
if($lv == 0)
  {
  print("No history entries found.\n");
  }
else
  {
  printf("<strong>%s</strong> entries listed.",$SGLFUNC->FormatNumber($lv));
  }
?>
</div>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
