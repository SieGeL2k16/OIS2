<?php
/**
 * Extension: ShowProcesses.
 * Gives an overview about processes and sessions including top lists.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (31-Aug-2011)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Process_Overview">
<?php
$procset = $db->Query("SELECT (SELECT VALUE FROM V\$PARAMETER WHERE UPPER(NAME) = 'PROCESSES') AS MAXPROC, (SELECT COUNT(*) FROM V\$SESSION) AS CURRPROC FROM DUAL");
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
$tsort = array(
  'PROCESS' => 's.PROCESS',
  'SID'     => 's.SID',
  'SERIAL'  => 's.SERIAL#',
  'ORAUSER' => 's.USERNAME',
  'OSUSER'  => 's.OSUSER',
  'STATUS'  => 's.STATUS',
  'MACHINE' => 's.MACHINE',
  'PRGNAME' => 's.MODULE',
  'LDATE'   => 's.LOGON_TIME',
  'PTYPE'   => 's.TYPE'
);
$timg = array(
  'PROCESS' => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'SID'     => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'SERIAL'  => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'ORAUSER' => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'OSUSER'  => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'STATUS'  => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'MACHINE' => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'PRGNAME' => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'LDATE'   => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif'),
  'PTYPE'   => sprintf('<img src="%s%s" border="0">',$OIS_IMG,'trans.gif')
  );
$sfield = '';
$sorter = $SGLFUNC->GetRequestParam('SORT');
$sdir   = $SGLFUNC->GetRequestParamInt('SORTDIR');
if($sdir == 0)
  {
  $sqldir = ' DESC';
  $ndir   = 1;
  $simg   = 'bullet_arrow_down.png';
  }
else
  {
  $sqldir = ' ASC';
  $ndir   = 0;
  $simg   = 'bullet_arrow_up.png';
  }
if(isset($tsort[$sorter])===TRUE)
  {
  $sfield = 'ORDER BY '.$tsort[$sorter].$sqldir;
  $timg[$sorter] = sprintf('<img src="%s%s" border="0">',$OIS_IMG,$simg);
  }
?>
<div>
Click on an entry to view details | <?php printf("Processes in usage: <b>%s</b> | Max. allowed processes: <b>%s</b>",$SGLFUNC->FormatNumber($procset['CURRPROC']),$SGLFUNC->FormatNumber($procset['MAXPROC']));?>
</div>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of currently connected Oracle processes and sessions">
<caption><small>BOLD => Long running processes</small></caption>
<thead><tr>
  <th><a href="show_processes.php?SORT=PROCESS&amp;SORTDIR=<?php echo($ndir);?>">PROCESS<?php echo($timg['PROCESS']);?></a></th>
  <th><a href="show_processes.php?SORT=SID&amp;SORTDIR=<?php echo($ndir);?>">SID<?php echo($timg['SID']);?></a></th>
  <th><a href="show_processes.php?SORT=SERIAL&amp;SORTDIR=<?php echo($ndir);?>">SERIAL#<?php echo($timg['SERIAL']);?></a></th>
  <th><a href="show_processes.php?SORT=ORAUSER&amp;SORTDIR=<?php echo($ndir);?>">ORACLE USER<?php echo($timg['ORAUSER']);?></a></th>
  <th><a href="show_processes.php?SORT=OSUSER&amp;SORTDIR=<?php echo($ndir);?>">OS USER<?php echo($timg['OSUSER']);?></a></th>
  <th><a href="show_processes.php?SORT=STATUS&amp;SORTDIR=<?php echo($ndir);?>">STATUS<?php echo($timg['STATUS']);?></a></th>
  <th><a href="show_processes.php?SORT=MACHINE&amp;SORTDIR=<?php echo($ndir);?>">MACHINE<?php echo($timg['MACHINE']);?></a></th>
  <th><a href="show_processes.php?SORT=PRGNAME&amp;SORTDIR=<?php echo($ndir);?>">PROGRAM / MODULE NAME<?php echo($timg['PRGNAME']);?></a></th>
  <th><a href="show_processes.php?SORT=LDATE&amp;SORTDIR=<?php echo($ndir);?>">LOGIN DATE<?php echo($timg['LDATE']);?></a></th>
  <th><a href="show_processes.php?SORT=PTYPE&amp;SORTDIR=<?php echo($ndir);?>">PROCESS TYPE<?php echo($timg['PTYPE']);?></a></th>
</tr></thead>
<tbody>
<?php
$myquery = <<<EOM
SELECT s.PROCESS,
       s.USERNAME,
       s.OSUSER,
       s.STATUS,
       s.MACHINE,
       s.PROGRAM,
       TO_CHAR(s.LOGON_TIME,'DD-Mon-YYYY HH24:MI:SS') AS LTIME,
       s.TYPE,
       NVL(s.MODULE,'---') AS MODULE,
       s.SID,
       s.SERIAL#,
       (SELECT COUNT(*) FROM V\$SESSION_LONGOPS sl WHERE sl.SID = s.SID AND sl.SERIAL# = s.SERIAL# AND TIME_REMAINING > 0) AS ISLONG
       FROM V\$SESSION s
 $sfield
EOM;
$db->QueryResult($myquery);
$lv=0;
$link = '<a href="javascript:openPopUp(\'%s\',\'%s\')" title="Click to view details">%s</a>';
while($p = $db->FetchResult())
  {
  if($lv % 2)
    {
    $cl = 'td_odd';
    }
  else
    {
    $cl = 'td_even';
    }
  if($p['USERNAME'] == "") $p['USERNAME'] = "---";
  if(intval($p['ISLONG']) > 0)
    {
    $cl.='_bold';
    }
  echo("<tr class=\"".$cl."\">\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),$p['PROCESS'])."</td>\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),$p['SID'])."</td>\n");
  echo("  <td class=\"td_number\">".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),$p['SERIAL#'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),htmlentities(addslashes($p['USERNAME'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),htmlentities(addslashes($p['OSUSER'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),$p['STATUS'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),htmlentities(addslashes($p['MACHINE'])))."</td>\n");
  echo("  <td><small>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),htmlentities(addslashes($p['PROGRAM'])))."</small><br>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),htmlentities(addslashes($p['MODULE'])))."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),$p['LTIME'])."</td>\n");
  echo("  <td>".sprintf($link,URLEncode($p['SID']),URLEncode($p['SERIAL#']),UCFirst(StrToLower($p['TYPE'])))."</td>\n");
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
