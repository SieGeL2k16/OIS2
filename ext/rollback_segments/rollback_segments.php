<?php
/**
 * Extension: Redo Log Informations.
 * Displays various redo log informations.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (05-Sep-2009)
 * $Id: rollback_segments.php 2 2011-06-30 18:10:40Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$addHeader = '';
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_V_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access use this plugin - aborting!');
  }
$undo = $db->Query("SELECT VALUE FROM V\$PARAMETER WHERE NAME = 'undo_management'");
if(!isset($undo['VALUE']) || $undo['VALUE'] == "")
  {
  $undo['VALUE'] = 'Not available (pre 9.x Version)';
  $v9 = false;
  $haslatch = "'N/A' AS LATCH,";
  }
else
  {
  $undo['VALUE'] = '<b>'.$undo['VALUE'].'</b>';
  $uts = $db->Query("SELECT VALUE FROM V\$PARAMETER WHERE NAME='undo_tablespace'");
  $undo['VALUE'].=' / Undo Tablespace: <b>'.$uts['VALUE'].'</b>';
  $v9 = true;
  $haslatch="       s.LATCH,";
  $ur = $db->Query("SELECT VALUE FROM V\$PARAMETER WHERE NAME='undo_retention'");
  $undo['VALUE'].=' / Retention time: <b>'.$SGLFUNC->FormatTime($ur['VALUE']).'</b>';
  }
echo('UNDO Management Mode: '.$undo['VALUE'].'<br>');
?>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable_border" summary="Rollback segment overview">
<caption>Rollback segments</caption>
<thead><tr>
  <th>Nr.</th>
  <th>Name</th>
  <th>Tablespace</th>
  <th>Bytes</th>
  <th>Blocks</th>
  <th>Extends</th>
  <th>RSSIZE</th>
  <th>Optimal<br>Size</th>
  <th>Highwater<br>mark</th>
  <th>Latch</th>
  <th>Status</th>
</tr></thead>
<tbody>
<?php
$myquery=<<<EOM
SELECT n.USN,
       d.SEGMENT_NAME,
       d.TABLESPACE_NAME,
       d.BYTES,
       d.BLOCKS,
       d.EXTENTS,
       LOWER(s.STATUS) AS STATE,
       s.RSSIZE,
       s.OPTSIZE,
$haslatch
       s.HWMSIZE
 FROM  SYS.DBA_SEGMENTS d, V\$ROLLSTAT s,V\$ROLLNAME n
 WHERE segment_type = 'ROLLBACK'
  AND  n.USN=s.USN
  AND  d.SEGMENT_NAME=n.NAME
 ORDER BY n.USN
EOM;
$db->QueryResult($myquery);
$lv=0;
while($r = $db->FetchResult())
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
  echo(" <td align=\"center\">".$r['USN']."</td>\n");
  echo(" <td align=\"center\">".$r['SEGMENT_NAME']."</td>\n");
  echo(" <td align=\"center\">".$r['TABLESPACE_NAME']."</td>\n");
  echo(" <td align=\"right\">".$SGLFUNC->FormatNumber($r['BYTES'])."</td>\n");
  echo(" <td align=\"right\">".$SGLFUNC->FormatNumber($r['BLOCKS'])."</td>\n");
  echo(" <td align=\"right\">".$r['EXTENTS']."</td>\n");
  echo(" <td align=\"right\">".$SGLFUNC->FormatNumber($r['RSSIZE'])."</td>\n");
  echo(" <td align=\"right\">".$SGLFUNC->FormatNumber($r['OPTSIZE'])."</td>\n");
  echo(" <td align=\"right\">".$SGLFUNC->FormatNumber($r['HWMSIZE'])."</td>\n");
  echo(" <td align=\"right\">".$r['LATCH']."</td>\n");
  echo(" <td align=\"right\">".UCWords($r['STATE'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable_border" summary="Current usage of rollback segments">
<caption>Current usage of Rollback Segments</caption>
<thead><tr>
  <th>Username</th>
  <th>USN #</th>
  <th>UBAFIL</th>
  <th>UBABLK</th>
  <th>USED_UBLOCK</th>
</tr></thead>
<tbody>
<?php
/*
 *  Query user activity on rollback segments
 */
$myquery = <<<EOM
SELECT  s.USERNAME,
        t.XIDUSN,
        t.UBAFIL,
        t.UBABLK,
        t.USED_UBLK
FROM    V\$session s, v\$transaction t
WHERE   s.SADDR = t.SES_ADDR
EOM;
$lv=0;
$db->QueryResult($myquery);
while($r = $db->FetchResult())
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
  echo("  <td align=\"center\">".$r['USERNAME']."</td>\n");
  echo("  <td align=\"center\">".$r['XIDUSN']."</td>\n");
  echo("  <td align=\"center\">".$r['UBAFIL']."</td>\n");
  echo("  <td align=\"center\">".$r['UBABLK']."</td>\n");
  echo("  <td align=\"center\">".$r['USED_UBLK']."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
if(!$lv)
  {
  echo("<tr class=\"td_even\">\n");
  echo("  <td colspan=\"5\" align=\"center\"><b>None.</b></td>\n");
  echo("</tr>\n");
  }
?>
</tbody>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable_border" summary="Rollback statistics">
<caption>Rollback Statistics</caption>
<thead><tr>
  <th>Size in<br>bytes</th>
  <th>Optimal<br>size</th>
  <th>High Water<br>Mark</th>
  <th>Num of<br>Shrinks</th>
  <th>Num of<br>Wraps</th>
  <th>Extents</th>
  <th>Average size<br>Active Extents</th>
</tr></thead>
<tbody>
<?php
$query=<<<EOM
SELECT RSSIZE,OPTSIZE,HWMSIZE,SHRINKS,WRAPS,EXTENDS,AVEACTIVE
  FROM V\$ROLLSTAT
 ORDER BY ROWNUM
EOM;
$db->QueryResult($query);
$lv = 0;
while($s = $db->FetchResult())
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
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['RSSIZE'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['OPTSIZE'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['HWMSIZE'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['SHRINKS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['WRAPS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['EXTENDS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['AVEACTIVE'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
