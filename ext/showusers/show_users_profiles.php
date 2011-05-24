<?php
/**
 * Extension: Show Users.
 * Displays all profiles defined in the database.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (15-Aug-2010)
 * $Id: show_users_profiles.php,v 1.1 2010/08/15 21:19:21 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="show_users_profiles">
<?php
// Read out the profiles and display on screen:
$t1 = array
 (
 'THEAD'        => array('Profile','Connection time limit','Sessions per user','Failed login attempts'),
 'CAPTION'      => 'List of available profiles'
 );
$sp = NULL;
$query= <<<EOM
SELECT DISTINCT p.PROFILE,
       (SELECT i.LIMIT FROM DBA_PROFILES i WHERE i.RESOURCE_NAME='CONNECT_TIME' AND i.PROFILE=p.PROFILE) AS CONN_TIME,
       (SELECT i.LIMIT FROM DBA_PROFILES i WHERE i.RESOURCE_NAME='SESSIONS_PER_USER' AND i.PROFILE=p.PROFILE) AS SESS_USERS,
       (SELECT i.LIMIT FROM DBA_PROFILES i WHERE i.RESOURCE_NAME='FAILED_LOGIN_ATTEMPTS' AND i.PROFILE=p.PROFILE) AS FAILED_PW
  FROM DBA_PROFILES p
 ORDER BY p.PROFILE
EOM;

$OIS2EXT->RenderQuery($query,$sp,$t1);
?>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtTabFooter();
?>
