<?php
/**
 * Extension: Flashback.
 * Displays informations about the recycle bin, a feature available since Oracle 10g+.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (19-Jul-2011)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<script type="text/javascript" src="flashback_recyclebin.js"></script>
<div id="Recycle_Bin">
<?php
$rbinstate  = $db->Query("SELECT VALUE FROM V\$PARAMETER WHERE NAME = 'recyclebin'");
$rbininfo   = $db->Query("SELECT COUNT(*) AS ANZ, TO_CHAR(MIN(TO_DATE(CREATETIME,'YYYY-MM-DD:HH24:MI:SS')),'DD-Mon-YYYY HH24:MI:SS') AS MINCD,TO_CHAR(MAX(TO_DATE(CREATETIME,'YYYY-MM-DD:HH24:MI:SS')),'DD-Mon-YYYY HH24:MI:SS') AS MAXCD FROM DBA_RECYCLEBIN");
if(intval($rbininfo['ANZ']))
  {
  $info = sprintf("Recycle bin contains <b>%s</b> entries covering from <b>%s</b> until <b>%s</b><br>",$SGLFUNC->FormatNumber($rbininfo['ANZ']),$rbininfo['MINCD'],$rbininfo['MAXCD']);
  }
else
  {
  $info = 'Recycle bin is empty.<br>';
  }
?>
The status of the Database Recycle Bin setting is: <b><?php echo($rbinstate['VALUE']);?></b><br>
<br>
<?php
echo($info);
if(!intval($rbininfo['ANZ']))
  {
  echo("</div>\n");
  $OIS2EXT->PrintExtTabFooter();
  exit;
  }
?>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of all objects residing in Recycle Bin">
<thead><tr>
  <th>Owner</th>
  <th>Recycle bin Name (BIN)<br>Original Name (ORG)</th>
  <th>Type</th>
  <th>Oper.</th>
  <th>TS Name</th>
  <th>Drop date</th>
  <th>Drop SCN</th>
  <th>Can<br>Undrop?</th>
  <th>Can<br>Purge?</th>
  <th>Size</th>
  <th colspan="2">&nbsp;</th>
</tr></thead>
<?php
// Display the contents of the DBA_RECYCLEBIN view:
$query=<<<SQL
SELECT  rb.OWNER,
        rb.OBJECT_NAME,
        rb.ORIGINAL_NAME,
        rb.OPERATION,
        rb.TYPE,
        rb.TS_NAME,
        rb.CREATETIME,
        TO_CHAR(TO_DATE(rb.DROPTIME,'YYYY-MM-DD:HH24:MI:SS'),'DD-Mon-YYYY HH24:MI:SS') AS DT,
        rb.DROPSCN,
        rb.PARTITION_NAME,
        DECODE(rb.CAN_UNDROP,'YES',1,'NO',0) AS CU,
        DECODE(rb.CAN_PURGE,'YES',1,'NO',0) AS CP,
        rb.RELATED,
        rb.BASE_OBJECT,
        rb.PURGE_OBJECT,
        rb.SPACE,
        t.BLOCK_SIZE,
        (rb.SPACE * t.BLOCK_SIZE) AS ROWSIZE
  FROM  DBA_RECYCLEBIN rb, DBA_TABLESPACES t
 WHERE  rb.TS_NAME = t.TABLESPACE_NAME
 ORDER  BY rb.CREATETIME DESC
SQL;
$db->QueryResult($query);
$lv = 0;
// Get the images base dir from OIS2:
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
$yesno = array( '<img src="'.$OIS_IMG.'cross.png" alt="No" border="0">',
                '<img src="'.$OIS_IMG.'tick.png" alt="Yes" border="0">'
              );
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
  echo("<tr class=\"".$cl."\">\n");
  echo("  <td title=\"Owner of object\">".$d['OWNER']."</td>\n");
  printf("  <td title=\"BIN: Recycle bin name | ORG: Original object name\" class=\"rb_title\"><small>BIN:&nbsp;%s</small><br><small>ORG:&nbsp;%s</small></td>\n",$d['OBJECT_NAME'],$d['ORIGINAL_NAME']);
  echo("  <td title=\"Object type\">".$d['TYPE']."</td>\n");
  echo("  <td title=\"Type of operation\">".$d['OPERATION']."</td>\n");
  echo("  <td title=\"Tablespace name\">".$d['TS_NAME']."</td>\n");
  echo("  <td title=\"Date and time of operation\" align=\"left\">".str_replace(" ","<br>",$d['DT'])."</td>\n");
  echo("  <td title=\"System change number (SCN) of the transaction which moved the object to the recycle bin\" align=\"right\">".$d['DROPSCN']."</td>\n");
  echo("  <td title=\"If object can be undropped\" align=\"center\">".$yesno[$d['CU']]."</td>\n");
  echo("  <td title=\"If object can be purged\" align=\"center\">".$yesno[$d['CP']]."</td>\n");
  echo("  <td title=\"Object size\" align=\"right\">".$SGLFUNC->FormatSize($d['ROWSIZE'])."</td>\n");
  if($d['CU'] == 1)
    {
    printf("  <td><img src=\"%sarrow_undo.png\" alt=\"BIN\" title=\"Undrop this object\" class=\"rb_undrop\" id=\"U_%s.%s\"></td>\n",$OIS_IMG,$d['OWNER'],$d['OBJECT_NAME']);
    }
  else
    {
    printf("  <td><img src=\"%strans.gif\" alt=\"-\"></td>\n",$OIS_IMG);
    }
  if($d['CP'] == 1)
    {
    printf("  <td><img src=\"%sbin_empty.png\" alt=\"BIN\" title=\"Purge this object\" class=\"rb_purge\" id=\"P_%s.%s\"></td>\n",$OIS_IMG,$d['OWNER'],$d['OBJECT_NAME']);
    }
  else
    {
    printf("  <td><img src=\"%strans.gif\" alt=\"-\"></td>\n",$OIS_IMG);
    }
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</table>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtTabFooter();
?>
