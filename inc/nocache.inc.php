<?php
/**
 * General header, includes all other php files.
 * @package OIS2\Includes
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.03 (31-Jan-2015)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/** Make sure that we get noticed about EVERYTHING problematic */
ini_set('error_reporting', E_ALL|E_NOTICE|E_STRICT);

/* Set the default timezone to avoid warnings about wrong timezones: */
date_default_timezone_set('Europe/Berlin');

if(!defined('NO_CONTENT_TYPE') || NO_CONTENT_TYPE != 1)
  {
  if(!headers_sent())
    {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Cache-Control: private");
    }
  }
require_once('defines.inc.php');
require_once('sgl_functions.class.php');
$oldloc = setLocale(LC_ALL,'en_US');
$SGLFUNC = new sgl_functions();
require_once('functions.inc.php');
require_once('db_oci8.class.php');
if(isset($_SERVER['REQUEST_TIME']) && intval($_SERVER['REQUEST_TIME']) > 0)
  {
  $start_time = $_SERVER['REQUEST_TIME'];    // PHP5 sets this
  }
else
  {
  $start_time = $SGLFUNC->getmicrotime();    // Not available, determine now
  }
$db = new db_oci8;
/** Load in the configuration and make sure that at least one database is configured: */
require_once('config.inc.php');
require_once('tnsparser.class.php');
if(defined('UI_THEME') == FALSE)
  {
  die("ERROR: config.inc.php is not correctly configured! Please check your config! [Missing \"UI_THEME\" define!]");
  }

// V2.02: Make sure that logfile is writeable (if configured)
if(defined('OIS_LOGFILE') == TRUE && OIS_LOGFILE != '')
  {
  if(@file_exists(OIS_LOGFILE) === FALSE)
    {
    if(@touch(OIS_LOGFILE) === FALSE)
      {
      die("ERROR: \"OIS_LOGFILE\" specifies a logfile which OIS2 cannot write to - please check permissions!");
      }
    }
  else
    {
    if(@is_writable(OIS_LOGFILE) === FALSE)
      {
      die("ERROR: Unable to write to existing logfile as defined by \"OIS_LOGFILE\" - Please check permissions!");
      }
    }
  }

/*
 * V2.02: If no TNS names are configured, try to load in tnsnames.ora from either $ORACLE_HOME (Server/client install)
 *        or from $TNS_ADMIN (Instantclient etc.), complain if none of these environment variables are set.
 */
if(empty($OIS_DATABASES) === TRUE)
  {
  $OHOME = getenv('ORACLE_HOME');
  if($OHOME != '')
    {
    $TNSFILE = $SGLFUNC->MergePath($OHOME,'network/admin/tnsnames.ora');
    }
  else
    {
    $TNSHOME = getenv('TNS_ADMIN');
    if($TNSHOME == '')
      {
      Error('NO Databases defined and also neither $ORACLE_HOME nor $TNS_ADMIN is not set?!','',TRUE);
      exit;
      }
    $TNSFILE = $SGLFUNC->MergePath($TNSHOME,'tnsnames.ora');
    }
  try
    {
    $TNS    = new TNSParser();
    $dummy  = $TNS->ParseTNS($TNSFILE);
    foreach($dummy AS $TNAME => $TDATA)
      {
      $OIS_DATABASES[]=$TNAME;
      }
    }
  catch(Exception $e)
    {
    Error('TNS could not be read: '.$e->getmessage(),'',TRUE);
    exit;
    }
  }
else
  {
  sort($OIS_DATABASES);
  }

/* V2.03: Check if OIS_INSTALL_PATH is set. If not, we try to build that on our own */
if(defined('OIS_INSTALL_PATH') === FALSE || OIS_INSTALL_PATH == '')
  {
  define('OIS_INSTALL_PATH', preg_replace("/(.*)(\/inc)$/","$1",dirname(__FILE__)));
  }

/* V2.03: Check if OIS_INSTALL_URL is set. If not, we try to build that on our own */



?>
