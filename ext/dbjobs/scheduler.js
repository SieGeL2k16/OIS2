/* JS code for JS scheduler tab */
$(document).ready(function() {
  $(".JOB_STATE").mouseover(function() {
    $(this).css('cursor','pointer');
  });
  $(".JOB_STATE").click(function() {
    var jobref  = $(this).attr('alt');
    var newstate= $(this).prop('title').replace(/(Click to )(.*)/,"$2");
    var rc = confirm("Do you really want to "+newstate+" the selected job\n\n\""+jobref+"\" ?");
    if(rc == false) return;
    $.ajax({
      cache: false,
      url: "ajax.php",
      async: false,
      dataType:'json',
      data: {"MODE":"TOGGLE","JOB":jobref,"NEWSTATE":newstate},
      success: function(json)
        {
        reloadTab(1);
        },
      error: function(XMLHttpRequest, textStatus, errorThrow)
        {
        alert('RETRIVAL ERROR: '+textStatus+'\nErrorThrow='+errorThrow);
        }
    });     // end ajax call

  });
});
