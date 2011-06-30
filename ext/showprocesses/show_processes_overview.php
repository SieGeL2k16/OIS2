<?php
/**
 * Extension: ShowProcesses.
 * Gives an overview about processes and sessions including top lists.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (13-Jul-2010)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Process_Overview">
<?php
$procset = $db->Query("SELECT (SELECT VALUE FROM V\$PARAMETER WHERE UPPER(NAME) = 'PROCESSES') AS MAXPROC, (SELECT COUNT(*) FROM V\$SESSION) AS CURRPROC FROM DUAL");
?>
<div>
Click on an entry to view details | <?php printf("Processes in usage: <b>%d</b> | Max. allowed processes: <b>%d</b>",$procset['CURRPROC'],$procset['MAXPROC']);?>
</div>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of currently connected Oracle processes and sessions">
<caption><small>BOLD => Long running processes</small></caption>
<thead><tr>
  <th>PROCESS</th>
  <th>SID</th>
  <th>SERIAL#</th>
  <th>ORACLE USER</th>
  <th>OS USER</th>
  <th>STATUS</th>
  <th>MACHINE</th>
  <th>PROGRAM / MODULE NAME</th>
  <th>LOGIN DATE</th>
  <th>PROCESS TYPE</th>
</tr></thead>
<tbody>
<?php
$myquery = <<<EOM
SELECT s.PROCESS,
       s.USERNAME,
       s.OSUSER,
       s.STATUS,
       s.MACHINE,
       s.PROGRAM,
       TO_CHAR(s.LOGON_TIME,'DD-Mon-YYYY HH24:MI:SS') AS LTIME,
       s.TYPE,
       NVL(s.MODULE,'---') AS MODULE,
       s.SID,
       s.SERIAL#,
       (SELECT COUNT(*) FROM V\$SESSION_LONGOPS sl WHERE sl.SID = s.SID AND sl.SERIAL# = s.SERIAL#) AS ISLONG
       FROM V\$SESSION s
EOM;
//       ORDER BY $dbsortfield $dbsortorder

$db->QueryResult($myquery);
$lv=0;
$link = '<a href="javascript:openPopUp(\'%s\')" title="Click to view details">%s</a>';
while($p = $db->FetchResult())
  {
  if($lv % 2)
    {
    $cl = 'td_odd';
    }
  else
    {
    $cl = 'td_even';
    }
  if($p['USERNAME'] == "") $p['USERNAME'] = "---";
  if(intval($p['ISLONG']) > 0)
    {
    $cl.='_bold';
    }
  echo("<tr class=\"".$cl."\">\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['PROCESS']),$p['PROCESS'])."</td>\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['PROCESS']),$p['SID'])."</td>\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['PROCESS']),$p['SERIAL#'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['USERNAME'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['OSUSER'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),$p['STATUS'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['MACHINE'])))."</td>\n");
  echo("  <td><small>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['PROGRAM'])))."</small><br>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['MODULE'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),$p['LTIME'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),UCFirst(StrToLower($p['TYPE'])))."</td>\n");
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
$OIS2EXT->PrintExtTabFooter();
?>
