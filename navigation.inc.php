<?php
/**
 * Navigation of OIS2.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.03 (31-Jan-2015)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
if(!defined('NAV_OVERRIDE_FILE') || NAV_OVERRIDE_FILE == '')
  {
  $script = basename($_SERVER['SCRIPT_FILENAME']);
  }
else
  {
  $script = NAV_OVERRIDE_FILE;
  }
if($script == 'mainindex.php')
  {
  $homeselect = ' class="nav_on"';
  }
else
  {
  $homeselect = '';
  }
?>
<div id="page_navigation">
<fieldset><legend>Navigation</legend>
<ul>
  <li>&raquo;&nbsp;<a href="<?php echo(OIS_INSTALL_URL);?>/mainindex.php"<?php echo($homeselect);?>>Home</a></li>
<?php
foreach($GLOBALS['OIS_EXTENSIONS'] AS $extname => $metadata)
  {
  if($script == basename($metadata['SCRIPTNAME']))
    {
    $class = ' class="nav_on"';
    }
  else
    {
    $class = '';
    }
  printf("<li>&raquo;&nbsp;<a href=\"%s/%s\"%s>%s</a></li>\n",OIS_INSTALL_URL,$metadata['SCRIPTNAME'],$class,$metadata['MENUNAME']);
  }
?>
  <li>&raquo;&nbsp;<a href="<?php echo(OIS_INSTALL_URL);?>/logout.php">Logout</a></li>
</ul>
</fieldset>
<br>
<div style="margin-left: 20px">
Refresh:<br>
<form method="GET" id="form_refresh" action="<?php echo($_SERVER['SCRIPT_NAME']);?>">
<select name="REFRESH" id="refresh" size="1">
  <option value="0"  >No refresh</option>
  <option value="10" >10 Seconds</option>
  <option value="30" >30 Seconds</option>
  <option value="60" >60 Seconds</option>
</select>
</form>
</div>
</div>
