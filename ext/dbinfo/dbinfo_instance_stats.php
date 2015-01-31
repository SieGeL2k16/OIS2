<?php
/**
 * Extension: Database Informations.
 * Displays instance informations.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (05-Sep-2009)
 * $Id: dbinfo_instance_stats.php 2 2011-06-30 18:10:40Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Instance_statistics">
<div id="instance_stats_table">
Below is a dump of the core Instance Statistics that are greater than 0.<br>
<br>
Of interest here are the values for:<br>
<ul>
  <li>Cumulative logons - (# of actual connections to the DB since last startup - good volume-of-use statistic)</li>
  <li>Table fetch continued row - (# of chained rows - will be higher if there are a lot of long fields</li>
  <li>If the value goes up over time, it is a good signaller of general database fragmentation)</li>
</ul>
<?php
$db->QueryResult('SELECT STATISTIC#, NAME, VALUE FROM V$SYSSTAT WHERE  VALUE > 0');
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Instance statistics">
<caption>Instance statistics</caption>
<tbody>
<?php
$lv = 0;
while($s = $db->FetchResult(OCI_NUM))
  {
  if($lv % 2)
    {
    $myback = 'td_even';
    }
  else
    {
    $myback = 'td_odd';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"right\">".$s[0]."</td>\n");
  echo("  <td align=\"left\">".$s[1]."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s[2])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
</div>

<div id="instance_stats_wait">
This will show wait stats for certain kernel instances.  This may show the need for additional rbs, wait lists, db_buffers.<br>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Wait statistics">
<caption>Wait Statistics for the Instance</caption>
<thead><tr>
  <th>Class type</th>
  <th>Times waited</th>
  <th>Total times</th>
</tr></thead>
<tbody>
<?php
$db->QueryResult("SELECT CLASS,COUNT,TIME FROM V\$WAITSTAT WHERE COUNT > 0 ORDER BY CLASS");
$lv = 0;
while($w = $db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_even';
    }
  else
    {
    $myback = 'td_odd';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"left\">".$w['CLASS']."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($w['COUNT'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($w['TIME'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
<br>
Look at the wait statistics generated above (if any). They will tell you where there is contention in the system.<br>
There will usually be some contention in any system - but if the ratio of waits for a particular operation starts to rise,
you may need to add additional resource, such as more database buffers, log buffers, or rollback segments.<br>
</div>

<div id="instance_stats_cursors">
<?php
$sql=<<<EOM
SELECT 'session_cached_cursors' parameter, LPAD (VALUE, 5) VALUE,
       DECODE (VALUE, 0, ' n/a', TO_CHAR (100 * used / VALUE, '990') || '%') USAGE
  FROM
    (
    SELECT MAX (s.VALUE) used
      FROM v\$statname n, v\$sesstat s
     WHERE n.NAME = 'session cursor cache count'
       AND s.statistic# = n.statistic#
    ),
    (
    SELECT VALUE
      FROM v\$parameter
      WHERE NAME = 'session_cached_cursors'
    )
UNION ALL
SELECT 'open_cursors', LPAD (VALUE, 5), TO_CHAR (100 * used / VALUE, '990') || '%'
  FROM
    (
    SELECT MAX(SUM(s.VALUE)) used
      FROM v\$statname n, v\$sesstat s
     WHERE n.NAME IN ('opened cursors current', 'session cursor cache count')
       AND s.statistic# = n.statistic#
     GROUP BY s.SID
    ),
    (
    SELECT VALUE
      FROM v\$parameter
     WHERE NAME = 'open_cursors'
    )
EOM;
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Cursor statistics">
<caption>Cursor statistics</caption>
<thead><tr>
  <th>Name</th>
  <th>Value</th>
  <th>Usage</th>
</tr></thead>
<tbody>
<?php
$lv = 0;
$db->QueryResult($sql);
while($s = $db->FetchResult(OCI_NUM))
  {
  if($lv % 2)
    {
    $myback = 'td_even';
    }
  else
    {
    $myback = 'td_odd';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"left\">".$s[0]."</td>\n");
  echo("  <td align=\"right\">".$s[1]."</td>\n");
  echo("  <td align=\"right\">".$s[2]."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
</div>

<div class="clear"></div>

</div>
<?php
$db->Disconnect();
?>
