<?php
/**
 * Extension: Show Users.
 * Displays all roles defined in the database.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (15-Aug-2010)
 * $Id: show_users_roles.php 2 2011-06-30 18:10:40Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="show_users_roles">
<?php
// Read out the object privileges for selected user and display on screen:
$t1 = array
 (
 'THEAD'        => array('Role','Password required'),
 'CAPTION'      => 'List of available roles'
 );
$sp = NULL;
$OIS2EXT->RenderQuery("SELECT ROLE,PASSWORD_REQUIRED FROM DBA_ROLES ORDER BY ROLE",$sp,$t1);
?>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtTabFooter();
?>
