<?php
/**
 * Extension: Show Users.
 * Lists all database users.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (31-Aug-2009)
 * $Id: show_users_list.php,v 1.1 2010/07/20 21:32:09 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="show_users_list">
<?php
$ucnt = $db->Query("SELECT COUNT(*) AS ANZ FROM ALL_USERS");
if($OIS2EXT->Get_DBA_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access DBA_* views - aborting!');
  }
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of users for the current database">
<caption><strong><?php echo($SGLFUNC->FormatNumber($ucnt['ANZ']));?></strong> users found in database - Click on an username to view details</caption>
<thead><tr>
  <th>ID</th>
  <th>Name</th>
  <th>Creation date</th>
  <th>Status</th>
  <th>Expiry date</th>
  <th>Tablespace</th>
  <th>Temp. TS</th>
  <th>Profile</th>
</tr></thead>
<tbody>
<?php
flush();
$myquery=<<<EOM
SELECT u.USER_ID,
       u.USERNAME,
       TO_CHAR(u.CREATED,'DD-Mon-YYYY HH24:MI:SS') AS CD,
       LOWER(u.ACCOUNT_STATUS) AS ASTATE,
       u.DEFAULT_TABLESPACE,
       u.TEMPORARY_TABLESPACE,
       LOWER(u.PROFILE) AS PROF,
       NVL(TO_CHAR(u.EXPIRY_DATE,'DD-Mon-YYYY HH24:MI:SS'),'---') AS ED
 FROM  DBA_USERS u
 ORDER BY u.USERNAME
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
  if($r['DEFAULT_TABLESPACE'] == 'SYSTEM' && $r['USERNAME'] != 'SYS' && $r['USERNAME'] != 'SYSTEM')
    {
    $img = sprintf('<img src="%swarn.gif" border="0" alt="Warning!" title="User has default tablespace set to SYSTEM !!" class="img_warn">',$OIS2EXT->Get_OIS2_Image_URL());
    }
  else
    {
    $img = '';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"right\">".$r['USER_ID']."</td>\n");
  echo("  <td align=\"left\" class=\"uname_catcher\" id=\"UN_".$r['USER_ID']."\">".$r['USERNAME']."</td>\n");
  echo("  <td align=\"left\">".$r['CD']."</td>\n");
  echo("  <td align=\"left\">".htmlentities(UCWords($r['ASTATE']))."</td>\n");
  echo("  <td align=\"left\">".$r['ED']."</td>\n");
  echo("  <td align=\"left\">".$r['DEFAULT_TABLESPACE']."".$img."</td>\n");
  echo("  <td align=\"left\">".$r['TEMPORARY_TABLESPACE']."</td>\n");
  echo("  <td align=\"left\">".UCWords($r['PROF'])."</td>\n");
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
