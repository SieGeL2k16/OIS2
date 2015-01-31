<?php
/**
 * Extension: Informations about Flashback and Recycle Bin.
 * Works only for Oracle Versions >= 10g !
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (19-Jul-2011)
 * $Id: flashback.php 10 2014-07-20 09:43:24Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$addHeader = "<link type=\"text/css\" href=\"flashback.css\" rel=\"stylesheet\" />";
$loadtabs=<<<EOM
\$("#tabs").tabs({beforeLoad: function( event, ui ) { \$(ui.panel).html('Loading...'); }});
EOM;
$OIS2EXT->Add_JS_Ready_Call($loadtabs);
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_V_Flag() == FALSE || $OIS2EXT->Get_DBA_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access this plugin - aborting!');
  }
if($OIS2EXT->Get_Oracle_Version() < 10)
  {
  $OIS2EXT->ErrorExit("Flashback / Recycle Bin informations are only available for Oracle Versions >= 10g!");
  }
?>
<div id="tabs">
<ul>
  <li><a href="flashback_overview.php" title="Flashback overview"><span>Flashback overview</span></a></li>
  <li><a href="flashback_recyclebin.php" title="Recycle Bin"><span>Recycle Bin</span></a></li>
</ul>
</div>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
