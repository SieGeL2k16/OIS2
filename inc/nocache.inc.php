<?php
/**
 * General header, includes all other php files.
 * @package OIS2
 * @subpackage Includes
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (30-May-2009)
 * $Id$
 * @filesource
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
$SGLFUNC = new sgl_functions();
require_once('functions.inc.php');
require_once('db_oci8.class.php');
if(isset($_SERVER['REQUEST_TIME']) && intval($_SERVER['REQUEST_TIME']) > 0)
  {
  $start_time = $_SERVER['REQUEST_TIME'];    // PHP5 sets this
  }
else
  {
  $start_time = $SGLFUNC->getmicrotime();              // Not available, determine now
  }
$db = new db_oci8;
/** Load in the configuration and make sure that at least one database is configured: */
require_once('config.inc.php');
if(defined('UI_THEME') == FALSE)
  {
  die("ERROR: config.inc.php is not correctly configured! Please check your config!");
  }
if(!count($OIS_DATABASES))
  {
  Error('ERROR: No databases configured! Please add at least one TNS Name to the configuration!','',TRUE);
  exit;
  }
?>
