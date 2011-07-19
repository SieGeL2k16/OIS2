<?php
/**
 * Extension: Flashback.
 * Displays informations about the flashback usage based on the corresponding DBA_* views.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (19-Jul-2011)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Flashback_overview">
<?php
$query=<<<EOM
SELECT NAME,
       VALUE
  FROM V\$PARAMETER
 WHERE NAME IN ('db_flashback_retention_target','undo_retention','undo_tablespace')
EOM;
$params = array();
$db->QueryResult($query);
while($d = $db->FetchResult())
  {
  $params[$d['NAME']] = $d['VALUE'];
  }
$db->FreeResult();
$d = $db->Query("SELECT LOG_MODE FROM V\$DATABASE");
$params['log_mode'] = $d['LOG_MODE'];
printf("Flashback Retention time is set to <b>%s</b> minutes.<br>\n",$params['db_flashback_retention_target']);
if($params['log_mode'] != 'ARCHIVELOG')
  {
  InformUser("<b>Instance does not run in ARCHIVELOG mode - flashback not available!</b>\n");
  }
?>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="flashback_usage" summary="Overview of flashback recovery usage">
<caption>Flash Recovery area usage</caption>
<thead><tr>
  <th>File type</th>
  <th>In use</th>
  <th>Reclaimable</th>
  <th># of files</th>
</tr></thead>
<tbody>
<?php
// Query first the Usage view from Flashback:
$query=<<<SQL
SELECT FILE_TYPE,
       PERCENT_SPACE_USED,
       PERCENT_SPACE_RECLAIMABLE,
       NUMBER_OF_FILES
  FROM V\$FLASH_RECOVERY_AREA_USAGE
SQL;
$lv = 0;
$db->QueryResult($query);
while($d = $db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td>".$d['FILE_TYPE']."</td>\n");
  echo("  <td align=\"right\">".$d['PERCENT_SPACE_USED']."%</td>\n");
  echo("  <td align=\"right\">".$d['PERCENT_SPACE_RECLAIMABLE']."%</td>\n");
  echo("  <td align=\"right\">".$d['NUMBER_OF_FILES']."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
<?php
// Fetch log table from flashback:
$query=<<<SQL
SELECT  OLDEST_FLASHBACK_SCN,
        TO_CHAR(OLDEST_FLASHBACK_TIME,'DD-Mon-YYYY Hh24:MI:SS') AS OFT,
        RETENTION_TARGET,
        FLASHBACK_SIZE,
        ESTIMATED_FLASHBACK_SIZE
  FROM  V\$FLASHBACK_DATABASE_LOG
SQL;
$flashlog = $db->Query($query);
if(is_array($flashlog) == FALSE)
  {
  $flashlog['OLDEST_FLASHBACK_SCN']     = 'N/A';
  $flashlog['OFT']                      = 'N/A';
  $flashlog['RETENTION_TARGET']         = 'N/A';
  $flashlog['FLASHBACK_SIZE']           = 0;
  $flashlog['ESTIMATED_FLASHBACK_SIZE'] = 0;
  }
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="flashback_log" summary="Overview of flashback log table">
<caption>V$FLASHBACK_DATABASE_LOG contents</caption>
<tr class="td_even">
  <td>Lowest SCN in the flashback data:</td>
  <td><?php echo($flashlog['OLDEST_FLASHBACK_SCN']);?></td>
</tr>
<tr class="td_odd">
  <td>Time of the lowest SCN:</td>
  <td><?php echo($flashlog['OFT']);?></td>
</tr>
<tr class="td_even">
  <td>Target retention time:</td>
  <td><?php echo($flashlog['RETENTION_TARGET']);?> mins</td>
</tr>
<tr class="td_odd">
  <td>Current size of the flashback data:</td>
  <td><?php echo($SGLFUNC->FormatSize($flashlog['FLASHBACK_SIZE']));?></td>
</tr>
<tr class="td_even">
  <td>Estimated size of flashback data needed:</td>
  <td><?php echo($SGLFUNC->FormatSize($flashlog['ESTIMATED_FLASHBACK_SIZE']));?></td>
</tr>
</table>
<div class="clear"></div>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="flashback_stats" summary="Overview of flashback stat table">
<caption>V$FLASHBACK_DATABASE_STAT contents (Last 100 rows)</caption>
<thead><tr>
  <th>Begin time</th>
  <th>End time</th>
  <th>Flashback<br>written</th>
  <th>DB Data<br>written</th>
  <th>Redo<br>written</th>
  <th>Est.<br>Flashback<br>Size</th>
</tr></thead>
<?php
$query=<<<SQL
SELECT TO_CHAR(BEGIN_TIME,'DD-Mon-YYYY HH24:MI:SS') AS BD,
       TO_CHAR(END_TIME,'DD-Mon-YYYY HH24:MI:SS') AS ED,
       FLASHBACK_DATA,
       DB_DATA,
       REDO_DATA,
       ESTIMATED_FLASHBACK_SIZE
 FROM V\$FLASHBACK_DATABASE_STAT
 WHERE ROWNUM < 101
SQL;
$lv = 0;
$db->QueryResult($query);
while($d = $db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td title=\"Begin of interval\">".$d['BD']."</td>\n");
  echo("  <td title=\"End of interval\">".$d['ED']."</td>\n");
  echo("  <td align=\"right\" title=\"Flashback data written during the interval\">".$SGLFUNC->FormatSize($d['FLASHBACK_DATA'])."</td>\n");
  echo("  <td align=\"right\" title=\"Database data (read/write) written during the interval\">".$SGLFUNC->FormatSize($d['DB_DATA'])."</td>\n");
  echo("  <td align=\"right\" title=\"Redo data written during the interval\">".$SGLFUNC->FormatSize($d['REDO_DATA'])."</td>\n");
  echo("  <td align=\"right\" title=\"Estimated Flashback size needed for retention during the interval\">".$SGLFUNC->FormatSize($d['ESTIMATED_FLASHBACK_SIZE'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtTabFooter();
?>
