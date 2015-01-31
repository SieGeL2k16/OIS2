<?php
/**
 * AJAX backend for Show Processes -> Kill session.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.03 (21-Jul-2014)
 * $Id: killsession.php 9 2013-07-20 06:34:13Z siegel $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$retdata = array();
$retdata['sid'] = $_REQUEST['SID'];
$retdata['ser'] = $_REQUEST['SERIAL'];

$KILL_SESSION = sprintf("ALTER SYSTEM KILL SESSION '%s,%s'",$retdata['sid'],$retdata['ser']);
$rc = $db->Query($KILL_SESSION,OCI_ASSOC,1);

if(is_array($rc) === FALSE)
  {
  $oerr = $db->GetSQLError();
  echo(json_encode(array('ERROR' => $oerr['msg'])));
  }
else
  {
  echo(json_encode(array('OK' => 1)));
  }
$db->Disconnect();
WriteLog(sprintf("KILLSESSION: \"%s@%s\" killed session \"%s,%s\"",$_SESSION['DBUSER'],$_SESSION['TNSNAME'],$retdata['sid'],$retdata['ser']));
?>
