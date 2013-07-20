<?php
/**
 * AJAX backend for Show Processes -> Kill session.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (22-May-2013)
 * $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$retdata = array();
$retdata['sid'] = $_REQUEST['SID'];
$retdata['ser'] = $_REQUEST['SERIAL'];

$KILL_SESSION = sprintf("ALTER SYSTEM KILL SESSION '%s,%s'",$retdata['sid'],$retdata['ser']);
$rc = $db->Query($KILL_SESSION,OCI_ASSOC,1);
$db->Disconnect();
if(is_array($rc) === FALSE) $retdata = $rc;
echo(json_encode($retdata));
?>
