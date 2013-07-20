/* JS Code for Process overview */
  /* Add mouseover effect */

$(document).ready(function() {
$(".killsess").mouseover(function() {
    $(this).css('cursor','pointer');
  });

$(".killsess").click(function() {
  var myid  = $(this).attr('id');
  var mysid = myid.replace(/(SESS_)(\d{1,})(-)(\d{1,})/,"$2");
  var myser = myid.replace(/(SESS_)(\d{1,})(-)(\d{1,})/,"$4");
  var rc    = confirm("Do you really want to kill this session?");
  if(rc == false) return;
  $.ajax({
      cache: false,
      url: "killsession.php",
      async: false,
      dataType:'json',
      data: {"SID":mysid,"SERIAL":myser},
      success: function(json)
        {
        reloadTab(0);
        },
      error: function(XMLHttpRequest, textStatus, errorThrow)
        {
        alert('RETRIVAL ERROR: '+textStatus+'\nErrorThrow='+errorThrow);
        }
    });     // end ajax call
}); // End click()

}); // End ready()
