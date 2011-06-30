<?php
/**
 * Extension: Show Process details.
 * Displays detailed informations for a given process id.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (31-May-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');

$pid = (isset($_GET['PID'])) ? strip_tags($_GET['PID']) : '';

// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'show_processes.php');
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$OIS2EXT->PrintExtHeader('Show process details for PID='.$pid,'',TRUE);
?>
<div id="fullpage_content">
<?php
$myquery =<<<EOM
SELECT RAWTOHEX(SADDR) AS SADDR,
       SID,
       SERIAL# AS SERIALNR,
       NVL(AUDSID,0) AS AUDSID,
       RAWTOHEX(PADDR) AS PADDR,
       USER# AS USERNR,
       USERNAME,
       COMMAND,
       OWNERID,
       NVL(TADDR,'---') AS TADDR,
       NVL(LOCKWAIT,'---') AS LOCKWAIT,
       STATUS,
       SERVER,
       SCHEMA# AS SCHEMANR,
       SCHEMANAME,
       OSUSER,
       MACHINE,
       NVL(TERMINAL,'---') AS TERMINAL,
       PROGRAM,
       TYPE,
       NVL(MODULE,'---') AS MODULE,
       NVL(ACTION,'---') AS ACTION,
       NVL(CLIENT_INFO,'---') AS CLIENT_INFO,
       TO_CHAR(LOGON_TIME,'DD-Mon-YYYY HH24:MI:SS') AS LTIME,
       RAWTOHEX(SQL_ADDRESS) AS SQL_ADDRESS,
       SQL_HASH_VALUE,
       RAWTOHEX(PREV_SQL_ADDR) AS PREV_SQL_ADDR,
       PREV_HASH_VALUE
       FROM V\$SESSION
 WHERE PROCESS=:myproc
EOM;
$dbparm['myproc'] = $pid;
$d = $db->QueryHash($myquery,OCI_ASSOC,0,$dbparm);
$currsql = "";
if(isset($d['SQL_ADDRESS']) && isset($d['SQL_HASH_VALUE']))
  {
  $sp = array('addr' => $d['SQL_ADDRESS'], 'hash' => $d['SQL_HASH_VALUE']);
  $db->QueryResultHash("SELECT SQL_TEXT FROM V\$SQLTEXT WHERE ADDRESS=:addr AND HASH_VALUE=:hash ORDER BY PIECE",$sp);
  while($sql = $db->FetchResult())
    {
    $currsql.= $sql['SQL_TEXT'];
    }
  $db->FreeResult();
  }
$prevsql = "";
if(isset($d['PREV_SQL_ADDR']) && isset($d['PREV_HASH_VALUE']))
  {
  $sp = array('addr' => $d['PREV_SQL_ADDR'], 'hash' => $d['PREV_HASH_VALUE']);
  $db->QueryResultHash("SELECT SQL_TEXT FROM V\$SQLTEXT WHERE ADDRESS=:addr AND HASH_VALUE=:hash ORDER BY PIECE",$sp);
  while($sql = $db->FetchResult())
    {
    $prevsql.= $sql['SQL_TEXT'];
    }
  $db->FreeResult();
  }
$cmdarray[0]  = "UNKNOWN CODE!";
$cmdarray[1]  = "CREATE TABLE";
$cmdarray[2]  = "INSERT";
$cmdarray[3]  = "SELECT";
$cmdarray[4]  = "CREATE CLUSTER";
$cmdarray[5]  = "ALTER CLUSTER";
$cmdarray[6]  = "UPDATE";
$cmdarray[7]  = "DELETE";
$cmdarray[8]  = "DROP CLUSTER";
$cmdarray[9]  = "CREATE INDEX";
$cmdarray[10] = "DROP INDEX";
$cmdarray[11] = "ALTER INDEX";
$cmdarray[12] = "DROP TABLE";
$cmdarray[13] = "CREATE SEQUENCE";
$cmdarray[14] = "ALTER SEQUENCE";
$cmdarray[15] = "ALTER TABLE";
$cmdarray[16] = "DROP SEQUENCE";
$cmdarray[17] = "GRANT";
$cmdarray[18] = "REVOKE";
$cmdarray[19] = "CREATE SYNONYM";
$cmdarray[20] = "DROP SYNONYM";
$cmdarray[21] = "CREATE VIEW";
$cmdarray[22] = "DROP VIEW";
$cmdarray[23] = "VALIDATE INDEX";
$cmdarray[24] = "CREATE PROCEDURE";
$cmdarray[25] = "ALTER PROCEDURE";
$cmdarray[26] = "LOCK TABLE";
$cmdarray[27] = "NO OPERATION";
$cmdarray[28] = "RENAME";
$cmdarray[29] = "COMMENT";
$cmdarray[30] = "AUDIT";
$cmdarray[31] = "NOAUDIT";
$cmdarray[32] = "CREATE DATABASE LINK";
$cmdarray[33] = "DROP DATABASE LINK";
$cmdarray[34] = "CREATE DATABASE";
$cmdarray[35] = "ALTER DATABASE";
$cmdarray[36] = "CREATE ROLLBACK SEGMENT";
$cmdarray[37] = "ALTER ROLLBACK SEGMENT";
$cmdarray[38] = "DROP ROLLBACK SEGMENT";
$cmdarray[39] = "CREATE TABLESPACE";
$cmdarray[40] = "ALTER TABLESPACE";
$cmdarray[41] = "DROP TABLESPACE";
$cmdarray[42] = "ALTER SESSION";
$cmdarray[43] = "ALTER USE";
$cmdarray[44] = "COMMIT";
$cmdarray[45] = "ROLLBACK";
$cmdarray[46] = "SAVEPOINT";
$cmdarray[47] = "PL/SQL EXECUTE";
$cmdarray[48] = "SET TRANSACTION";
$cmdarray[49] = "ALTER SYSTEM SWITCH LOG";
$cmdarray[50] = "EXPLAIN";
$cmdarray[51] = "CREATE USER";
$cmdarray[25] = "CREATE ROLE";
$cmdarray[53] = "DROP USER";
$cmdarray[54] = "DROP ROLE";
$cmdarray[55] = "SET ROLE";
$cmdarray[56] = "CREATE SCHEMA";
$cmdarray[57] = "CREATE CONTROL FILE";
$cmdarray[58] = "ALTER TRACING";
$cmdarray[59] = "CREATE TRIGGER";
$cmdarray[60] = "ALTER TRIGGER";
$cmdarray[61] = "DROP TRIGGER";
$cmdarray[62] = "ANALYZE TABLE";
$cmdarray[63] = "ANALYZE INDEX";
$cmdarray[64] = "ANALYZE CLUSTER";
$cmdarray[65] = "CREATE PROFILE";
$cmdarray[66] = "DROP PROFILE";
$cmdarray[67] = "ALTER PROFILE";
$cmdarray[68] = "DROP PROCEDURE";
$cmdarray[69] = "DROP PROCEDURE";
$cmdarray[70] = "ALTER RESOURCE COST";
$cmdarray[71] = "CREATE SNAPSHOT LOG";
$cmdarray[72] = "ALTER SNAPSHOT LOG";
$cmdarray[73] = "DROP SNAPSHOT LOG";
$cmdarray[74] = "CREATE SNAPSHOT";
$cmdarray[75] = "ALTER SNAPSHOT";
$cmdarray[76] = "DROP SNAPSHOT";
$cmdarray[79] = "ALTER ROLE";
$cmdarray[85] = "TRUNCATE TABLE";
$cmdarray[86] = "TRUNCATE CLUSTER";
$cmdarray[88] = "ALTER VIEW";
$cmdarray[91] = "CREATE FUNCTION";
$cmdarray[92] = "ALTER FUNCTION";
$cmdarray[93] = "DROP FUNCTION";
$cmdarray[94] = "CREATE PACKAGE";
$cmdarray[95] = "ALTER PACKAGE";
$cmdarray[96] = "DROP PACKAGE";
$cmdarray[97] = "CREATE PACKAGE BODY";
$cmdarray[98] = "ALTER PACKAGE BODY";
$cmdarray[99] = "DROP PACKAGE BODY";
$cmdarray[100]= "LOGON";
$cmdarray[101]= "LOGOFF";
$cmdarray[102]= "LOGOFF BY CLEANUP";
$cmdarray[103]= "SESSION REC";
$cmdarray[104]= "SYSTEM AUDIT";
$cmdarray[105]= "SYSTEM NOAUDIT";
$cmdarray[106]= "AUDIT DEFAULT";
$cmdarray[107]= "NOAUDIT DEFAULT";
$cmdarray[108]= "SYSTEM GRANT";
$cmdarray[109]= "SYSTEM REVOKE";
$cmdarray[110]= "CREATE PUBLIC SYNONYM";
$cmdarray[111]= "DROP PUBLIC SYNONYM";
$cmdarray[112]= "CREATE PUBLIC DATABASE LINK";
$cmdarray[113]= "DROP PUBLIC DATABASE LINK";
$cmdarray[114]= "GRANT ROLE";
$cmdarray[115]= "REVOKE ROLE";
$cmdarray[116]= "EXECUTE PROCEDURE";
$cmdarray[117]= "USER COMMENT";
$cmdarray[118]= "ENABLE TRIGGER";
$cmdarray[119]= "DISABLE TRIGGER";
$cmdarray[120]= "ENABLE ALL TRIGGERS";
$cmdarray[121]= "DISABLE ALL TRIGGERS";
$cmdarray[122]= "NETWORK ERROR";
$cmdarray[123]= "EXECUTE TYPE";
$cmdarray[157]= "CREATE DIRECTORY";
$cmdarray[158]= "DROP DIRECTORY";
$cmdarray[159]= "CREATE LIBRARY";
$cmdarray[160]= "CREATE JAVA";
$cmdarray[161]= "ALTER JAVA";
$cmdarray[162]= "DROP JAVA";
$cmdarray[163]= "CREATE OPERATOR";
$cmdarray[164]= "CREATE INDEXTYPE";
$cmdarray[165]= "DROP INDEXTYPE";
$cmdarray[167]= "DROP OPERATOR";
$cmdarray[168]= "ASSOCIATE STATISTICS";
$cmdarray[169]= "DISASSOCIATE STATISTICS";
$cmdarray[170]= "CALL METHOD";
$cmdarray[171]= "CREATE SUMMARY";
$cmdarray[172]= "ALTER SUMMARY";
$cmdarray[173]= "DROP SUMMARY";
$cmdarray[174]= "CREATE DIMENSION";
$cmdarray[175]= "ALTER DIMENSION";
$cmdarray[176]= "DROP DIMENSION";
$cmdarray[177]= "CREATE CONTEXT";
$cmdarray[178]= "DROP CONTEXT";
$cmdarray[179]= "ALTER OUTLINE";
$cmdarray[180]= "CREATE OUTLINE";
$cmdarray[181]= "DROP OUTLINE";
$cmdarray[182]= "UPDATE INDEXES";
$cmdarray[183]= "ALTER OPERATOR";
if($prevsql == "")
  {
  $prevsql = "---";
  }
else
  {
  $prevsql = preg_replace("/\n|\r/","<br>",$prevsql);
  }
if($currsql == "")
  {
  $currsql = "---";
  }
else
  {
  $currsql = preg_replace("/\n|\r/","<br>",$currsql);
  }
?>
<table cellspacing="1" cellpadding="2" border="0" class="datatable" width="98%" summary="Process details">
<tr class="td_odd">
  <td class="td_label">Session address:</td>
  <td><?php echo(isset($d['SADDR']) ? $d['SADDR'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">ProcessID:</td>
  <td><?php echo($pid);?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Session Identifier (SID):</td>
  <td><?php echo(isset($d['SID']) ? $d['SID'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label" title="Serial#">Session serial number:</td>
  <td><?php echo(isset($d['SERIALNR']) ? $d['SERIALNR'] : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Auditing session ID:</td>
  <td><?php echo(isset($d['AUDSID']) ? $d['AUDSID'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Owner process address:</td>
  <td><?php echo(isset($d['PADDR']) ? $d['PADDR'] : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Oracle Username (ID):</td>
  <td><?php echo(isset($d['USERNAME']) ? htmlentities(addslashes($d['USERNAME']." (".$d['USERNR'].")")) : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Schema Username (ID):</td>
  <td><?php echo(isset($d['SCHEMANAME']) ? $d['SCHEMANAME']." (".$d['SCHEMANR'].")" : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Logon time:</td>
  <td><?php echo(isset($d['LTIME']) ? $d['LTIME'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Last statement parsed:</td>
  <td><?php echo(isset($d['COMMAND']) ? $cmdarray[$d['COMMAND']]." (".$d['COMMAND'].")" : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Owner ID (if valid):</td>
  <td><?php echo(isset($d['OWNERID']) ? $d['OWNERID'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Addr. of transaction state obj.:</td>
  <td><?php echo(isset($d['TADDR']) ? $d['TADDR'] : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Address of locking wait for:</td>
  <td><?php echo(isset($d['LOCKWAIT']) ? $d['LOCKWAIT'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Status of session:</td>
  <td><?php echo(isset($d['STATUS']) ? $d['STATUS'] : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Server type:</td>
  <td><?php echo(isset($d['SERVER']) ? $d['SERVER'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">OS client user name:</td>
  <td><?php echo(isset($d['OSUSER']) ? htmlentities(addslashes($d['OSUSER'])) : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Machine name:</td>
  <td><?php echo(isset($d['MACHINE']) ? htmlentities(addslashes($d['MACHINE'])) : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Terminal:</td>
  <td><?php echo(isset($d['TERMINAL']) ? htmlentities(addslashes($d['TERMINAL'])) : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Program name:</td>
  <td><?php echo(isset($d['PROGRAM']) ? htmlentities(addslashes($d['PROGRAM'])) : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Module name:</td>
  <td><?php echo(isset($d['MODULE']) ? htmlentities(addslashes($d['MODULE'])) : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Session type:</td>
  <td><?php echo(isset($d['TYPE']) ? $d['TYPE'] : '---');?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Last SQL query:</td>
  <td><?php echo(htmlspecialchars(htmlentities(addslashes($prevsql))));?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Current SQL query:</td>
  <td><?php echo(htmlentities(addslashes($currsql)));?></td>
</tr>
<tr class="td_even">
  <td class="td_label">Current Action:</td>
  <td><?php echo(isset($d['ACTION']) ? $d['ACTION'] : '---');?></td>
</tr>
<tr class="td_odd">
  <td class="td_label">Info about Client:</td>
  <td><?php echo(isset($d['CLIENT_INFO']) ? $d['CLIENT_INFO'] : '---');?></td>
</tr>
</table>
<br>
<?php
// We check now here if current process has a long operation performing:

$longopsql=<<<EOM
SELECT OPNAME,
       TARGET,
       NVL(TARGET_DESC,'N/A') AS TDESC,
       TO_CHAR(START_TIME,'DD-Mon-YYYY HH24:MI:SS') AS SD,
       ELAPSED_SECONDS,
       SOFAR,
       TOTALWORK,
       UNITS,
       TIME_REMAINING,
       RAWTOHEX(SQL_ADDRESS) AS SQL_ADDRESS,
       SQL_HASH_VALUE
  FROM V\$SESSION_LONGOPS
 WHERE SID = :mysid
   AND SERIAL# = :myserial
EOM;
if(isset($d['ID']) && isset($d['SERIALNR']))
  {
  $sp = array('mysid' => $d['SID'],'myserial' => $d['SERIALNR']);
  $chk = $db->QueryHash("SELECT COUNT(*) FROM V\$SESSION_LONGOPS WHERE SID = :mysid AND SERIAL# = :myserial",OCI_NUM,0,$sp);
  if($chk[0])
    {
    $longop = $db->QueryHash($longopsql,OCI_ASSOC,0,$sp);
    $longsql = "";
    $sp = array('addr' => $longop['SQL_ADDRESS'], 'hash' => $longop['SQL_HASH_VALUE']);
    $db->QueryResultHash("SELECT SQL_TEXT FROM V\$SQLTEXT WHERE ADDRESS=:addr AND HASH_VALUE=:hash ORDER BY PIECE",$sp);
    while($sql = $db->FetchResult())
      {
      $longsql.= $sql['SQL_TEXT'];
      }
    $db->FreeResult();
    $progress = round(100 * $longop['SOFAR'] / $longop['TOTALWORK'],2);
    echo("<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" class=\"MAINBORDER\" width=\"98%\" summary=\"Long operation\">\n");
    echo("<caption>This session has a long operation running:</caption>\n");
    echo("<tr class=\"td_even\">\n");
    echo("  <td>Operation:</td>\n");
    echo("  <td>".$longop['OPNAME']."</td>\n");
    echo("</tr>\n");
    echo("<tr class=\"td_odd\">\n");
    echo("  <td>Target (Description):</td>\n");
    echo("  <td>".$longop['TARGET']." (".$longop['TDESC'].")</td>\n");
    echo("</tr>\n");
    echo("<tr class=\"td_even\">\n");
    echo("  <td>Starttime / Elapsed:</td>\n");
    echo("  <td>".$longop['SD']." / ".$SGLFUNC->FormatTime($longop['ELAPSED_SECONDS'])."</td>\n");
    echo("</tr>\n");
    echo("<tr class=\"td_odd\">\n");
    echo("  <td>Progress:</td>\n");
    echo("  <td>".number_format($longop['TOTALWORK'])." ".$longop['UNITS']." total, ".number_format($longop['SOFAR'])." ".$longop['UNITS']." processed (".$progress." %)</td>\n");
    echo("</tr>\n");
    echo("<tr class=\"td_even\">\n");
    echo("  <td>Est. time to finish:</td>\n");
    echo("  <td>".$SGLFUNC->FormatTime($longop['TIME_REMAINING'])."</td>\n");
    echo("</tr>\n");
    echo("<tr class=\"td_odd\">\n");
    echo("  <td>SQL Query:</td>\n");
    echo("  <td>".htmlentities(addslashes($longsql))."</td>\n");
    echo("</tr>\n");
    echo("</table>\n");
    }
  }
?>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter('<div align="center"><a href="javascript:self.close()" class="btn">Close window</a></div>');
?>
