<?php
/**
 * Login page for OIS2.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.03 (31-Jan-2015)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
require_once('inc/nocache.inc.php');
$sysd = checkSystemData();
$lerr = (isset($_GET['LERR'])) ? intval($_GET['LERR']) : 0;
$un   = '';
if($lerr)
  {
  $errstr = '';
  switch($lerr)
    {
    case  1:
              $errstr = 'Empty username given?';
              break;
    case  2:
              $dummy  = Unserialize(Base64_Decode(strip_tags($_GET['D'])));
              $errstr = $dummy['msg'];
              $un     = $dummy['uname'];
              break;
    default:
              $errstr = 'Unknown Error '.$lerr.' - please inform the author!';
    }
  }

if(isset($_COOKIE['OIS2_LOGIN']) && $_COOKIE['OIS2_LOGIN'] != '')
  {
  $dummy = explode('|',$_COOKIE['OIS2_LOGIN']);
  $uname = $dummy[0];
  $tname = $dummy[1];
  }
else
  {
  $uname = '';
  $tname = '';
  }
if($un == '')
  {
  $un = $uname;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo(SITE_TITLE);?>: Login page</title>
<?php
require_once('inc/metatags.inc.php');
?>
<script>
$(document).ready(function() {
  $("input:submit").button();
});
</script>
</head>
<body>
<?php
if(function_exists('json_encode')==FALSE)
  {
  Error('ERROR: YOUR PHP HAS NO SUPPORT FOR "JSON" - PLEASE RECOMPILE/REINSTALL!');
  $db->disconnect();
  exit;
  }
?>
<div id="login_mask">
<h1>Welcome to <?php echo(SITE_TITLE);?></h1>
<small>Written 2009-2021 by <a href="https://www.saschapfalz.de/downloads_readme.php?FID=59&CAT=0" target="_blank" title="Click to visit my homepage to get new updates">Sascha 'SieGeL' Pfalz</a></small><br>
<br>
<form method="post" action="login_check.php" id="login_form">
<table class="tborder">
<caption>Please enter your login data:</caption>
<tr>
  <td><label for="username">Username:</label></td>
  <td><input type="text" name="USERNAME" id="username" value="<?php echo($un);?>" size="32" maxlength="32" required="required" autofocus="autofocus"></td>
</tr>
<tr>
  <td><label for="password">Password:</label></td>
  <td><input type="password" name="PASSWORD" id="password" value="" size="32" maxlength="100" required="required"></td>
</tr>
<tr>
  <td><label for="database">Database:</label></td>
  <td><select name="DATABASE" id="database" size="1">
<?php
for($i = 0; $i < count($OIS_DATABASES); $i++)
  {
  if($OIS_DATABASES[$i] == $tname)
    {
    $mysel = ' SELECTED';
    }
  else
    {
    $mysel = '';
    }
  echo("  <option value=\"".$OIS_DATABASES[$i]."\"".$mysel.">".$OIS_DATABASES[$i]."</option>\n");
  }
?>
  </select>
  </td>
</tr>
<?php
if($sysd['PRIV_CONNECT'])
  {
  echo("<tr>\n");
  echo("  <td><label for=\"conntype\">Connect as:</label></td>\n");
  echo("  <td><select name=\"CONNTYPE\" id=\"conntype\" size=\"1\"><option value=\"".OCI_DEFAULT."\">Normal</option>\n<option value=\"".OCI_SYSOPER."\">SYSOPER</option><option value=\"".OCI_SYSDBA."\">SYSDBA</option></select></td>\n");
  echo("</tr>\n");
  }
?>
</table>
<br>
<input type="submit" value="Login">
</form>
<?php
if($lerr)
  {
  echo("<div id=\"login_error\">\n");
  Error($errstr);
  echo("</div>\n");
  }
?>
</div>
<?php
require_once('inc/footer.inc.php');
?>
</body>
</html>
