<?php
/**
 * Extension: Informations about Flashback and Recycle Bin.
 * Works only for Oracle Versions >= 10g !
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (18-Jul-2010)
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
if($OIS2EXT->Get_Oracle_Version() < 10)
  {
  $OIS2EXT->ErrorExit("Flashback / Recycle Bin informations are only available for Oracle Versions >= 10g!");
  }
?>
<div id="tabs">
<ul>
  <li><a href="flashback_overview.php"><span>Flashback overview</span></a></li>
  <li><a href="flashback_recyclebin.php"><span>Recycle Bin</span></a></li>
</ul>
</div>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
