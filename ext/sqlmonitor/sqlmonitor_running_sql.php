<?php
/**
 * Extension: SQL Monitor.
 * Lists all currently running SQL statements together with statistics on screen.
 * @package OIS2
 * @subpackage Plugin
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 0.1 (18-Jul-2014)
 * $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * NOTE: The orginal SQL statements are taken from Ask Tom script "show_sql.sql", see: https://asktom.oracle.com/pls/apex/f?p=100:11:0::::P11_QUESTION_ID:767025833873
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
/**
 * Returns the concatinated SQL text.
 * Normally this should be done via PL/SQL function or LISTAGG() in 11gR2, but we do not want to alter the connected DB,
 * so we do this as PHP function...not wise from performance point, but...:)
 */
function FetchSQL(&$db,$sqladdr,$sqlhashval)
  {
  $RESULT = '';
  $sp     = array('sqladdr' => $sqladdr, 'hav' => $sqlhashval);
  $SQL    = "SELECT SQL_TEXT FROM V\$SQLTEXT_WITH_NEWLINES WHERE ADDRESS = :sqladdr AND HASH_VALUE = :hav ORDER BY PIECE";
  $res = $db->QueryResultHash($SQL,$sp);
  while($d = $db->FetchResult(OCI_ASSOC,$res))
    {
    $RESULT.=$d['SQL_TEXT'];
    }
  $db->FreeResult($res);
  return(str_replace(chr(0),'',$RESULT));
  }
?>
<script type="text/javascript">
function refreshTab()
  {
  var current_index = $("#tabs").tabs("option","active");
  $("#tabs").tabs('load',current_index);
  }
$(document).ready(function() {
  setTimeout('refreshTab()',2000);

});
</script>
<div id="Running_SQL">

<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of SQL statements currently running">
<caption>Currently executed SQL (refreshed every 2 seconds)</caption>
<thead><tr>
  <th>USERNAME</th>
  <th>SID</th>
  <th>SERIAL#</th>
  <th>PROCESS</th>
  <th>PROGRAM</th>
  <th>LOGON TIME</th>
  <th>CURRENT TIME</th>
  <th>RUNNING SEC.</th>
  <th>SQL</th>
</tr></thead>
<tbody>
<?php
$SQL=<<<EOM
SELECT  USERNAME,
        SID,
        SERIAL#,
        PROCESS,
        PROGRAM,
        TO_CHAR(LOGON_TIME,' DD.MM.YYYY HH24:MI:SS') LOGON_TIME,
        TO_CHAR(SYSDATE,' DD.MM.YYYY HH24:MI:SS') CURRENT_TIME,
        RAWTOHEX(SQL_ADDRESS) AS SQL_ADDR,
        SQL_HASH_VALUE,
        LAST_CALL_ET
  FROM  V\$SESSION
  WHERE STATUS = 'ACTIVE'
    AND RAWTOHEX(SQL_ADDRESS) <> '00'
    AND USERNAME IS NOT NULL
  ORDER BY LAST_CALL_ET
EOM;
$db->QueryResult($SQL);
$lv=0;
while($d = $db->FetchResult())
  {
  if($lv % 2)
    {
    $cl = 'td_odd';
    }
  else
    {
    $cl = 'td_even';
    }
  printf("<tr class=\"%s\">\n",$cl);
  printf("  <td>%s</td>\n",$d['USERNAME']);
  printf("  <td>%s</td>\n",$d['SID']);
  printf("  <td>%s</td>\n",$d['SERIAL#']);
  printf("  <td>%s</td>\n",$d['PROCESS']);
  printf("  <td>%s</td>\n",$d['PROGRAM']);
  printf("  <td>%s</td>\n",$d['LOGON_TIME']);
  printf("  <td>%s</td>\n",$d['CURRENT_TIME']);
  printf("  <td>%s</td>\n",$d['LAST_CALL_ET']);
  printf("  <td>%s</td>\n",htmlentities(FetchSQL($db,$d['SQL_ADDR'],$d['SQL_HASH_VALUE'])));
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
$OIS2EXT->PrintExtTabFooter();
?>
