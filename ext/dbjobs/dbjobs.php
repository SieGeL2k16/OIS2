<?php
/**
 * Extension: Overview of Job scheduler (DBMS_JOB / DBMS_SCHEDULER).
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
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$OIS_URL = OIS_INSTALL_URL;

// Before calling PrintExtHeader() we first include a jQuery ready() call to our header to get the tabs activated.

$OIS2EXT->Add_JS_Ready_Call('$("#tabs").tabs();');
$OIS2EXT->PrintExtHeader($extdata['EXTENSION']);
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_V_Flag() == FALSE || $OIS2EXT->Get_DBA_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access this plugin - aborting!');
  }
?>
<div id="tabs">
<ul>
  <li><a href="dbjobs_dbms_jobs.php" title="DBMS_JOB"><span>DBMS_JOB</span></a></li>
<?php
if($OIS2EXT->Get_Oracle_Version() >= 10)
  {
  echo("<li><a href=\"dbjobs_dbms_sched.php\" title=\"DBMS_SCHEDULER_JOBS\"><span>SCHEDULER_JOBS</span></a></li>\n");
  echo("<li><a href=\"dbjobs_dbms_sched_details.php\" title=\"DBMS_SCHEDULER_JOB_DETAILS\"><span>SCHEDULER_JOB_DETAILS</span></a></li>\n");
  echo("<li><a href=\"dbjobs_dbms_sched_programs.php\" title=\"DBMS_SCHEDULER_PRG\"><span>SCHEDULER_PROGRAMS</span></a></li>\n");
  }
?>
</ul>
</div>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
