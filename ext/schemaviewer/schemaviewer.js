/* Javascript functionality for Schemaviewer plugin */
$(document).ready(function() {
  $('#schema').empty();
  $('#schema').append("<option value=\"\">Loading data...<\/option>");
  $.ajax({
    type: "GET",
    url: "ajax.php",
    cache:false,
    dataType:'json',
    data: {"MODE" : "SCHEMA"},
    success: function(json)
      {
      $('#schema').empty();
      $('#schema').append("<option value=\"\">--- Please choose ---<\/option>");
      jQuery.each(json,function(json_key,json_val)
        {
        $('#schema').append("<option value=\""+json_val+"\">"+json_val+"<\/option>");
        });
      }
    });
  $("#schema").change(function() {
    if($("#schema").val() != "")
      {
      $('#objects').empty();
      $('#objects').append("<option value=\"\">Loading data...<\/option>");
      $('#object_list').html('&nbsp;');
      $.ajax({
        type: "GET",
        url: "ajax.php",
        cache:false,
        dataType:'json',
        data: {"MODE" : "OBJECTS","USER":$("#schema").val()},
        success: function(json)
          {
          $('#objects').empty();
          $('#objects').append("<option value=\"\">--- Please choose ---<\/option>");
          $('#objects').append("<option value=\"---\">--> All Objects<\/option>");
          jQuery.each(json,function(json_key,json_val)
            {
            $('#objects').append("<option value=\""+json_val.OBJECT_TYPE+"\">"+json_val.OBJECT_TYPE+" ("+json_val.ANZ+")<\/option>");
            });
          }
        });
      }
  });
  $("#objects").change(function() {
    if($("#objects").val() == "") return;
    $("#object_list").html('Loading data, please wait...');
    $.ajax({
      type: "GET",
      url: "ajax.php",
      cache:false,
      dataType:'json',
      data: {"MODE" : "LIST","USER":$("#schema").val(),"OBJECT":$("#objects").val()},
      success: function(json)
        {
        if(json.length > 0)
          {
          var buffer = '<table summary="List of objects" class="datatable" >';
          buffer+='<thead><tr>';
          buffer+='<th>Object name</th>';
          buffer+='<th>Object type</th>';
          buffer+='<th>Object status</th>';
          buffer+='<th>Created on</th>';
          buffer+='<th>Is temp?</th>';
          buffer+='<th>Generated?</th>';
          buffer+='<\/tr><\/thead><tbody>';
          var lv    = 0;
          var mycl  = '';
          var cl2   = '';
          jQuery.each(json,function(json_key,json_val)
            {
            if(lv % 2) mycl = 'td_odd';
            else mycl = 'td_even';
            if(json_val.STATUS != 'VALID')
              {
              cl2 = ' style="color: #ff0000"';
              }
            else
              {
              cl2 = '';
              }
            buffer+="<tr class=\""+mycl+"\">";
            buffer+="<td"+cl2+" class=\"obj_name\">"+json_val.OBJECT_NAME+"<\/td>";
            buffer+="<td"+cl2+" id=\""+json_val.OBJECT_NAME+"\">"+json_val.OBJECT_TYPE+"<\/td>";
            buffer+="<td"+cl2+">"+json_val.STATUS+"<\/td>";
            buffer+="<td"+cl2+" align=\"right\" title=\"Last DDL modification on "+json_val.MD+"\">"+json_val.CD+"<\/td>";
            buffer+="<td"+cl2+" align=\"center\">"+json_val.ISTEMP+"<\/td>";
            buffer+="<td"+cl2+" align=\"center\">"+json_val.ISGEN+"<\/td>";
            buffer+="<\/tr>";
            lv++;
            });
          buffer+='<\/tbody><\/table><b>'+lv+'<\/b> row(s) shown';
          $("#object_list").html(buffer);
          }
        }
      });
  });
  $(".obj_name").live('mouseover',function(e){
    $(this).css('cursor','pointer');
  });
  $(".obj_name").live('click',function(e){
    var ismeta  = parseInt($("#has_metadata").val());
    if(!ismeta)
      {
      alert("DBMS_METADATA not available - DDL cannot be shown without it!");
      }
    else
      {
      var owner   = $("#schema").val();
      var objname = $(this).html();
      var objtype = $("#"+objname).html();
      var myurl   = "viewer_details.php?UN="+owner+"&ON="+objname+"&OT="+objtype;
      var mywin   = window.open(myurl,'OBJDETAILS','width=850,height=600,scrollbars=yes');
      }
  });
  $("#btn_schemasize").click(function() {
    var sch = $("#schema").val();
    if(sch == '')
      {
      alert("Please select first a schema !");
      $("#schema").focus();
      return;
      }
    var myurl = "viewer_overview.php?SCHEMA="+sch;
    var mywin = window.open(myurl,'OVERVIEW','width=850,height=600,scrollbars=yes');
  });
});
