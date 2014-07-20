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
      isTab = parseInt($("#tabs li").length);
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
