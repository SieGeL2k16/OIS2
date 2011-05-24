<?php
/**
 * Extension: ShowProcesses.
 * Displays the Top 20 waiting processes and sessions.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (13-Jul-2010)
 * $Id: show_processes_top_wait.php,v 1.2 2010/11/27 11:20:31 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Top_20_Wait">
Displays the Top 20 waiting sessions in the last 5 minutes.<br>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of currently connected Oracle processes and sessions">
<caption><small>Click on an entry to view details</small></caption>
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
  <th>Resource<br>Consumer<br>Group</th>
  <th>Client<br>Info</th>
</tr></thead>
<tbody>
<?php
$query=<<<SQL
SELECT *
  FROM
  (
  SELECT i.SESSION_ID,
         i.SESSION_SERIAL#,
         i.CNT,
         s.USERNAME,
         s.OSUSER,
         s.MACHINE,
         s.PROGRAM,
         NVL(s.RESOURCE_CONSUMER_GROUP,'---') AS RCG,
         NVL(s.CLIENT_INFO,'---') AS CI,
         s.PROCESS,
         NVL(s.MODULE,'---') AS MODULE,
         s.STATUS,
         TO_CHAR(s.LOGON_TIME,'DD-Mon-YYYY HH24:MI:SS') AS LTIME
    FROM
    (
    SELECT SESSION_ID,
           SESSION_SERIAL#,
           COUNT(*) AS CNT
      FROM V\$ACTIVE_SESSION_HISTORY
     WHERE session_state= 'WAITING' AND
           sample_time > sysdate - INTERVAL '5' MINUTE
     GROUP BY session_id, session_serial#
     ORDER BY COUNT(*) desc
    ) i, V\$SESSION s
   WHERE i.SESSION_ID = s.SID
   )
 WHERE ROWNUM <= 20
SQL;
$lv = 0;
$db->QueryResult($query);
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
  echo("<tr class=\"".$cl."\">\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['PROCESS']),$p['PROCESS'])."</td>\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['PROCESS']),$p['SESSION_ID'])."</td>\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['PROCESS']),$p['SESSION_SERIAL#'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['USERNAME'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['OSUSER'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),$p['STATUS'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['MACHINE'])))."</td>\n");
  echo("  <td><small>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['PROGRAM'])))."</small><br>".sprintf($link,URLEncode($p['PROCESS']),htmlentities(addslashes($p['MODULE'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),$p['LTIME'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),$p['RCG'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['PROCESS']),$p['CI'])."</td>\n");
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
