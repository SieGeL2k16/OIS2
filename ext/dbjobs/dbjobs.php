<?php
/**
 * Extension: Overview of Job scheduler (DBMS_JOB / DBMS_SCHEDULER).
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (07-Sep-2009)
 * $Id: dbjobs.php 10 2014-07-20 09:43:24Z siegel $
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
$OIS2EXT->PrintExtHeader($extdata['EXTENSION']);
?>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
  $( "#tabs" ).tabs({
    beforeLoad: function( event, ui ) {
      $(ui.panel).html('Loading...');
      ui.jqXHR.error(function() {
        ui.panel.html("Couldn't load this tab?");
      });
    }
  });
});
</script>
<div id="page_content">
<?php
if($OIS2EXT->Get_V_Flag() == FALSE || $OIS2EXT->Get_DBA_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access this plugin - aborting!');
  }
?>
<div id="tabs">
<ul>
  <li><a href="dbjobs_dbms_jobs.php">DBMS_JOB</a></li>
<?php
if($OIS2EXT->Get_Oracle_Version() >= 10)
  {
  echo("  <li><a href=\"dbjobs_dbms_sched.php\">SCHEDULER_JOBS</a></li>\n");
  echo("  <li><a href=\"dbjobs_dbms_sched_details.php\">SCHEDULER_JOB_DETAILS</a></li>\n");
  echo("  <li><a href=\"dbjobs_dbms_sched_programs.php\">SCHEDULER_PROGRAMS</a></li>\n");
  }
?>
</ul>
</div>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
