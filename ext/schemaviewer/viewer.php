<?php
/**
 * Extension: Schema Viewer.
 * Displays contents of all schemata found inside the database.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (24-Dec-2010)
 * $Id: viewer.php,v 1.3 2010/12/26 10:38:03 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$addHeader=<<<EOM
<script language="Javascript" type="text/javascript" src="schemaviewer.js"></script>
<style type="text/css">
#object_list {
  margin-top: 20px;
}
</style>
EOM;
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_DBA_Flag() == FALSE || $OIS2EXT->Get_V_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access use this plugin - aborting!');
  }
$sp = array('n' => 'DBMS_METADATA');
// Make sure that we have the DBMS_METADATA package available:
$stats = $db->QueryHash("SELECT COUNT(*) AS CNT FROM ALL_OBJECTS WHERE OBJECT_NAME=:n",OCI_ASSOC,0,$sp);
?>
<form method="POST" action="viewer.php">
<input type="hidden" name="HAS_METADATA" id="has_metadata" value="<?php echo($stats['CNT']);?>">
<label for="schema">Schema:</label>
<select name="SCHEMA" id="schema" size="1">
  <option value="">Available Schema</option>
</select>
<label for="objects">Objects:</label>
<select name="OBJECTS" id="objects" size="1">
  <option value="">Schema objects</option>

</select>
</form>
<div id="object_list">



</div>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
