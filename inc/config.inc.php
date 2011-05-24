<?php
/**
 * Configuration file for OIS 2.
 * Make sure to adapt this file to match your environment!
 * @package OIS2
 * @subpackage Includes
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (21-Mar-2009)
 * $Id: config.inc.php,v 1.8 2011/05/12 16:11:31 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Enter here all TNSNames for the databases you want to access with OIS2.
 * Put every entry in single quotes and separate multiple entries with a comma.
 */
$OIS_DATABASES = array (
  'EBOLA',
  'SGLMOBIL'
);

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
 * Name of the JQuery.UI Theme in use.
 * Remember to copy your themes under the css/ directory of OIS2 base directory!
 */
define('UI_THEME', 'smoothness/jquery-ui-1.8.11.custom.css');

/**
 * If you have the JPGraph classes installed, enter here the full path to the root directory of the JPGraph installation.
 * For more informations about JPGraph please visit http://jpgraph.net/
 */
define('JPGRAPH_PATH'   , '/html/private/jpgraph3/src');

?>
