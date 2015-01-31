<?php
/**
 * Footer for all pages, lists stats and closes database connection.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.03 (31-Jan-2015)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
if(isset($GLOBALS['db']) && is_object($GLOBALS['db']))
  {
  $q      = 'Queries: '.$GLOBALS['db']->GetQueryCount().' / ';
  $mytime = ' title="Oracle time: '.round($GLOBALS['db']->GetQueryTime(),3).' sec."';
  $GLOBALS['db']->Disconnect();
  }
else
  {
  $q      = '';
  $mytime = '';
  }
if(isset($SGLFUNC)==FALSE || is_object($SGLFUNC) == FALSE)
  {
  $SGLFUNC = new sgl_functions();
  }
$et = $SGLFUNC->getmicrotime();
$queries = "<small".$mytime.">OIS v".SITE_VERSION." / ".$q."Exec time: ".round($et-$GLOBALS['start_time'],4)."sec.</small>";
?>
<div id="page_footer">
<table id="tbl_footer">
<tr>
  <td class="tbl_footer_l">Servertime: <?php echo($SGLFUNC->sgl_strftime('%A, %@ %b %Y %T'));?></td>
  <td class="tbl_footer_m">&nbsp;</td>
  <td class="tbl_footer_r"><?php echo($queries);?></td>
</tr>
</table>
</div>
