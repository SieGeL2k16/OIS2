<?php
/**
 * Extension: Tablespace Informations.
 * Displays datafile informations for a given tablespace.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (06-Sep-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension. As we are a sub-page we have to tell navigation and GetExtInfo() our parent:
define('NAV_OVERRIDE_FILE'  , 'tablespaces.php');
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'tablespaces.php');

// Get parameter
$sp['tsname'] = (isset($_GET['TS']))  ? strip_tags($_GET['TS']) : '';
$is_temp      = (isset($_GET['TMP'])) ? strip_tags($_GET['TMP']) : 'NO';
if($is_temp == 'YES')
  {
  $table = 'DBA_TEMP_FILES';
  }
else
  {
  $table = 'DBA_DATA_FILES';
  }
$addHeader = '';
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
if($sp['tsname'] == '')
  {
  $OIS2EXT->ErrorExit('ERROR: Invalid Tablespace name retrieved - aborting!');
  }
?>
<div id="page_content">
List of datafiles assigned to tablespace <b>&quot;<?php echo($sp['tsname']);?>&quot;</b>:<br>
<br>
<a href="tablespaces.php" class="btn">Back to tablespace overview</a><br>
<br>
<?php
if($OIS2EXT->Get_DBA_Flag() == FALSE || $OIS2EXT->Get_V_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access use this plugin - aborting!');
  }
?>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="Datafile(s) informations">
<thead><tr>
  <th>File#</th>
  <th>Rel.<br>FNO</th>
  <th>Datafile</th>
  <th>Size<br>total</th>
  <th>Size<br>used</th>
  <th>Size<br>free</th>
  <th>Status</th>
  <th>Auto-<br>grow</th>
  <th>Max.<br>size</th>
  <th>Increment<br>by</th>
</tr></thead>
<tbody>
<?php
$blocksize = $db->Query("SELECT VALUE FROM V\$PARAMETER WHERE NAME='db_block_size'",OCI_NUM);
$myquery = <<<EOM
SELECT  d.FILE_ID,
        d.FILE_NAME,
        d.BYTES,
        LOWER(STATUS) AS STATUS,
        d.AUTOEXTENSIBLE,
        d.MAXBYTES,
        d.INCREMENT_BY,
        d.RELATIVE_FNO,
        (SELECT SUM(f.BYTES) FROM DBA_FREE_SPACE f WHERE f.FILE_ID=d.FILE_ID) as S1
  FROM  $table d
  WHERE d.TABLESPACE_NAME=:tsname
  ORDER BY d.RELATIVE_FNO
EOM;
$db->QueryResultHash($myquery,$sp);
$lv=0;
// Get the images base dir from OIS2:
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
$yesno = array( 'Yes' => '<img src="'.$OIS_IMG.'tick.png" alt="Yes" border="0" title="Yes">',
                'No'  => '<img src="'.$OIS_IMG.'cross.png" alt="No" border="0" title="No">'
              );
while($f = $db->FetchResult())
  {
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td align=\"center\">".$f['FILE_ID']."</td>\n");
  echo("  <td align=\"center\">".$f['RELATIVE_FNO']."</td>\n");
  echo("  <td align=\"left\">".$f['FILE_NAME']."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatSize($f['BYTES'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatSize($f['BYTES']-$f['S1'])."</td>\n");
  echo("  <td align=\"right\">".$SGLFUNC->FormatSize($f['S1'])."</td>\n");
  echo("  <td align=\"left\">".UCFirst($f['STATUS'])."</td>\n");
  echo("  <td align=\"center\">".$yesno[UCFirst(StrTolower($f['AUTOEXTENSIBLE']))]."</td>\n");
  if($f['AUTOEXTENSIBLE']=='YES')
    {
    echo("  <td align=\"right\">".$SGLFUNC->FormatSize($f['MAXBYTES'])."</td>\n");
    echo("  <td align=\"left\">".$SGLFUNC->FormatSize($f['INCREMENT_BY'] * $blocksize[0])."</td>\n");
    }
  else
    {
    echo("  <td align=\"right\">---</td>\n");
    echo("  <td align=\"right\">---</td>\n");
    }
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>
<br>
<a href="tablespaces.php" class="btn">Back to tablespace overview</a>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
