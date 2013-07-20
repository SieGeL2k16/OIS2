<?php
/**
 * Extension: Schema viewer.
 * Displays details for a given object/type/user combination.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (26-Dec-2010)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');

$user   = (isset($_GET['UN'])==TRUE) ? strip_tags($_GET['UN']) : '';
$object = (isset($_GET['ON'])==TRUE) ? strip_tags($_GET['ON']) : '';
$type   = (isset($_GET['OT'])==TRUE) ? strip_tags($_GET['OT']) : '';

// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'viewer.php');
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$OIS2EXT->PrintExtHeader('Show DDL for object &quot;'.$user.'.'.$object.'&quot;','',TRUE);
?>
<div id="fullpage_content">
<?php
if($user == '' || $object == '' || $type == '')
  {
  Error("One or more required parameter are empty - aborting!");
  $db->Disconnect();
  exit;
  }
$query = 'SELECT DBMS_METADATA.GET_DDL(:type,:object,:schema) AS DDL FROM DUAL';
if($type == 'PACKAGE BODY') $type = 'PACKAGE';
$sp = array('type' => $type, 'object' => $object, 'schema' => $user);
$ddl = $db->QueryHash($query,OCI_ASSOC,0,$sp);
echo("<pre>".$ddl['DDL']."</pre>");
?>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter('<div align="center"><a href="javascript:self.close()" class="btn">Close window</a></div>');
?>
