<?php
/**
 * Main index page of OIS2 after successful login.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (30-May-2009)
 * $Id: mainindex.php,v 1.8 2010/07/13 21:53:02 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
require_once('inc/sessionheader.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title><?php echo(SITE_TITLE);?>: Connected as <?php printf("%s@%s",$_SESSION['DBUSER'],$_SESSION['TNSNAME']);?></title>
<?php
require_once('inc/metatags.inc.php');
?>
<style type="text/css">
#db_info {
  float         : left;
  padding-right : 20px;
}
#db_health {
  float         : left;
}
#list_extensions {
  margin-top    : 0px;
}
#list_extensions hr {
  margin-bottom : 20px;
}
</style>
</head>
<body>
<?php
printHeader('Welcome to Oracle Information Site '.SITE_VERSION);
require_once('navigation.inc.php');
?>
<div id="page_content">

<div id="db_info">
<?php
if($_SESSION['V_VIEWS'] == TRUE)
  {
  $iquery   = 'SELECT HOST_NAME,BANNER FROM v$instance,v$version';
  $hostdata = $db->Query($iquery);
  $hostname = $hostdata['HOST_NAME'];
  }
else
  {
  $hostname = 'N/A (insufficent privileges)';
  }
$dbversion = $db->Version();
$dbtime = $db->Query("SELECT TO_CHAR(SYSDATE,'DD-Mon-YYYY HH24:MI:SS') AS DBTIME FROM DUAL");
?>
<table cellspacing="1" cellpadding="2" border="0" class="datatable_border" summary="Oracle Version and components">
<tr>
  <td class="td_label">Oracle Version:</td>
  <td class="td_data"><?php echo($dbversion);?></td>
</tr>
<tr>
  <td class="td_label">Hostname:</td>
  <td><?php echo($hostname);?></td>
</tr>
<tr valign="top">
  <td class="td_label">Components:</td>
  <td class="td_data">
<?php
$db->QueryResult('SELECT PRODUCT,VERSION,STATUS FROM PRODUCT_COMPONENT_VERSION');
while($d = $db->FetchResult())
  {
  printf("  %s %s %s<br>\n",$d['PRODUCT'],$d['VERSION'],$d['STATUS']);
  }
$db->FreeResult();
?>
  </td>
</tr>
</table>
</div>
<div id="db_health">
<?php
flush();
$myquery=<<<EOM
SELECT SUM(TBYTES) AS TOTALBYTES
 FROM
  (
  SELECT  H1,
          SUM(H3) AS TBYTES
    FROM
    (
    SELECT  t1.TABLESPACE_NAME AS H1,
            d1.BYTES AS H3
      FROM  DBA_DATA_FILES d1, DBA_TABLESPACES t1
     WHERE  t1.TABLESPACE_NAME=d1.TABLESPACE_NAME
    UNION
    SELECT  t2.TABLESPACE_NAME AS H1,
            d2.BYTES H3
      FROM  DBA_TEMP_FILES d2, DBA_TABLESPACES t2
     WHERE  t2.TABLESPACE_NAME=d2.TABLESPACE_NAME
    )
  GROUP BY H1
  )
EOM;
if($_SESSION['DBA_VIEWS'] == TRUE)
  {
  $ts_stats = $db->Query($myquery);
  $ts_stats_d = $SGLFUNC->FormatSize($ts_stats['TOTALBYTES']);
  }
else
  {
  $ts_stats_d = 'N/A (insufficent privileges)';
  }
$myquery=<<<EOM
SELECT
  (SELECT COUNT(*) FROM V\$SESSION) AS TOTSESS
FROM DUAL
EOM;
if($_SESSION['V_VIEWS'] == TRUE)
  {
  $stats    = $db->Query($myquery);
  $sessions = $SGLFUNC->FormatNumber($stats['TOTSESS']);
  }
else
  {
  $sessions = 'N/A (insufficent privileges)';
  }
?>
<table cellspacing="1" cellpadding="2" border="0" summary="Oracle Database status infos" class="datatable_border">
<tr>
  <td class="td_label">Database time:</td>
  <td class="td_data"><?php echo($dbtime['DBTIME']);?></td>
</tr>
<tr>
  <td class="td_label">Webserver time:</td>
  <td class="td_data"><?php echo(date('d-M-Y H:i:s'));?></td>
</tr>
<tr>
  <td class="td_label">Database size:</td>
  <td class="td_data"><?php echo($ts_stats_d);?></td>
</tr>
<tr>
  <td class="td_label">Active sessions:</td>
  <td class="td_data"><?php echo($sessions);?></td>
</tr>
</table>
</div>
<div class="clear"></div>
<div id="list_extensions">
<hr>
<fieldset><legend><strong><?php echo($SGLFUNC->FormatNumber(count($OIS_EXTENSIONS)));?></strong> loaded plugin(s):</legend>
<ul>
<?php
foreach($OIS_EXTENSIONS AS $extname => $metadata)
  {
  printf("<li>%s v%s by %s</li>\n",$metadata['EXTENSION'],$metadata['VERSION'],$metadata['AUTHOR']);
  }
?>
</ul>
</fieldset>
</div>
</div>
<?php
require_once('inc/footer.inc.php');
?>
</body>
</html>
