<?php
/**
 * Extension: Displays the DBMS_SCHEDULER_JOB_DETAILS view.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (02-Oct-2011)
 * $Id: dbjobs_dbms_sched_details.php 9 2013-07-20 06:34:13Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$dbtime = $db->Query("SELECT TO_CHAR(SYSDATE,'DD-Mon-YYYY HH24:MI:SS') AS D,(SELECT COUNT(*) FROM DBA_SCHEDULER_JOB_RUN_DETAILS) AS ANZ FROM DUAL");
$jpgraph  = $OIS2EXT->Get_JPGraph_Path();
// Get the images base dir from OIS2:
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
?>
<div id="DBMS_SCHEDULER_JOB_DETAILS">
<p>Entries in DBA_SCHEDULER_JOB_RUN_DETAILS: <?php echo($SGLFUNC->FormatNumber($dbtime['ANZ']))?> row(s) | Database date &amp; time: <?php echo($dbtime['D']);?></p>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of DBMS_SCHEDULER entries">
<caption>NOTE: Only the last 2000 rows are shown!</caption>
<thead><tr>
  <th>Logdate</th>
  <th>Owner</th>
  <th>Job name</th>
  <th>Status</th>
  <th># of<br>errors</th>
  <th>Startdate</th>
  <th>Run duration</th>
  <th>Instance ID<br>(Session ID)</th>
  <th>CPU used</th>
</thead></tr>
<tbody>
<?php
$myquery=<<<EOM
SELECT i.*
  FROM (
SELECT  LOG_ID,
        TO_CHAR(LOG_DATE,'DD-Mon-YYYY HH24:MI:SS') AS LD,
        OWNER,
        JOB_NAME,
        JOB_SUBNAME,
        STATUS,
        ERROR#,
        TO_CHAR(REQ_START_DATE,'DD-Mon-YYYY HH24:MI:SS') AS RSD,
        TO_CHAR(ACTUAL_START_DATE,'DD-Mon-YYYY HH24:MI:SS') AS ASD,
        TO_CHAR(RUN_DURATION) AS RD,
        INSTANCE_ID,
        SESSION_ID,
        SLAVE_PID,
        TO_CHAR(CPU_USED) AS CU,
        ADDITIONAL_INFO
  FROM  DBA_SCHEDULER_JOB_RUN_DETAILS
  ORDER BY LOG_DATE DESC
 ) i
WHERE ROWNUM <= 2000
EOM;
$db->QueryResult($myquery);
$lv = 0;
$yesno = array( 'TRUE'   => '<img src="'.$OIS_IMG.'tick.png" alt="Yes" border="0" title="%s">',
                'FALSE'  => '<img src="'.$OIS_IMG.'cross.png" alt="No" border="0" title="%s">'
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
  echo("<tr class=\"".$myback."\" valign=\"top\" title=\"".$d['ADDITIONAL_INFO']."\">\n");
  echo("  <td title=\"Log date\">".$d['LD']."</td>\n");
  echo("  <td title=\"Owner\">".$d['OWNER']."</td>\n");
  echo("  <td title=\"Job name\">".$d['JOB_NAME']."</td>\n");
  echo("  <td title=\"Status\">".$d['STATUS']."</td>\n");
  echo("  <td title=\"Errors\" align=\"center\">".$d['ERROR#']."</td>\n");
  echo("  <td title=\"Actual start date (Requested start date: ".$d['RSD'].")\">".$d['ASD']."</td>\n");
  echo("  <td title=\"Run duration\">".$d['RD']."</td>\n");
  echo("  <td title=\"Instance ID (SessionID)\">".$d['INSTANCE_ID']." (".$d['SESSION_ID'].")</td>\n");
  echo("  <td title=\"CPU used\">".$d['CU']."</td>\n");
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
