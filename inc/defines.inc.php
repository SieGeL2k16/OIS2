<?php
/**
 * All defines are stored here.
 * @package OIS2
 * @subpackage Includes
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (30-May-2009)
 * $Id: defines.inc.php,v 1.6 2011/05/12 16:11:31 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Version of this site.
 */
define('SITE_VERSION'       , '2.01');

/**
 * This define is used to set the maximum possible INTEGER value when binding number values to/from Oracle -> PHP.
 */
define('INTEGER_MAX',	99999999999999999999999999999999999999);

/**
 * Defines the main title of this website.
 */
define('SITE_TITLE' ,'Oracle Information Site '.SITE_VERSION);

/**
 * The session name.
 */
define('SESSIONNAME' ,  'OIS2');
?>
