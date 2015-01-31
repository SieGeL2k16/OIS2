<?php
/**
 * Footer for all pages, lists stats and closes database connection
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (30-May-2009)
 * $Id: footer.inc.php 2 2011-06-30 18:10:40Z siegel $
 * @filesource
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
<table cellspacing="0" cellpadding="0" border="0" width="100%" summary="Footer">
<tr>
  <td width="40%" align="left">Servertime: <?php echo($SGLFUNC->sgl_strftime('%A, %@ %b %Y %T'));?></td>
  <td width="20%" align="center">&nbsp;</td>
  <td width="40%" align="right"><?php echo($queries);?></td>
</tr>
</table>
</div>
