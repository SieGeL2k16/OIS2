<?php
/**
 * Navigation of OIS2.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (30-May-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
if(!defined('NAV_OVERRIDE_FILE') || NAV_OVERRIDE_FILE == '')
  {
  $script = basename($_SERVER['SCRIPT_FILENAME']);
  }
else
  {
  $script = NAV_OVERRIDE_FILE;
  }
if($script == 'mainindex.php')
  {
  $homeselect = ' class="nav_on"';
  }
else
  {
  $homeselect = '';
  }
?>
<div id="page_navigation">
<fieldset><legend>Navigation</legend>
<ul>
  <li>&raquo;&nbsp;<a href="<?php echo(OIS_INSTALL_URL);?>/mainindex.php"<?php echo($homeselect);?>>Home</a></li>
<?php
foreach($GLOBALS['OIS_EXTENSIONS'] AS $extname => $metadata)
  {
  if($script == basename($metadata['SCRIPTNAME']))
    {
    $class = ' class="nav_on"';
    }
  else
    {
    $class = '';
    }
  printf("<li>&raquo;&nbsp;<a href=\"%s/%s\"%s>%s</a></li>\n",OIS_INSTALL_URL,$metadata['SCRIPTNAME'],$class,$metadata['MENUNAME']);
  }
?>
  <li>&raquo;&nbsp;<a href="<?php echo(OIS_INSTALL_URL);?>/logout.php">Logout</a></li>
</ul>
</fieldset>
<br>
<div style="margin-left: 20px">
Refresh:<br>
<form method="GET" id="form_refresh" action="">
<select name="REFRESH" id="refresh" size="1">
  <option value="0"  >No refresh</option>
  <option value="10" >10 Seconds</option>
  <option value="30" >30 Seconds</option>
  <option value="60" >60 Seconds</option>
</select>
</form>
</div>
</div>
<script language="Javascript" type="text/javascript">
function reloadTab(tid)
  {
  $("#tabs").tabs('load',tid);
  }
var timeoutID = 0;
var currentTab = 0;
$(document).ready(function() {
  $("#tabs").bind( "tabsselect", function(event, ui) {
    if(currentTab != ui.index)
      {
      window.clearInterval(timeoutID);
      }
    currentTab = ui.index;
    $("#refresh").change();
  });
  $("#refresh").change(function() {
    if(timeoutID > 0)
      {
      window.clearTimeout(timeoutID);
      timeoutID = 0;
      }
    var myrefresh = parseInt($("#refresh").val());
    var isTab;
    if($("#tabs").length == 0)
      {
      isTab = NaN;
      }
    else
      {
      isTab = parseInt($("#tabs").tabs('length'));
      }
    if(myrefresh > 0)
      {
      document.cookie = "OIS_REFRESH="+myrefresh;
      if(isNaN(isTab) == true)
        {
        timeoutID = window.setInterval('window.location.reload()',myrefresh*1000);
        }
      else
        {
        timeoutID = window.setInterval('reloadTab('+currentTab+')',myrefresh*1000);
        }
      }
    else
      {
      document.cookie = "OIS_REFRESH=0";
      if(timeoutID > 0)
        {
        window.clearInterval(timeoutID);
        timeoutID = 0;
        }
      if(isNaN(isTab) == true)
        {
        window.location.reload();
        }
      else
        {
        reloadTab(currentTab);
        }
      }
  });
  var mycookies = document.cookie.split(";");
  for(i = 0; i < mycookies.length; i++)
    {
    var chk = mycookies[i].split("=");
    if(chk[0].replace(/ /,"") == "OIS_REFRESH" && parseInt(chk[1]) > 0)
      {
      $("#refresh").val(chk[1]);
      $("#refresh").change();
      }
    }
});
</script>