<?php
/**
 * Extension: Show Users.
 * Lists all database users.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (18-Jul-2014)
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * $Id: show_users.php 10 2014-07-20 09:43:24Z siegel $
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS);
$OIS_URL = OIS_INSTALL_URL;
$addHeader = <<<EOM
<script language="Javascript" type="text/javascript">
function openPopUp(uid)
  {
  var myurl = 'show_user_details.php?UID='+uid;
  var wuid  = 'USER'+uid;
  var mywin = window.open(myurl,wuid,'width=500, height=500,scrollbars=yes');
  }
</script>
<style type="text/css">
.img_warn {
padding-left  : 4px;
padding-right : 4px;
}
</style>
EOM;
$js_ready=<<<EOM
\$("#tabs").tabs({beforeLoad: function( event, ui ) { \$(ui.panel).html('Loading...'); }});
$(".uname_catcher").live('click',function() {
  var uid = $(this).attr('id').replace(/(UN_)(.*)/,"$2");
  openPopUp(uid);
});
$(".uname_catcher").live('mouseover',function() {
  $(this).css('cursor','pointer');
});
EOM;
// Now we call the class method "PrintExtHeader()", which dumps out the complete HTML header, so you put your stuff in the <div> </div> part only:
$OIS2EXT->Add_JS_Ready_Call($js_ready);
$OIS2EXT->PrintExtHeader($extdata['EXTENSION'],$addHeader);
?>
<div id="page_content">
<?php
if($OIS2EXT->Get_DBA_Flag() == FALSE)
  {
  $OIS2EXT->ErrorExit('Error: You have not the necessary privileges to access V$ views - aborting!');
  }
?>
<div id="tabs">
<ul>
  <li><a href="show_users_list.php"><span>All users</span></a></li>
  <li><a href="show_users_roles.php"><span>Roles</span></a></li>
  <li><a href="show_users_profiles.php"><span>Profiles</span></a></li>
</ul>
</div>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter();
?>
