<?php
/**
 * Extension: Show Process details.
 * Displays detailed informations for a given process id.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (12-Sep-2011)
 * $Id: show_processes_details.php 5 2011-09-12 20:17:05Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
require_once('command_list.inc.php');

$sid    = (isset($_GET['SID']))     ? strip_tags($_GET['SID']) : '';
$serial = (isset($_GET['SERIAL']))  ? strip_tags($_GET['SERIAL']) : '';

// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'show_processes.php');
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$OIS2EXT->PrintExtHeader('Show process details for SID='.$sid.' AND SERIAL#='.$serial,'',TRUE);
?>
<div id="fullpage_content">
<?php
$addq = '';
if($OIS2EXT->Get_Oracle_Version() >= 9)
  {
  $addq.= 'CURRENT_QUEUE_DURATION,CLIENT_IDENTIFIER,';
  }
if($OIS2EXT->Get_Oracle_Version() >=10)
  {
  $addq.= 'BLOCKING_SESSION_STATUS,BLOCKING_INSTANCE,BLOCKING_SESSION,SEQ#,EVENT#,EVENT,P1,P1TEXT,P2,P2TEXT,P3,P3TEXT,WAIT_CLASS_ID,WAIT_CLASS#,WAIT_CLASS,WAIT_TIME,SECONDS_IN_WAIT,STATE,SERVICE_NAME,SQL_TRACE,SQL_TRACE_WAITS,SQL_TRACE_BINDS,';
  }
if($OIS2EXT->Get_Oracle_Version() >=11)
  {
  $addq.= 'SQL_TRACE_PLAN_STATS,SESSION_EDITION_ID,RAWTOHEX(CREATOR_ADDR) AS CREATOR_ADDR,CREATOR_SERIAL#,ECID,';
  }
$myquery =<<<EOM
SELECT RAWTOHEX(SADDR) AS SADDR,
       SID,
       SERIAL#,
       NVL(AUDSID,0) AS AUDSID,
       RAWTOHEX(PADDR) AS PADDR,
       USER#,
       USERNAME,
       COMMAND,
       OWNERID,
       NVL(TADDR,'---') AS TADDR,
       NVL(LOCKWAIT,'---') AS LOCKWAIT,
       STATUS,
       SERVER,
       SCHEMA#,
       SCHEMANAME,
       OSUSER,
       PROCESS,
       MACHINE,
       NVL(TERMINAL,'---') AS TERMINAL,
       PROGRAM,
       TYPE,
       NVL(MODULE,'---') AS MODULE,
       NVL(ACTION,'---') AS ACTION,
       NVL(CLIENT_INFO,'---') AS CLIENT_INFO,
       TO_CHAR(LOGON_TIME,'DD-Mon-YYYY HH24:MI:SS') AS LOGON_TIME,
       LAST_CALL_ET,
       FIXED_TABLE_SEQUENCE,
       ROW_WAIT_OBJ#,
       ROW_WAIT_FILE#,
       ROW_WAIT_BLOCK#,
       ROW_WAIT_ROW#,
       FAILOVER_TYPE,
       FAILOVER_METHOD,
       FAILED_OVER,
       RESOURCE_CONSUMER_GROUP,
       PDML_STATUS,
       PDDL_STATUS,
       PQ_STATUS,
       $addq
       RAWTOHEX(SQL_ADDRESS) AS SQL_ADDRESS,
       SQL_HASH_VALUE,
       RAWTOHEX(PREV_SQL_ADDR) AS PREV_SQL_ADDR,
       PREV_HASH_VALUE
       FROM V\$SESSION
 WHERE SID=:mysid
   AND SERIAL#=:myserial
EOM;
$dbparm = array('mysid' => $sid,'myserial' => $serial);
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
$ownerid = '';
if($d['OWNERID']=='2147483644')
  {
  $d['OWNERID'] = 'invalid';
  }
$d['Previous SQL'] = $prevsql;
$d['Current SQL'] = $currsql;
if(isset($d['COMMAND'])==TRUE && isset($cmdarray[$d['COMMAND']])===TRUE)
  {
  $d['COMMAND'].=' ('.$cmdarray[$d['COMMAND']].')';
  }
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" width="98%" summary="Process details">
<tbody>
<?php
$lv = 0;
foreach($d as $key => $val)
  {
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }
  if($val == '') $val = '---';
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td>".htmlentities(UCWords(StrToLower(str_replace("_"," ",$key))),ENT_COMPAT,'utf-8')."</td>\n");
  echo("  <td>".htmlentities($val,ENT_COMPAT,'utf-8')."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
?>
</tbody>
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
   AND TIME_REMAINING > 0
EOM;
if(isset($d['SID']) && isset($d['SERIALNR']))
  {
  $sp = array('mysid' => $d['SID'],'myserial' => $d['SERIALNR']);
  $chk = $db->QueryHash("SELECT COUNT(*) FROM V\$SESSION_LONGOPS WHERE SID = :mysid AND SERIAL# = :myserial AND TIME_REMAINING > 0",OCI_NUM,0,$sp);
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
    echo("<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" class=\"datatable\" width=\"98%\" summary=\"Long operation\">\n");
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
