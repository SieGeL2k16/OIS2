<?php
/**
 * Extension: Displays the DBMS_SCHEDULER_PROGRAMS view.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (02-Oct-2011)
 * $Id: dbjobs_dbms_sched_programs.php 9 2013-07-20 06:34:13Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$dbtime = $db->Query("SELECT TO_CHAR(SYSDATE,'DD-Mon-YYYY HH24:MI:SS') AS D,(SELECT COUNT(*) FROM DBA_SCHEDULER_PROGRAMS) AS ANZ FROM DUAL");
$jpgraph  = $OIS2EXT->Get_JPGraph_Path();
// Get the images base dir from OIS2:
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
?>
<div id="DBMS_SCHEDULER_PRG">
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of DBMS_SCHEDULER entries">
<caption>Contents from DBMS_SCHEDULER_PROGRAMS: <?php echo($SGLFUNC->FormatNumber($dbtime['ANZ']))?> program(s) defined | Database date &amp; time: <?php echo($dbtime['D']);?></caption>
<thead><tr>
  <th>Owner</th>
  <th>Name</th>
  <th>Type</th>
  <th>Action</th>
  <th># of Args</th>
  <th>Detached</th>
  <th>Enabled</th>
</thead></tr>
<tbody>
<?php
$myquery=<<<EOM
SELECT i.*
  FROM
  (
  SELECT  OWNER,
          PROGRAM_NAME,
          PROGRAM_TYPE,
          PROGRAM_ACTION,
          NUMBER_OF_ARGUMENTS,
          ENABLED,
          DETACHED,
          COMMENTS
    FROM  DBA_SCHEDULER_PROGRAMS
    ORDER BY OWNER,PROGRAM_NAME
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
  echo("<tr class=\"".$myback."\" valign=\"top\" title=\"".$d['COMMENTS']."\">\n");
  echo("  <td>".$d['OWNER']."</td>\n");
  echo("  <td>".$d['PROGRAM_NAME']."</td>\n");
  echo("  <td>".$d['PROGRAM_TYPE']."</td>\n");
  echo("  <td>".$d['PROGRAM_ACTION']."</td>\n");
  echo("  <td align=\"center\">".$d['NUMBER_OF_ARGUMENTS']."</td>\n");
  echo("  <td align=\"center\">".sprintf($yesno[$d['DETACHED']],$d['DETACHED'])."</td>\n");
  echo("  <td align=\"center\">".sprintf($yesno[$d['ENABLED']],$d['ENABLED'])."</td>\n");
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
