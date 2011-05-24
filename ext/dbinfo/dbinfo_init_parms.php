<?php
/**
 * Extension: Database Informations.
 * This page displays all initialisation parameters grouped by default and non-default settings.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (05-Sep-2009)
 * $Id: dbinfo_init_parms.php,v 1.4 2010/12/20 23:27:05 siegel Exp $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
?>
<div id="Initialisation_parameter">
<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="init_nondefault" summary="Non-default init parameter">
<caption>Non-default initialisation parameter</caption>
<tbody>
<?php
$db->QueryResult("SELECT NAME,VALUE,DESCRIPTION FROM V\$PARAMETER WHERE ISDEFAULT='FALSE' ORDER BY NAME");
$lv=0;
while($d = $db->FetchResult(OCI_NUM))
  {
  if($lv % 2)
    {
    $myback = 'td_even';
    }
  else
    {
    $myback = 'td_odd';
    }
  echo("<tr class=\"".$myback."\" title=\"".$d[2]."\">\n");
  echo("  <td>".$d[0].":</td>\n");
  if(!preg_match("/\D/",$d[1]))
    {
    $mycontent = $SGLFUNC->FormatNumber($d[1]);
    }
  else
    {
    $mycontent = preg_replace("/,/",",<br>",$d[1]);
    }
  echo("  <td>".wordwrap($mycontent,78,"\n",true)."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
?>
</tbody>
</table>

<table cellspacing="1" cellpadding="4" border="0" class="datatable" id="init_default" summary="Non-default init parameter">
<caption>Default initialisation parameter</caption>
<tbody>
<?php
$db->QueryResult("SELECT NAME,VALUE,DESCRIPTION FROM V\$PARAMETER WHERE ISDEFAULT='TRUE' ORDER BY NAME");
$lv=0;
while($d = $db->FetchResult(OCI_NUM))
  {
  if($lv % 2)
    {
    $myback = 'td_even';
    }
  else
    {
    $myback = 'td_odd';
    }
  echo("<tr class=\"".$myback."\" title=\"".$d[2]."\">\n");
  echo("  <td>".$d[0].":</td>\n");
  if(!preg_match("/\D/",$d[1]))
    {
    $mycontent = $SGLFUNC->FormatNumber($d[1]);
    }
  else
    {
    $mycontent = preg_replace("/,/",",<br>",$d[1]);
    }
  echo("  <td>".wordwrap($mycontent,78,"\n",true)."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
$db->FreeResult();
$db->Disconnect();
?>
</tbody>
</table>
<div class="clear"></div>
</div>
<?php
$OIS2EXT->PrintExtTabFooter();
?>
