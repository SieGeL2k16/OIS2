<?php
/**
 * Extension: Top 20 Queries.
 * Displays details for a given SQL query.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (18-Jul-2014)
 * $Id: top20queries_details.php 10 2014-07-20 09:43:24Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');

$addr = (isset($_GET['ADDRESS'])) ? strip_tags($_GET['ADDRESS']) : '';

// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'show_processes.php');
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$OIS2EXT->PrintExtHeader('Show process details for Hash='.$addr,'',TRUE);
?>
<div id="fullpage_content">
<?php
if($OIS2EXT->Get_Oracle_Version() < 8)
  {
  $addon1 = '';
  $addon2 = '';
  }
else
  {
  $addon1 = "        ROUND(CPU_TIME/1000000,2) AS CPU,\n        ROUND(ELAPSED_TIME/1000000,2) AS ELAPSED,\n";
  $addon2 = "        CPU_TIME,\n        ELAPSED_TIME,\n";
  }
$q=<<<EOM
SELECT  SQL_FULLTEXT,
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
        ADDRESS,
        HASH_VALUE,
        MODULE,
        ACTION
  FROM  (
        SELECT  SQL_FULLTEXT,
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
$addon2
                ADDRESS,
                HASH_VALUE,
                MODULE,
                ACTION
          FROM  SYS.V_\$SQLAREA A, SYS.ALL_USERS B
         WHERE  A.PARSING_USER_ID=B.USER_ID
         ORDER  BY 3 DESC)
WHERE ADDRESS=:addr
EOM;
$sp = array('addr' => $addr);
$s = $db->QueryHash($q,OCI_ASSOC,0,$sp);
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Displays details for a given SQL query">
<tr class="td_even" valign="top">
  <td align="right"><b>SQL Query:</b></td>
  <td align="left"><code><?php echo(str_replace("\n","<br>",htmlspecialchars($s['SQL_FULLTEXT'])));?></code></td>
</tr>
<tr class="td_odd" valign="top">
  <td align="right"><b>Schema User:</b></td>
  <td align="left"><?php echo($s['USERNAME']);?></td>
</tr>
<tr class="td_even" valign="top">
  <td nowrap align="right"><b>Disk reads per exec.:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['DISK_READS_PER_EXEC'],2));?></td>
</tr>
<tr class="td_odd" valign="top">
  <td align="right"><b>Buffer gets:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['BUFFER_GETS']));?></td>
</tr>
<tr class="td_even" valign="top">
  <td align="right"><b>Disk reads:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['DISK_READS']));?></td>
</tr>
<tr class="td_odd" valign="top">
  <td nowrap align="right"><b>Parse calls / Executions:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['PARSE_CALLS']));?> / <?php echo($SGLFUNC->FormatNumber($s['EXECUTIONS']));?></td>
</tr>
<tr class="td_even" valign="top">
  <td align="right"><b>Sorts:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['SORTS']));?></td>
</tr>
<tr class="td_odd" valign="top">
  <td align="right"><b>Rows processed:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['ROWS_PROCESSED']));?></td>
</tr>
<tr class="td_even" valign="top">
  <td align="right"><b>Hit Ratio:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['HIT_RATIO'],2));?>%</td>
</tr>
<tr class="td_odd" valign="top">
  <td align="right"><b>First loading time:</b></td>
  <td align="left"><?php echo($s['FIRST_LOAD_TIME']);?></td>
</tr>
<?php
if($addon1 != '')
  {
  echo("<tr class=\"td_even\" valign=\"top\">\n");
  echo("  <td align=\"right\"><b>CPU Time:</b></td>\n");
  echo("  <td align=\"left\">".$s['CPU']."sec</td>\n");
  echo("</tr>\n");
  echo("<tr class=\"td_odd\" valign=\"top\">\n");
  echo("  <td align=\"right\"><b>Elapsed Time:</b></td>\n");
  echo("  <td align=\"left\">".$s['ELAPSED']."sec</td>\n");
  echo("</tr>\n");
  }
?>
<tr class="td_even" valign="top">
  <td align="right"><b>Sharable Memory:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['SHARABLE_MEM']));?> Bytes</td>
</tr>
<tr class="td_odd" valign="top">
  <td align="right"><b>Persistent Memory:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['PERSISTENT_MEM']));?> Bytes</td>
</tr>
<tr class="td_even" valign="top">
  <td align="right"><b>Runtime Memory:</b></td>
  <td align="left"><?php echo($SGLFUNC->FormatNumber($s['RUNTIME_MEM']));?> Bytes</td>
</tr>
<tr class="td_odd" valign="top">
  <td align="right"><b>Module:</b></td>
  <td align="left"><?php echo($s['MODULE']);?></td>
</tr>
<tr class="td_even" valign="top">
  <td align="right"><b>Action:</b></td>
  <td align="left"><?php echo($s['ACTION']);?></td>
</tr>
</table>
<br>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter('<div align="center"><a href="javascript:self.close()" class="btn">Close window</a></div>');
?>
