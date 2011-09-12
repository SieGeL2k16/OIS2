<?php
/**
 * Extension: Schema viewer.
 * Displays an overview of ALL objects for a given schema including the size for all of the objects found.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (20-Jul-2011)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'viewer.php');
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$schema = $SGLFUNC->GetRequestParam('SCHEMA');
$OIS2EXT->PrintExtHeader('Show schema overview for &quot;'.$schema.'&quot;','',TRUE);
?>
<div id="fullpage_content">


</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter('<div align="center"><a href="javascript:self.close()" class="btn">Close window</a></div>');
?>
