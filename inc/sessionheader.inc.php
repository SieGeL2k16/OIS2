<?php
/**
 * Session include file.
 * Initializes session and loads in the global nocache.inc.php file afterwards.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (29-Mar-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
require_once('defines.inc.php');
require_once('config.inc.php');
@session_name(SESSIONNAME);
@session_start();
if(isset($_SESSION['DBUSER']) == false || $_SESSION['DBUSER'] == '')
  {
  header('Location:'.OIS_INSTALL_URL.'/index.php');
  exit;
  }

// Session is initialized, now load in the nocache.inc.php script to have everything in place:
require_once('nocache.inc.php');

// And perform the connection to the database:
$db->Connect($_SESSION['DBUSER'],$_SESSION['DBPASS'],$_SESSION['TNSNAME'],1,'',$_SESSION['CONNTYPE']);

/*
 * Now we load all plugins from our ext/ directory.
 * Plugins are always stored in own subdirectory.
 * Per convention every plugin must have a directory and a file
 * with the same name as the according subdirectory with extension ".ext".
 * See README.extensions for further details on how to include own plugins.
 * Note that the OIS2_INSTALL_PATH define is set in the config.inc.php script!
 */
$OIS_EXT_PATH = OIS_INSTALL_PATH.'/ext';
if(is_dir($OIS_EXT_PATH) == FALSE)
  {
  InformUser('ERROR: NO EXTENSION DIRECTORY FOUND - CHECK YOUR INSTALLATION!!!',OIS_INSTALL_URL.'/mainindex.php');
  $db->Disconnect();
  exit;
  }
// All extensions are stored in this array:
$OIS_EXTENSIONS = array();

$dirhandle = @opendir($OIS_EXT_PATH);
if($dirhandle == FALSE)
  {
  InformUser('ERROR: EXTENSION DIRECTORY CANNOT BE READ ?!',OIS_INSTALL_URL.'/mainindex.php');
  $db->Disconnect();
  exit;
  }
while (false !== ($file = readdir($dirhandle)))
  {
  if($file == '.' || $file == '..') continue;
  $fullpath = $OIS_EXT_PATH.'/'.$file;
  if(is_dir($fullpath) == TRUE)
    {
    $metafile = $fullpath.'.ext';
    if(@file_exists($metafile))
      {
      $OIS_EXTENSIONS[$file]=ReadExtInfo($file,$metafile);
      continue;
      }
    }
  }
@closedir($dirhandle);
usort($OIS_EXTENSIONS,'sort_plugins');

// If we where called from an extension, load in the extension support class and instantiate it:

if(defined('IS_EXTENSION') && IS_EXTENSION == 1)
  {
  require_once('ois2_extension_class.php');
  $OIS2EXT = new OIS2_extension($db);
  }
?>
