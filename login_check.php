<?php
/**
 * Checks login data and creates session.
 * If login fails an error is returned to the index page, else a redirect is performed
 * after session initialisation to the mainindex.php script.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (28-Mar-2009)
 * $Id: login_check.php,v 1.9 2010/07/18 22:24:40 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
require_once('inc/nocache.inc.php');

$uname    = (isset($_POST['USERNAME'])) ? htmlentities(strip_tags($_POST['USERNAME'])) : '';
$upass    = (isset($_POST['PASSWORD'])) ? htmlentities(strip_tags($_POST['PASSWORD'])) : '';
$tnsname  = (isset($_POST['DATABASE'])) ? htmlentities(strip_tags($_POST['DATABASE'])) : '';
$conntype = (isset($_POST['CONNTYPE'])) ? intval($_POST['CONNTYPE']) : 0;

if($uname == '')
  {
  header('Location:index.php?LERR=1');
  exit;
  }

// Disable auto-errorhandling and set connection retries = 0 so we can react on login errors:

$db->setErrorHandling(db_oci8::DBOF_RETURN_ALL_ERRORS);
$db->setConnectRetries(0);
$rc = $db->Connect($uname,$upass,$tnsname,0,'',$conntype);
if(!$rc)
  {
  $reason = $db->getSQLError();
  $reason['uname'] =  $uname;
  $reason['ctype']  = $conntype;
  $data = Base64_Encode(Serialize($reason));
  header('Location:index.php?LERR=2&D='.$data);
  exit;
  }

// Check if access to V$ views is possible:
$sess = $db->Query('SELECT COUNT(*) AS SESSCNT FROM V$SESSION',OCI_ASSOC,1);
if(!is_array($sess) || intval($sess['SESSCNT']) == 0)
  {
  $v_views = FALSE;
  }
else
  {
  $v_views = TRUE;
  }

// Check if access to DBA_ views is possible:
$sess = $db->Query('SELECT COUNT(*) AS UCNT FROM DBA_USERS',OCI_ASSOC,1);
if(!is_array($sess) || intval($sess['UCNT']) == 0)
  {
  $dba_views = FALSE;
  }
else
  {
  $dba_views = TRUE;
  }

// Determine the version of the connected database:

$dummy = $db->Version();
$dbver = preg_replace('/(Oracle Database )(\d{1,2})(\w{1})(.*)/','$2',$dummy);

// User is logged in, now lets build session, enter the database credentials and redirect to mainindex.php:


session_name(SESSIONNAME);
session_start();

$_SESSION['DBUSER']     = $uname;
$_SESSION['DBPASS']     = $upass;
$_SESSION['TNSNAME']    = $tnsname;
$_SESSION['LOGINTIME']  = time();
$_SESSION['V_VIEWS']    = $v_views;
$_SESSION['DBA_VIEWS']  = $dba_views;
$_SESSION['DBVERSION']  = $dbver;
$_SESSION['CONNTYPE']   = $conntype;

@session_write_close();

// We set here a cookie with selection of Username + TNSName, so index.php can use this to prefill the form.

@setCookie('OIS2_LOGIN',sprintf("%s|%s",$uname,$tnsname));

header('Location:mainindex.php');
?>
