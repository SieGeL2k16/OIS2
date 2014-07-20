<?php
/**
 * Configuration file for OIS 2.
 * Make sure to adapt this file to match your environment!
 * @package OIS2
 * @subpackage Includes
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.02 (17-Jul-2014)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Enter here all TNSNames for the databases you want to access with OIS2.
 * Put every entry in single quotes and separate multiple entries with a comma.
 * Since v2.02 you can leave this array empty, in this case OIS2 reads the TNS
 * file and uses all TNSNames from this file.
 */
$OIS_DATABASES = array ();

/**
 * Specify here the absolute pathname where OIS2 is installed.
 * This is *NOT* the URL from your Webserver! This is specified in the next define!
 */
define('OIS_INSTALL_PATH' , '/html/private/OIS2');

/**
 * Specify here the URL to open OIS2 in your Webbrowser.
 */
define('OIS_INSTALL_URL' , '/OIS2');

/**
 * If you have the JPGraph classes installed, enter here the full path to the root directory of the JPGraph installation.
 * For more informations about JPGraph please visit http://www.aditus.nu/jpgraph/
 */
define('JPGRAPH_PATH'   , '/html/private/jpgraph3/src');

/**
 * Name of the JQuery.UI Theme in use.
 * Remember to copy your themes under the css/ directory of OIS2 base directory!
 */
define('UI_THEME', 'smoothness');
?>
