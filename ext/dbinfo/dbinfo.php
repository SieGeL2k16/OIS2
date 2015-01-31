<?php
/**
 * Extension: Database Informations.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.02 (14-Jul-2014)
 * $Id: dbinfo.php 10 2014-07-20 09:43:24Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$addHeader = sprintf("<link type=\"text/css\" href=\"%s\" rel=\"stylesheet\" />","dbinfo.css");

// Before calling PrintExtHeader() we first include a jQuery ready() call to our header to get the tabs activated.
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
?>
<div id="tabs">
<ul>
  <li><a href="dbinfo_general.php" title="General informations"><span>General informations</span></a></li>
  <li><a href="dbinfo_init_parms.php" title="Initialisation parameter"><span>Initialisation parameter</span></a></li>
  <li><a href="dbinfo_sga.php" title="SGA usage"><span>SGA usage</span></a></li>
<?php
if($OIS2EXT->Get_Oracle_Version() >= 9)
  {
  echo("<li><a href=\"dbinfo_pga.php\" title=\"PGA usage\"><span>PGA usage</span></a></li>\n");
  }
?>
  <li><a href="dbinfo_instance_stats.php" title="Instance statistics"><span>Instance statistics</span></a></li>
  <li><a href="dbinfo_control_file.php" title="Control file"><span>Control file</span></a></li>
<?php
if($OIS2EXT->Get_Oracle_Version() >= 11)
  {
  echo("<li><a href=\"dbinfo_result_cache.php?".strip_tags($_SERVER['QUERY_STRING'])."\" title=\"Result Cache overview\"><span>Result Cache</span></a></li>\n");
  }
if($OIS2EXT->Get_Oracle_Version() >= 10)
  {
  echo("<li><a href=\"dbinfo_used_features.php\" title=\"Used features\"><span>Used Features</span></a></li>\n");
  }
?>
  <li><a href="dbinfo_registry.php" title="Display Registry"><span>Registry</span></a></li>
</ul>
</div>
</div>
<?php
// Call this method to dump out the footer and disconnect from database
$OIS2EXT->PrintExtFooter();
?>
