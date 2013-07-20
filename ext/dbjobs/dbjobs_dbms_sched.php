<?php
/**
 * Extension: Overview of Job scheduler (DBMS_JOB / DBMS_SCHEDULER).
 * This displays the DBMS_SCHEDULER entries.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.02 (30-May-2012)
 * $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$dbtime = $db->Query("SELECT TO_CHAR(SYSDATE,'DD-Mon-YYYY HH24:MI:SS') AS D,(SELECT COUNT(*) FROM DBA_SCHEDULER_JOBS) AS ANZ FROM DUAL");
$jpgraph  = $OIS2EXT->Get_JPGraph_Path();
// Get the images base dir from OIS2:
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
?>
<script language="javascript" type="text/javascript" src="scheduler.js"></script>
<div id="DBMS_SCHEDULER_JOBS">
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of DBMS_SCHEDULER entries">
<caption>Contents from DBA_SCHEDULER_JOBS: <?php echo($SGLFUNC->FormatNumber($dbtime['ANZ']))?> Job(s) defined | Database date &amp; time: <?php echo($dbtime['D']);?></caption>
<thead><tr>
  <th>Owner</th>
  <th>Job name</th>
  <th>Job action</th>
  <th>Start date</th>
  <th>Repeat interval</th>
  <th>Next run date</th>
  <th>Enabled</th>
</thead></tr>
<tbody>
<?php
$myquery=<<<EOM
SELECT  OWNER,
        JOB_NAME,
        JOB_ACTION,
        TO_CHAR(START_DATE,'DD-Mon-YYYY HH24:MI:SS') AS SD,
        REPEAT_INTERVAL,
        TO_CHAR(NEXT_RUN_DATE,'DD-Mon-YYYY HH24:MI:SS') AS NRD,
        COMMENTS,
        ENABLED
  FROM  DBA_SCHEDULER_JOBS
EOM;
$db->QueryResult($myquery);
$lv = 0;
$yesno = array( 'TRUE'   => '<img src="'.$OIS_IMG.'tick.png" alt="%s.%s" border="0" title="Click to disable" class="JOB_STATE">',
                'FALSE'  => '<img src="'.$OIS_IMG.'cross.png" alt="%s.%s" border="0" title="Click to enable" class="JOB_STATE">'
              );
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
  echo("<tr class=\"".$myback."\" valign=\"top\" title=\"".$d['COMMENTS']."\">\n");
  echo("  <td>".$d['OWNER']."</td>\n");
  echo("  <td>".$d['JOB_NAME']."</td>\n");
  echo("  <td><small>".$d['JOB_ACTION']."</small></td>\n");
  echo("  <td>".$d['SD']."</td>\n");
  echo("  <td>".$d['REPEAT_INTERVAL']."</td>\n");
  echo("  <td>".$d['NRD']."</td>\n");
  echo("  <td align=\"center\">".sprintf($yesno[$d['ENABLED']],$d['OWNER'],$d['JOB_NAME'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

</div>
<?php
$db->Disconnect();
?>
