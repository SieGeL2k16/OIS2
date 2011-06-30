<?php
/**
 * Extension: Overview of Job scheduler (DBMS_JOB / DBMS_SCHEDULER).
 * This displays the DBMS_JOB entries.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (07-Sep-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$dbtime = $db->Query("SELECT TO_CHAR(SYSDATE,'DD-Mon-YYYY HH24:MI:SS') AS D,(SELECT COUNT(*) FROM DBA_JOBS) AS ANZ FROM DUAL");
?>
<div id="div_dbms_jobs">
<table cellspacing="1" cellpadding="2" border="0" class="datatable" summary="Lists all defined database jobs from DBMS_JOBS">
<caption><?php echo($SGLFUNC->FormatNumber($dbtime['ANZ']))?> Job(s) defined | Database date &amp; time: <?php echo($dbtime['D']);?></caption>
<thead><tr>
  <th>#</th>
  <th>Schema</th>
  <th>Execution<br>Dates</th>
  <th>Invalid<br>Errors</th>
  <th>Interval</th>
  <th nowrap>Command</th>
</tr></thead>
<tbody>
<?php
$myquery = <<<EOM
SELECT JOB,
       LOG_USER,
       PRIV_USER,
       SCHEMA_USER,
       TO_CHAR(LAST_DATE,'DD-Mon-YYYY') AS LD,
       LAST_SEC,
       TO_CHAR(THIS_DATE,'DD-Mon-YYYY') AS TD,
       THIS_SEC,
       TO_CHAR(NEXT_DATE,'DD-Mon-YYYY') AS ND,
       NEXT_SEC,
       BROKEN,
       INTERVAL,
       FAILURES,
       WHAT,
       INSTANCE
FROM DBA_JOBS
ORDER BY JOB
EOM;
$db->QueryResult($myquery);
$lv=0;
while($j=$db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }

  if($j['BROKEN']=='N')
    {
    $fontcolor="SMALL";
    }
  else
    {
    $fontcolor="REDSMALL";
    }
  echo("<tr class=\"".$myback."\" valign=\"top\">\n");
  echo("  <td align=\"center\"><span class=\"SMALL\">".$j['JOB']."</span></td>\n");
  echo("  <td align=\"left\"><span class=\"SMALL\">".$j['SCHEMA_USER']."</span></td>\n");
  echo("  <td align=\"right\">\n");
  echo("  <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" summary=\"\">\n");
  echo("  <tr>\n");
  $last = $j['LD']." ".$j['LAST_SEC'];
  if($last==" ") $last = "---";
  echo("    <td align=\"right\" nowrap><span class=\"".$fontcolor."\">Last:</span></td>\n");
  echo("    <td align=\"right\" nowrap><span class=\"".$fontcolor."\">".$last."</span></td>\n");
  echo("  </tr>\n");
  $current = $j['TD']." ".$j['THIS_SEC'];
  if($current==" ") $current = "---";
  echo("  <tr>\n");
  echo("    <td align=\"right\" nowrap><span class=\"".$fontcolor."\">Current:</span></td>\n");
  echo("    <td align=\"right\" nowrap><span class=\"".$fontcolor."\">".$current."</span></td>\n");
  echo("  </tr>\n");
  $next = $j['ND']." ".$j['NEXT_SEC'];
  if($next==" ") $next = "---";
  echo("  <tr>\n");
  echo("    <td align=\"right\" nowrap><span class=\"".$fontcolor."\">Next:</span></td>\n");
  echo("    <td align=\"right\" nowrap><span class=\"".$fontcolor."\">".$next."</span></td>\n");
  echo("  </tr>\n");
  echo("  </table>\n");
  echo("  </td>\n");
  echo("  <td align=\"left\"><span class=\"".$fontcolor."\">".$j['BROKEN']."&nbsp;/&nbsp;".$j['FAILURES']."</span></td>\n");
  echo("  <td align=\"left\"><span class=\"".$fontcolor."\">".$j['INTERVAL']."</span></td>\n");
  echo("  <td align=\"left\"><span class=\"".$fontcolor."\">".$j['WHAT']."</span></td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
$db->Disconnect();
?>
</tbody>
</table>
</div>
<?php
$db->Disconnect();
?>
