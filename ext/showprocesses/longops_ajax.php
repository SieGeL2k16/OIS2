<?php
/**
 * AJAX backend for Show Processes -> Long operations.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (13-Jul-2010)
 * $Id: longops_ajax.php 4 2011-09-12 19:14:40Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
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
 WHERE TIME_REMAINING > 0
 ORDER BY START_TIME
EOM;
$retdata = array();
$db->QueryResult($longopsql);
while($d = $db->FetchResult())
  {
  $retdata[] = $d;
  }
$db->FreeResult();
$db->Disconnect();
echo(json_encode($retdata));
?>
