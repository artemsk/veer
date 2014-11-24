<link rel="stylesheet" type="text/css" href="{MAINURL}/template/admin/a_css.css"/>
<!--[if IE]>
<link rel='stylesheet' type='text/css' href='{MAINURL}/template/admin/a_css_ie.css' />
<![endif]-->
<script type="text/javascript">
    $('#message_form1').ready(function () {
        $('.replyname').click(function() { //this will apply to all anchor tags
            var lnk=$(this).html();
            $('#msgtxtarea').val($('#msgtxtarea').val()+lnk+" ");
        });

     $('#adm_msg_post').submit(function(event) {
      event.preventDefault();
      var act=$(this).attr('action');
      var em=$("#emnot").attr('checked');
      var txt=$('#msgtxtarea').val();
      $('#msgtxtarea').val('');
      if(em==true) { var em2=1; } else { var em2=0; }
      $.post( act, { em_notify: em2, msgtxt: txt, a_msgsend: "send" } , function( data ) {
       $("#messages").html(data);
        });
    });

    $('#view3').click(function() {
        $('#message_block').css('display','none');
        $('#container').css('width','95%');
        $('#view3').css('border-bottom-width','2px ');
        $('#view1').css('border-bottom-width','0px');
        $('#view2').css('border-bottom-width','0px');
        });

    $('#view1').click(function() {
    $('#message_block').css('display','block');
    $('#container').css('width','82%');
    $('#message_block').css('width','16%');
        $('#view3').css('border-bottom-width','0px ');
        $('#view1').css('border-bottom-width','2px');
        $('#view2').css('border-bottom-width','0px');
    });

    $('#view2').click(function() {
    $('#message_block').css('display','block');
    $('#container').css('width','60%');
    $('#message_block').css('width','38%');
        $('#view3').css('border-bottom-width','0px ');
        $('#view1').css('border-bottom-width','0px');
        $('#view2').css('border-bottom-width','2px');
    });

    });

    var auto_refresh = setInterval( function() {
    $('#messages').load('{EXTPTH}/messages/show');
    }, 5000);

    $('#adm_menu').ready(function() {
        var lnk=document.location.href;
        $a=$(this).find('a[href="'+lnk+'"]');
        $a.replaceWith($a.html());
        });

      $(document).on("click", ".tbl_chkbox_on", function(){
       $(this).removeClass('tbl_chkbox_on').addClass('tbl_chkbox_off');
       $(this).find('input[type=checkbox]').removeAttr('checked','checked');
    });

    $(document).on("click", ".tbl_chkbox_off", function(){
       $(this).removeClass('tbl_chkbox_off').addClass('tbl_chkbox_on');
       $(this).find('input[type=checkbox]').attr('checked','checked');
        });

   $(document).ready(function() {

    $(document).find('input[name=a_editshop]').click(function() {        
    $('#adm_form_editshop').submit(function(event) {
      event.preventDefault();
      var act=$(this).attr('action');
      var serform=$('#adm_form_editshop').serialize();
      $("#edit1").hide('slow').html('<img src="{IMG_PATH}/loader.gif" />').hide('slow');
      $.post( act, serform, function( data ) {          
            $("#edit1").html(data).show('slow'); 
           $('#adm_form_editshop').unbind('submit');
    });
    });    
    });

    $('.adm_form_post_editshop_stats').submit(function(event) {
      event.preventDefault();
      var act=$(this).attr('action');
      var where=$(this).find('input[name=a_editshop_stats_hid]').val();
      var serform=$(this).serialize();
      if(where=="b") { var loaddiv="#edit3"; } else { var loaddiv="#edit2"; }
      $(loaddiv).html('<img src="{IMG_PATH}/loader.gif" />');
      $.post( act, serform, function( data ) {
           $(loaddiv).html(data);
           $(this).unbind('submit');
       });
    });

      /* $('.adm_form_post_editshop_conf').submit(function(event) {
      event.preventDefault(); 
      var act=$(this).attr('action');    
      var serform=$(this).serialize();
      alert(serform);
      var serform2=serform.split('a_editshop_conf_hid=');
      var serform3=serform2[1].split('&');
      var loaddiv="#edit4_"+serform3[0];

      $(loaddiv).html('<img src="{IMG_PATH}/loader.gif" />');
      $.post( act, serform, function( data ) {
           $(loaddiv).html(data);
           $(this).unbind('submit');
    });
    }); TODO: разобраться почему не работает */


    });

</script>