<?php
/**
 * Extension: Database Informations.
 * Displays informations about the control file.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (03-Aug-2010)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Control_file">
<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="db_control" summary="Control file informations">
<caption>Control file(s)</caption>
<thead><tr>
  <th>Name</th>
  <th>Status</th>
  <th>Created in<br>Flashback?</th>
  <th>Filesize</th>
</tr></thead>
<tbody>
<?php
$control = array();
$db->QueryResult("SELECT * FROM V\$CONTROLFILE");
$lv = 0;
while($d = $db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_even';
    }
  else
    {
    $myback = 'td_odd';
    }
  if($d['STATUS'] == "") $d['STATUS'] = 'VALID';
  if(isset($d['IS_RECOVERY_DEST_FILE']) == FALSE)
    {
    $d['IRDF'] = '---';
    }
  else
    {
    $d['IRDF'] = $d['IS_RECOVERY_DEST_FILE'];
    }
  if(isset($d['BLOCK_SIZE']) == FALSE)
    {
    $d['FSIZE'] = 'N/A';
    }
  else
    {
    $d['FSIZE'] = $SGLFUNC->FormatSize(($d['BLOCK_SIZE'] * $d['FILE_SIZE_BLKS']));
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td>".$d['NAME']."</td>\n");
  echo("  <td>".$d['STATUS']."</td>\n");
  echo("  <td>".$d['IRDF']."</td>\n");
  echo("  <td>".$d['FSIZE']."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="db_control" summary="Control file record section informations">
<caption>Control file record sections</caption>
<thead><tr>
  <th>Type</th>
  <th>Record size</th>
  <th>Records total</th>
  <th>Records used</th>
  <th>First index</th>
  <th>Last index</th>
  <th>Record ID</th>
</tr></thead>
<tbody>
<?php
$db->QueryResult("SELECT * FROM V\$CONTROLFILE_RECORD_SECTION");
$lv = 0;
// TYPE,RECORD_SIZE,RECORDS_TOTAL,RECORDS_USED,FIRST_INDEX,LAST_INDEX,LAST_RECID
while($d = $db->FetchResult())
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
  echo("  <td>".$d['TYPE']."</td>\n");
  echo("  <td class=\"td_number\">".$d['RECORD_SIZE']."</td>\n");
  echo("  <td class=\"td_number\">".$d['RECORDS_TOTAL']."</td>\n");
  echo("  <td class=\"td_number\">".$d['RECORDS_USED']."</td>\n");
  echo("  <td class=\"td_number\">".$d['FIRST_INDEX']."</td>\n");
  echo("  <td class=\"td_number\">".$d['LAST_INDEX']."</td>\n");
  echo("  <td class=\"td_number\">".$d['LAST_RECID']."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
