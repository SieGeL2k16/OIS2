<?php
/**
 * Extension: ShowProcesses.
 * Gives an overview about processes and sessions including top lists.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (13-Jul-2010)
 * $Id: show_processes.php,v 1.6 2010/11/27 11:20:31 siegel Exp $
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
$addHeader = <<<EOM
<script language="Javascript" type="text/javascript">
function openPopUp(pid)
  {
  var myurl = 'show_processes_details.php?PID='+pid;
  var wpid  = 'PROC'+pid;
  var mywin = window.open(myurl,wpid,'width=780, height=580,scrollbars=yes');
  }
</script>
EOM;
// Now we call the class method "PrintExtHeader()", which dumps out the complete HTML header, so you put your stuff in the <div> </div> part only:
$OIS2EXT->Add_JS_Ready_Call('$("#tabs").tabs();');
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_V_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access V$ views - aborting!');
  }
?>
<div id="tabs">
<ul>
  <li><a href="show_processes_overview.php" title="Process Overview"><span>Overview</span></a></li>
  <li><a href="show_processes_top_cpu.php" title="Top 20 CPU"><span>Top 20 CPU</span></a></li>
  <li><a href="show_processes_top_wait.php" title="Top 20 Wait"><span>Top 20 Wait</span></a></li>
</ul>
</div>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
