<?php
/**
 * Extension: Database Informations.
 * This page displays statistics about the 11g Result cache.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (12-May-2011)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$binds = NULL;

$RES_TYPE   = $SGLFUNC->GetRequestParam('RES_TYPE');
$RES_STATE  = $SGLFUNC->GetRequestParam('RES_STATE');
$RES_NS     = $SGLFUNC->GetRequestParam('RES_NS','-');
?>
<div id="Result_Cache_overview">
<?php
$status = $db->Query("SELECT DBMS_RESULT_CACHE.STATUS AS ST FROM DUAL");
printf("Server Result Cache Status: <strong>%s</strong><br>\n<br>\n",$status['ST']);
$th_opts = array('CAPTION' => 'Result Cache Statistics','TABLE_CLASS' => 'datatable tbl_float');
$OIS2EXT->RenderQuery("SELECT * FROM V\$RESULT_CACHE_STATISTICS",$binds,$th_opts);
?>
<?php
$th_opts = array('CAPTION' => 'Result Cache Initialisation Parameter');
$OIS2EXT->RenderQuery("SELECT NAME,DISPLAY_VALUE,DESCRIPTION FROM V\$PARAMETER WHERE NAME LIKE '%result_cache%'",$binds,$th_opts);
?>
<div class="clear"></div>
<div id="result_cache_objects">
<form method="GET" action="<?php echo(OIS_INSTALL_URL);?>/ext/dbinfo/dbinfo.php#Result_Cache_overview">
<label for="filter">Filter:</label>
<select name="RES_TYPE" id="res_type" size="1">
<option value="">-- All types --</option>
<?php
$db->QueryResult("SELECT TYPE FROM V\$RESULT_CACHE_OBJECTS GROUP BY TYPE ORDER BY TYPE");
while($d = $db->FetchResult())
  {
  if($RES_TYPE == $d['TYPE']) $mysel = ' SELECTED';
  else $mysel = '';
  printf("  <option value=\"%s\"%s>%s</option>\n",$d['TYPE'],$mysel,$d['TYPE']);
  }
$db->FreeResult();
?>
</select>
<select name="RES_STATE" id="res_state" size="1">
<option value="">-- All states --</option>
<?php
$db->QueryResult("SELECT STATUS FROM V\$RESULT_CACHE_OBJECTS GROUP BY STATUS ORDER BY STATUS");
while($d = $db->FetchResult())
  {
  if($RES_STATE == $d['STATUS']) $mysel = ' SELECTED';
  else $mysel = '';
  printf("  <option value=\"%s\"%s>%s</option>\n",$d['STATUS'],$mysel,$d['STATUS']);
  }
$db->FreeResult();
?>
</select>
<select name="RES_NS" id="res_ns" size="1">
<option value="-">-- All Namespaces --</option>
<?php
$db->QueryResult("SELECT NAMESPACE FROM V\$RESULT_CACHE_OBJECTS GROUP BY NAMESPACE ORDER BY NAMESPACE");
while($d = $db->FetchResult())
  {
  if($RES_NS == $d['NAMESPACE']) $mysel = ' SELECTED';
  else $mysel = '';
  printf("  <option value=\"%s\"%s>%s</option>\n",$d['NAMESPACE'],$mysel,$d['NAMESPACE']);
  }
$db->FreeResult();
?>
</select>
<input type="submit" value="Show">
</form>
<br>
<table summary="Result Cache objects" class="datatable">
<caption>Result cache objects</caption>
<thead><tr>
  <th>Type</th>
  <th>Status</th>
  <th>Name</th>
  <th>Namespace</th>
  <th>Created</th>
  <th>SCN</th>
  <th>Row Cnt</th>
  <th>Cache ID</th>
</tr></thead>
<tbody>
<?php
$lv = 0;
$fbind = array();
$WHERE = '';
if($RES_TYPE != '')
  {
  $WHERE.=' AND TYPE=:t';
  $fbind['t'] = $RES_TYPE;
  }
if($RES_STATE != '')
  {
  $WHERE.=' AND STATUS=:s';
  $fbind['s'] = $RES_STATE;
  }
if($RES_NS != '-')
  {
  $WHERE.=' AND NAMESPACE=:n';
  $fbind['n'] = $RES_NS;
  }
if($WHERE != '')
  {
  $WHERE = 'WHERE '.substr($WHERE,5);
  }
$SQL = "SELECT TYPE,STATUS,NAME,NAMESPACE,TO_CHAR(CREATION_TIMESTAMP,'DD-Mon-YYYY HH24:MI:SS') AS CD,SCN,ROW_COUNT,CACHE_ID FROM V\$RESULT_CACHE_OBJECTS ".$WHERE." ORDER BY CREATION_TIMESTAMP DESC";
if($WHERE == '')
  {
  $db->QueryResult($SQL);
  }
else
  {
  $db->QueryResultHash($SQL,$fbind);
  }
while($d = $db->FetchResult())
  {
  if($lv % 2) $class = 'td_even';
  else $class = 'td_odd';
  echo("<tr class=\"".$class."\">\n");
  printf("  <td>%s</td>\n",$d['TYPE']);
  printf("  <td>%s</td>\n",$d['STATUS']);
  printf("  <td><small>%s</small></td>\n",$d['NAME']);
  printf("  <td>%s</td>\n",$d['NAMESPACE']);
  printf("  <td>%s</td>\n",$d['CD']);
  printf("  <td class=\"td_number\">%s</td>\n",$d['SCN']);
  printf("  <td class=\"td_number\">%s</td>\n",$d['ROW_COUNT']);
  printf("  <td>%s</td>\n",$d['CACHE_ID']);
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
<?php printf("Listed %s row(s)\n",$SGLFUNC->FormatNumber($lv));?>
</div>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
