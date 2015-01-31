<?php
/**
 * Extension: ShowProcesses->LongOps.
 * Displays all current long running operations.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (31-Aug-2011)
 * $Id: show_processes_longops.php 9 2013-07-20 06:34:13Z siegel $
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$OIS_URL = OIS_INSTALL_URL;
?>
<script language="Javascript" type="text/javascript" src="longops.js"></script>
<div id="LongOps">
This tab continously monitors the V$SESSION_LONGOPS View for entries with TIME_REMAINING > 0. Data will updated on a 3 second interval.<br>
<br>
<table cellspacing="1" cellpadding="4" border="0" class="datatable" summary="List of sessions running as long operations." id="t_longops">
<caption class="lro_cap">Long-running operations</caption>
<thead>
<tr>
  <th>Operation</th>
  <th>Target</th>
  <th>Target Description</th>
  <th>Start date</th>
  <th>Elapsed<br>seconds</th>
  <th>Progress<br>Total</th>
  <th>Time<br>remaining</th>
</tr>
</thead>

</table>

</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtTabFooter();
?>
