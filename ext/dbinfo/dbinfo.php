<?php
/**
 * Extension: Database Informations.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (03-Sep-2009)
 * $Id: dbinfo.php,v 1.7 2011/05/12 15:59:46 siegel Exp $
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
<style type="text/css">
#General_informations, #Initialisation_parameter {
  padding-top   : 20px;
}

.datatable {
  border        : 1px solid #cccccc;
}

table caption {
  font-style    : italic;
  padding-left  : 1px;
}

/* The tables for the general database informations tab */

#db_info,#ctl_info,#reset_info,#core_info,#instance_info,#installed_options {
  float         : left;
  margin-right  : 30px;
  margin-bottom : 30px;
  min-width     : 150px;
  max-width     : 340px;
}

/* Tables for init parameter display */

#init_default, #init_nondefault {
  float         : left;
  width         : 440px;
  margin-right  : 40px;
}

/* Tables for memory usage */

#sga_mem, #sga_free_mem {
  margin-right  : 40px;
  float         : left;
}

/* The divs for the memory display */

#div_sga, #div_free_sga {
  float         : left;
}

/* If jpgraph pictures are rendered this style is used for them: */

.graph_sga {
  margin-top    : 16px;
  margin-right  : 10px;
  margin-bottom : 30px;
  float         : left;
}

/* Dynamic SGA components table */

#sga_dynamic, #sga_lib_sum {
  margin-right  : 30px;
  width         : 520px;
  float         : left;
  margin-bottom : 30px;
}
#sga_lib_sum {
  width         : 450px;
  float         : left;
}

#sga_shared_pool {
  float         : left;
  margin-right  : 30px;
}

/* Instance statistics */

#Instance_statistics {
  margin-top    : 20px;
}

#instance_stats_table {
  width         : 400px;
  float         : left;
  margin-right  : 40px;
}

#instance_stats_wait, #instance_stats_cursors {
  width         : 380px;
  margin-right  : 30px;
  float         : left;
  margin-bottom : 30px;
}
</style>
EOM;

// Before calling PrintExtHeader() we first include a jQuery ready() call to our header to get the tabs activated.

$OIS2EXT->Add_JS_Ready_Call('$("#tabs").tabs();');
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
  echo("<li><a href=\"dbinfo_result_cache.php\" title=\"Result Cache overview\"><span>Result Cache</span></a></li>\n");
  }
?>
</ul>
</div>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
