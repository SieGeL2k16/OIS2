<?php
/**
 * Extension: Redo Log Informations.
 * Displays various redo log informations.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (05-Sep-2009)
 * $Id$
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
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Redo Log overview">
<thead><tr>
  <th>Group</th>
  <th>Redo-Log file</th>
  <th>Members</th>
  <th>Size</th>
  <th>Redolog<br>state</th>
  <th>Archived?</th>
  <th>First change</th>
  <th>Seq. #</th>
  <th>SCN</th>
</tr></thead>
<tbody>
<?php
$myquery=<<<EOM
SELECT  f.MEMBER,
        l.THREAD# AS THREADNR,
        l.SEQUENCE# AS SEQNR,
        l.BYTES,
        l.MEMBERS,
        l.ARCHIVED,
        l.STATUS,
        l.FIRST_CHANGE# AS FCHG,
        TO_CHAR(l.FIRST_TIME,'DD-Mon-YYYY HH24:MI:SS') AS FT,
        l.GROUP# AS GROUPNR
  FROM  V\$LOGFILE f, V\$LOG l
 WHERE  f.GROUP#=l.GROUP#
 ORDER  BY l.GROUP#
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
  echo(" <td align=\"center\">".$r['GROUPNR']."</td>\n");
  echo(" <td>".$r['MEMBER']."</td>\n");
  echo(" <td align=\"center\">".$r['MEMBERS']."</td>\n");
  echo(" <td align=\"right\">".$SGLFUNC->FormatSize($r['BYTES'])."</td>\n");
  echo(" <td align=\"center\">".UCWords(strtolower($r['STATUS']))."</td>\n");
  echo(" <td align=\"center\">".UCWords(strtolower($r['ARCHIVED']))."</td>\n");
  echo(" <td align=\"center\">".$r['FT']."</td>\n");
  echo(" <td align=\"center\">".$SGLFUNC->FormatNumber($r['SEQNR'])."</td>\n");
  echo(" <td align=\"center\">".$SGLFUNC->FormatNumber($r['FCHG'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Latch informations">
<thead><tr>
 <th>Latch</th>
 <th>WTW Gets</th>
 <th>WTW Misses</th>
 <th>IMM Gets</th>
 <th>IMM Misses</th>
</tr></thead>
<tbody>
<?php
$myquery = <<<EOM
SELECT    SUBSTR(name,1,20) AS NAME,
          SUM(gets) AS N_GETS,
          SUM(misses) AS N_MISS,
          SUM(immediate_gets) AS N_IGETS,
          SUM(immediate_misses) AS N_IMISS
 FROM     V\$LATCH
 WHERE    name LIKE '%redo%'
 GROUP BY name
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
  echo("  <td align=\"left\">".$r['NAME']."</TD>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($r['N_GETS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($r['N_MISS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($r['N_IGETS'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($r['N_IMISS'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

<?php
// Ratio of MISSES to GETS:
$query=<<<EOM
SELECT  TO_CHAR(ROUND((SUM(misses)/(SUM(gets)+0.00000000001) * 100),2), '990.90')||'%'
  FROM  v\$latch
 WHERE  NAME IN ('redo allocation',  'redo copy')
EOM;
$ratio_misses_gets = $db->Query($query,OCI_NUM);

// Ratio of IMMEDIATE_MISSES to IMMEDIATE_GETS:
$query=<<<EOM
SELECT  TO_CHAR(ROUND((SUM(immediate_misses) / (SUM(immediate_misses+immediate_gets)+0.00000000001) * 100),2),'990.90')||'%'
  FROM  V\$LATCH
 WHERE  NAME IN ('redo allocation',  'redo copy')
EOM;
$ratio_immediate_misses_gets = $db->Query($query,OCI_NUM);
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable_border" summary="Ratios for redo allocation / redo copy">
<caption>Ratio of misses to gets</caption>
<tbody>
<tr class="td_even">
  <td>Ratio of MISSES to GETS:</td>
  <td><?php echo($ratio_misses_gets[0]);?></td>
</tr>
<tr class="td_odd">
  <td>Ratio of IMMEDIATE_MISSES to IMMEDIATE_GETS:</td>
  <td><?php echo($ratio_immediate_misses_gets[0]);?></td>
</tr>
</tbody>
</table>
If either ratio exceeds 1%, performance will be affected.<br>
Decreasing the size of LOG_SMALL_ENTRY_MAX_SIZE reduces the number of processes copying information on the redo allocation latch.<br>
Increasing the size of LOG_SIMULTANEOUS_COPIES will reduce contention for redo copy latches.

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
