<?php
/**
 * Extension: Top 20 Queries.
 * Lists the most used queries and also the SQL queries with most invalidations.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (12-Sep-2009)
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
$addHeader=<<<EOM
<script language="Javascript" type="text/javascript">
function showDetails(addr)
  {
  var myloc = "top20queries_details.php?ADDRESS="+addr;
  var mywin = window.open(myloc,'SQLDETAILS','width=780,height=500,scrollbars=yes');
  }
</script>
EOM;
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_V_Flag() == FALSE || $OIS2EXT->Get_DBA_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access this plugin - aborting!');
  }
if($OIS2EXT->Get_Oracle_Version() < 8)
  {
  $addon1 = '';
  }
else
  {
  $addon1 = "        CPU_TIME,\n        ELAPSED_TIME,\n";
  }
$q=<<<EOM
SELECT  SQL_TEXT,
        USERNAME,
        DISK_READS_PER_EXEC,
        BUFFER_GETS,
        DISK_READS,
        PARSE_CALLS,
        SORTS,
        EXECUTIONS,
        ROWS_PROCESSED,
        HIT_RATIO,
        FIRST_LOAD_TIME,
        SHARABLE_MEM,
        PERSISTENT_MEM,
        RUNTIME_MEM,
$addon1
        ADDR,
        HASH_VALUE
  FROM  (
        SELECT  SQL_TEXT,
                B.USERNAME,
                ROUND((A.DISK_READS/DECODE(A.EXECUTIONS,0,1,A.EXECUTIONS)),2) AS DISK_READS_PER_EXEC,
                A.DISK_READS,
                A.BUFFER_GETS,
                A.PARSE_CALLS,
                A.SORTS,
                A.EXECUTIONS,
                A.ROWS_PROCESSED,
                100 - ROUND(100 * A.DISK_READS/GREATEST(A.BUFFER_GETS,1),2) AS HIT_RATIO,
                A.FIRST_LOAD_TIME,
                SHARABLE_MEM,
                PERSISTENT_MEM,
                RUNTIME_MEM,
$addon1
                RAWTOHEX(ADDRESS) AS ADDR,
                HASH_VALUE
          FROM  SYS.V_\$SQLAREA A, SYS.ALL_USERS B
         WHERE  A.PARSING_USER_ID=B.USER_ID
           AND  B.USERNAME NOT IN ('SYS','SYSTEM')
         ORDER  BY 3 DESC)
 WHERE ROWNUM < 21
EOM;
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Lists the Top 20 queries">
<caption>Click on Query to view statistics</caption>
<thead>
<tr>
  <th>#</th>
  <th>Schema User</th>
  <th>SQL Query</th>
</tr>
</thead>
<tbody>
<?php
$lv = 0;
$db->QueryResult($q);
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
  echo("<tr class=\"".$myback."\" valign=\"top\">\n");
  echo("  <td align=\"center\">".sprintf("%02d.",$lv+1)."</td>\n");
  echo("  <td align=\"center\">".$s['USERNAME']."</td>\n");
  echo("  <td align=\"left\"><a href=\"javascript:showDetails('".$s['ADDR']."')\">".wordwrap(htmlspecialchars($s['SQL_TEXT']),100,'<br>',true)."</a></td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
if(!$lv)
  {
  echo("<tr class=\"td_even\">\n<td colspan=\"3\" align=\"center\"><b>No data</b></td>\n</tr>\n");
  }
?>
</tbody>
</table>
<br>
<?php
flush();
$sql=<<<EOM
SELECT SUBSTR(RTRIM(sql_text),1,900) AS "SQL", INVALIDATIONS
  FROM V\$SQLAREA
 WHERE INVALIDATIONS > 10
 ORDER BY INVALIDATIONS DESC
EOM;
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Lists SQL queries with most invalidations">
<caption>SQL queries with most invalidations</caption>
<thead>
<tr>
  <th>SQL Query</th>
  <th>Invalidations</th>
</tr>
</thead>
<tbody>
<?php
$lv = 0;
$db->QueryResult($sql);
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
  echo("<tr class=\"".$myback."\" valign=\"top\">\n");
  echo("  <td align=\"left\">".wordwrap(htmlspecialchars($s['SQL']),100,'<br> ,',true)."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatNumber($s['INVALIDATIONS'])."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
if(!$lv)
  {
  echo("<tr class=\"td_even\">\n<td colspan=\"2\" align=\"center\"><b>No data</b></td>\n</tr>\n");
  }
?>
</tbody>
</table>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
