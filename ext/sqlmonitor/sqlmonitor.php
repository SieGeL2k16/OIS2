<?php
/**
 * Extension: SQL Monitor.
 * Lists all currently running SQL statements together with statistics on screen.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 0.1 (21-Jul-2014)
 * $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$OIS_URL = OIS_INSTALL_URL;
// Now we call the class method "PrintExtHeader()", which dumps out the complete HTML header, so you put your stuff in the <div> </div> part only:
$loadtabs=<<<EOM
\$("#tabs").tabs({beforeLoad: function( event, ui ) { \$(ui.panel).html('Loading...'); }});
EOM;
$OIS2EXT->Add_JS_Ready_Call($loadtabs);
$addHeader = '';
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
  <li><a href="sqlmonitor_running_sql.php?<?php echo(Str_Replace("&","&amp;",$_SERVER['QUERY_STRING']));?>" title="Running SQL"><span>Running SQL</span></a></li>
</ul>
</div>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
