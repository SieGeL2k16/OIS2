<?php
/**
 * Logout page destroys session.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (24-Apr-2009)
 * $Id: logout.php 2 2011-06-30 18:10:40Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
require_once('inc/sessionheader.inc.php');
$username = $_SESSION['DBUSER'];
$tnsname  = $_SESSION['TNSNAME'];
$logintime= time() - $_SESSION['LOGINTIME'];
@session_destroy();
WriteLog(sprintf("LOGOUT: User \"%s\" disconnected from DB \"%s\"",$username,$tnsname));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title><?php echo(SITE_TITLE);?>: Disconnected from database</title>
<?php
require_once('inc/metatags.inc.php');
?>
<script language="Javascript" type="text/javascript">
$(function() {
		$("a").button();
	});
</script>
</head>
<body>
<h1>Your are now disconnected from <?php echo($tnsname);?></h1>
<?php
$info = sprintf("Your session time was %s",$SGLFUNC->FormatTime($logintime));
InformUser($info);
?>
<br>
<a href="index.php" class="btn">Click here to login again</a>
</body>
</html>
