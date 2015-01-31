/* JS Code for flashback_recyclebin.php */
$(document).ready(function() {
  $("#rb_purge_all").button();

  $("#rb_purge_all").click(function() {
    var rc = confirm('Do you really want to clean up system-wide recycle bin?\n\nWARNING: This cannot be undone!');
    if(rc == false) return;
    $.ajax({
      url: "flashback_ajax.php",
      global: false,
      async: false,
      cache: false,
      type: "POST",
      data: ({"MODE": "CLEANALL"}),
      dataType:'json',
      timeout: 15000,
      success: function(json)
        {
        if(json.ERROR)
          {
          alert(json.ERROR);
          }
        else
          {
          var current_index = $("#tabs").tabs("option","active");
          $("#tabs").tabs('load',current_index);
          }
        },
      error: function(jqXHR, textStatus, errorThrown)
        {
        alert(textStatus+": " + errorThrown);
        }
      });
  });

  $(".rb_undrop, .rb_purge").mouseover(function() { $(this).css('cursor','pointer'); });

  $(".rb_undrop, .rb_purge").click(function() {
    var myid  = $(this).attr('id').replace(/([P|U]{1})(_)(.*)(\.)(.*)/,"$1|$3|$5").split("|");
    var tit   = $(this).parent().parent().get(0).cells[1].innerHTML.replace(/(.*)(ORG:&nbsp;)(.*)(<\/small>)/,"$3");
    var ttype = $(this).parent().parent().get(0).cells[2].innerHTML;
    var q     = '';
    if(myid[0] == 'P')
      {
      q = 'Do you really want to purge object\n\n'+myid[1]+'.'+tit+'\n\nfrom recycle bin?';
      }
    else
      {
      q = 'Do you really want to restore object\n\n'+myid[1]+'.'+tit+'\n\nfrom recycle bin?';
      }
    var rc = confirm(q);
    if(rc == false)
      {
      return(false);
      }
  $.ajax({
    url: "flashback_ajax.php",
    global: false,
    async: false,
    cache: false,
    type: "POST",
    data: ({"MODE": myid[0],"OWNER": myid[1], "OBJ":myid[2],"ORG":tit,"TTYPE":ttype}),
    dataType:'json',
    timeout: 15000,
    success: function(json)
      {
      if(json.ERROR)
        {
        alert(json.ERROR);
        }
      else
        {
        var current_index = $("#tabs").tabs("option","active");
        $("#tabs").tabs('load',current_index);
        }
      },
    error: function(jqXHR, textStatus, errorThrown)
      {
      alert(textStatus+": " + errorThrown);
      }
    });
  });


});
