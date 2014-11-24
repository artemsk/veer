<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>{TITLE}</title>
<meta name="keywords" content="{KWORDS}" />
<meta name="description" content="{DESCRIPTIONS}" />
<link rel='stylesheet' type='text/css' href='{TEMPL_PATH}/css.css' />
<!--[if IE]>
<link rel='stylesheet' type='text/css' href='{TEMPL_PATH}/css_ie.css' />
<![endif]-->
<!--<link rel="search" href="" title="" type="application/opensearchdescription+xml" />-->
<link rel="shortcut icon" href="{IMG_PATH}/favicon.ico" />
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->
<script src="{IMG_PATH}/js/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="{IMG_PATH}/js/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="{IMG_PATH}/js/jquery.maskedinput.min.js" type="text/javascript"></script>
<script type="text/javascript" src="{IMG_PATH}/js/skrollr.min.js"></script>
<script type="text/javascript">
        function show(elementname)
        {
            document.getElementById(elementname).style.display='block';
        }

        function hide(elementname)
        {
            document.getElementById(elementname).style.display='none';
        }

        function clearText(thefield){
            if (thefield.defaultValue==thefield.value)
                thefield.value = ""
        }

    function jumpToAnchor() {
   window.location = String(window.location).replace(/\#.*$/, "") + "#catlist";
}

    $.ajaxSetup ({ cache: false });

</script>
<script>
    jQuery(function($){
       $("#phone").mask("?(999) 999-99-99");
    });    

    $(document).ready(function() {
      var s = skrollr.init();
      
      var wh=($(window).height())-50;
      if(wh>630) { wh=630; }
      if(wh<450) { wh=450; }
      
      $('.klb_4, .klb_4_slider, .klb_4_dynamic_1st, .klb_4_dynamic_2nd, #klb_10').height(wh+'px');
      $('#klb_main_out, #footer').css('top',wh+'px');
      $('#klb_5').css('top',(wh-50)+'px');
      
      $("#footer").height($("#footer").height()+20);
      
      $("#klb_5").click(function() {
          
         clearInterval(autoslide);
         autoslide=setInterval (function(){
         $("#klb_5").click();
         }, 10000);
       
         var previmg=$("#klb_5").attr('bgloaded');

         $("#klb_5").hide();      
        
         $('.klb_4_dynamic_2nd').load('{EXTPTH}/rotatinghead/'+previmg, function() { 
         
         var newslogan=$('#klb_6_change').html();
         var imgloaded=$('#klb_6_change').attr('bgloaded');
         $('#klb_6_change').remove();
         
         $('#klb_6').animate({left:'-=150%'},500,"easeInOutQuart", function() {
             $('#klb_6').html(newslogan).css('left','400%').animate({ left: '-=300%'}, 500, "easeInOutQuart");
         });
         
         $('.klb_4_dynamic_2nd').animate({ left: '-=100%'}, 1000, "easeInOutQuart", function() {
             $(this).removeClass('klb_4_dynamic_2nd').addClass('klb_4_dynamic_1st').css('left','0%');
         });
         
         $('.klb_4_dynamic_1st').animate({ left: '-=100%'}, 1000, "easeInOutQuart", function() {
             $(this).hide();
             $(this).removeClass('klb_4_dynamic_1st').addClass('klb_4_dynamic_2nd').css('left','100%').show();
             $("#klb_5").delay(500).show();
             $("#klb_5").attr('bgloaded',imgloaded);
         });

         });         
      });
       
      autoslide=setInterval (function(){
        $("#klb_5").click();
       }, 10000);
    
    });
</script>
{TRACKING}{HEAD_CONTAINER}
</head>