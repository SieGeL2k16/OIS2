/*
 * Ajax for Long Operations display in extension "show Processes"
 * $Id$
 */
function updateLO()
  {
  var tname = $("#t_longops thead");
  $.ajax({
      cache: false,
      url: "longops_ajax.php",
      async: false,
      dataType:'json',
      success: function(json)
        {
        var buffer = '';
        var existing = $("#t_longops .td_even, #t_longops .td_odd");
        jQuery.each($(existing), function(k,v){
          $(v).remove();
        });
        if(json.length == 0)
          {
          buffer       = '<tr class="td_even">';
          buffer=buffer+ '<td colspan="7" align="center"><b>No long operations currently running.<\/b><\/td>';
          buffer=buffer+'<\/tr>';
          tname.append(buffer);
          return(false);
          }
        var cnt = 0;
        $(".lro_cap").html('Long-running operations ('+json.length+')');
        jQuery.each(json, function(json_key,json_val)
          {
          if(cnt % 2) cln = 'td_odd';
          else cln = 'td_even';
          buffer = '<tr class="'+cln+'">';
          buffer=buffer+'<td>'+json_val.OPNAME+'<\/td>';
          buffer=buffer+'<td>'+json_val.TARGET+'<\/td>';
          buffer=buffer+'<td>'+json_val.TDESC+'<\/td>';
          buffer=buffer+'<td>'+json_val.SD+'<\/td>';
          buffer=buffer+'<td align="right">'+json_val.ELAPSED_SECONDS+' sec.<\/td>';
          buffer=buffer+'<td align="right">'+json_val.SOFAR+' / '+json_val.TOTALWORK+' '+json_val.UNITS+'<\/td>';
          buffer=buffer+'<td align="right">'+json_val.TIME_REMAINING+' sec.<\/td>';
          buffer=buffer+'<\/tr>';
          tname.append(buffer);
          cnt++;
          });
        },
      error: function(XMLHttpRequest, textStatus, errorThrow)
        {
        alert('RETRIVAL ERROR: '+textStatus+'\nErrorThrow='+errorThrow);
        }
    });     // end ajax call
  }
$(document).ready(function() {
  updateLO();
});
window.setInterval("updateLO()",3000);