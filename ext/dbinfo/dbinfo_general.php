<?php
/**
 * Extension: Database Informations.
 * This page displays general database informations.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (03-Sep-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="General_informations">
<?php
$myquery = <<<EOM
SELECT DBID,
       NAME,
       TO_CHAR(CREATED,'DD-Mon-YYYY HH24:MI:SS') AS CREATED,
       RESETLOGS_CHANGE# AS RLOGCHANGE,
       TO_CHAR(RESETLOGS_TIME,'DD-Mon-YYYY HH24:MI:SS') AS RLOGTIME,
       PRIOR_RESETLOGS_CHANGE# AS RESETLOGS,
       TO_CHAR(PRIOR_RESETLOGS_TIME,'DD-Mon-YYYY HH24:MI:SS') AS RESETLOGS_TIME,
       LOG_MODE,
       CHECKPOINT_CHANGE# AS CKPOINT_CHG,
       ARCHIVE_CHANGE# AS ARC_CHG,
       CONTROLFILE_TYPE,
       TO_CHAR(CONTROLFILE_CREATED,'DD-Mon-YYYY HH24:MI:SS') AS CONTROLFILE_CREATED,
       CONTROLFILE_SEQUENCE# AS CTL_SEQ,
       CONTROLFILE_CHANGE# AS CTL_CHG,
       TO_CHAR(CONTROLFILE_TIME,'DD-Mon-YYYY HH24:MI:SS') AS CONTROLFILE_TIME,
       OPEN_RESETLOGS,
       TO_CHAR(VERSION_TIME,'DD-Mon-YYYY HH24:MI:SS') AS VERSION_TIME,
       OPEN_MODE
       FROM V\$DATABASE
EOM;
$d = $db->Query($myquery);

$myquery=<<<EOM
SELECT  INSTANCE_NUMBER,
        INSTANCE_NAME,
        HOST_NAME,
        TO_CHAR(STARTUP_TIME,'DD-Mon-YYYY HH24:MI:SS') AS ST,
        STATUS,
        ARCHIVER,
        LOGINS,
        SHUTDOWN_PENDING,
        DATABASE_STATUS,
        INSTANCE_ROLE
 FROM   V\$INSTANCE
EOM;
$instance = $db->Query($myquery);
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="instance_info" summary="Instance informations">
<caption>Instance Informations</caption>
<tr class="td_even">
  <td align="left">Instance Number:</td>
  <td align="right"><?php echo($instance['INSTANCE_NUMBER']);?></td>
</tr>
<tr class="td_odd">
  <td align="left">Instance Name:</TD>
  <td align="right"><?php echo($instance['INSTANCE_NAME']);?></td>
</tr>
<tr class="td_even">
  <td align="left">Hostname of Server:</TD>
  <td align="right"><?php echo($instance['HOST_NAME']);?></td>
</tr>
<tr class="td_odd">
  <td align="left">Instance started at:</td>
  <td align="right"><?php echo($instance['ST']);?></td>
</tr>
<tr class="td_even">
  <td align="left">Instance Status:</td>
  <td align="right"><?php echo($instance['STATUS'])?></td>
</tr>
<tr class="td_odd">
  <td align="left">Instance Archivier:</td>
  <td align="right"><?php echo($instance['ARCHIVER']);?></td>
</tr>
<tr class="td_even">
  <td align="left">Logins:</td>
  <td align="right"><?php echo($instance['LOGINS']);?></td>
</tr>
<tr class="td_odd">
  <td align="left">Instance will be shutdown:</td>
  <td align="right"><?php echo($instance['SHUTDOWN_PENDING']);?></td>
</tr>
<tr class="td_even">
  <td align="left">Database Status:</td>
  <td align="right"><?php echo($instance['DATABASE_STATUS']);?></td>
</tr>
<tr class="td_odd">
  <td align="left">Instance used as:</td>
  <td align="right"><?php echo(preg_replace("/_/"," ",$instance['INSTANCE_ROLE']));?></td>
</tr>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="core_info" summary="Core component informations">
<caption>Core Component versions</caption>
<?php
flush();
$db->QueryResult("SELECT BANNER FROM V\$VERSION");
$lv=0;
while($c = $db->FetchResult())
  {
  if($lv % 2) $myback = 'td_even';
  else $myback = 'td_odd';
  echo("   <tr class=\"".$myback."\">\n");
  echo("  <td>".$c['BANNER']."</td>\n");
  echo(" </tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="installed_options" summary="Installed options">
<caption>Installed Options</caption>
<?php
flush();
$db->QueryResult("SELECT PARAMETER FROM V\$OPTION WHERE VALUE='TRUE' ORDER BY PARAMETER");
$lv=0;
$options = '';
while($o = $db->FetchResult(OCI_NUM))
  {
  $options.=$o[0].', ';
  $lv++;
  }
$options = substr($options,0,strlen($options)-2);
$db->FreeResult();
if($lv % 2) $myback = 'td_even';
else $myback = 'td_odd';
echo("<tr class=\"".$myback."\">\n");
echo("  <td><small>".$options."</small></td>\n");
echo("</tr>\n");
?>
</table>

<div class="clear"></div>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="db_info" summary="Database informations">
<caption>Database Informations</caption>
<tr class="td_even">
  <td>Database ID:</td>
  <td><?php echo($d['DBID']);?></td>
</tr>
<tr class="td_odd">
  <td>Name:</td>
  <td><?php echo($d['NAME']);?></td>
</tr>
<tr class="td_even">
  <td>Created:</td>
  <td><?php echo($d['CREATED']);?></td>
</tr>
<tr class="td_odd">
  <td>Log Mode:</td>
  <td><?php echo($d['LOG_MODE']);?></td>
</tr>
<tr class="td_even">
  <td>Current SCN:</td>
  <td><?php echo($SGLFUNC->FormatNumber($d['CKPOINT_CHG']));?></td>
</tr>
<tr class="td_odd">
  <td>Oldest SCN:</td>
  <td><?php echo($SGLFUNC->FormatNumber($d['ARC_CHG']));?></td>
</tr>
<tr class="td_even">
  <td>Database Mode:</Td>
  <td><?php echo($d['OPEN_MODE']);?></td>
</tr>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="ctl_info" summary="Controlfile informations">
<caption>Controlfile</caption>
<tbody><tr class="td_even">
  <td>Type:</td>
  <td><?php echo($d['CONTROLFILE_TYPE']);?></td>
</tr></tbody>
<tbody><tr class="td_odd">
  <td>Created:</td>
  <td><?php echo($d['CONTROLFILE_CREATED']);?></td>
</tr></tbody>
<tbody><tr class="td_even">
  <td>Seq. Number:</td>
  <td><?php echo($SGLFUNC->FormatNumber($d['CTL_SEQ']));?></td>
</tr></tbody>
<tbody><tr class="td_odd">
  <td>SCN:</td>
  <td><?php echo($SGLFUNC->FormatNumber($d['CTL_CHG']));?></td>
</tr></tbody>
<tbody><tr class="td_even">
  <td>Last updated:</td>
  <td><?php echo($d['CONTROLFILE_TIME']);?></td>
</tr></tbody>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="reset_info" summary="Resetlog informations">
<caption>Reset Log Info</caption>
<tr class="td_even">
  <td align="left">Number:</td>
  <td align="right"><?php echo($d['RLOGCHANGE']);?></td>
</tr>
<tr class="td_odd">
  <td align="left">Logs created:</td>
  <td align="right"><?php echo($d['RLOGTIME']);?></td>
</tr>
<tr class="td_even">
  <td align="left">Last Number:</td>
  <td align="right"><?php echo($d['RESETLOGS']);?></td>
</tr>
<tr class="td_odd">
  <td align="left">Last Reset at:</td>
  <td align="right"><?php echo($d['RESETLOGS_TIME']);?></td>
</tr>
<tr class="td_even">
  <td align="left">Open:</td>
  <td align="right"><?php echo($d['OPEN_RESETLOGS']);?></td>
</tr>
</table>

<div class="clear"></div>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
