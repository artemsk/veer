<!DOCTYPE html>
<html>
<head>
<meta charset="windows-1251">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>{TITLE}</title>
<meta name="keywords" content="{KWORDS}" />
<meta name="description" content="{DESCRIPTIONS}" />
<meta name="viewport" content="width=device-width">

<link rel="stylesheet" href="{TEMPL_PATH}/css/bootstrap.css">
<link rel='stylesheet' type='text/css' href='{TEMPL_PATH}/css/css.css' />

<script src="{TEMPL_PATH}/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        
<!--<link rel="shortcut icon" href="{IMG_PATH}/favicon.ico" />-->
<!--<link rel="search" href="" title="" type="application/opensearchdescription+xml" />-->

<script src="{TEMPL_PATH}/js/vendor/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="{TEMPL_PATH}/js/vendor/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="{TEMPL_PATH}/js/vendor/jquery.maskedinput.min.js" type="text/javascript"></script>
<script src="{TEMPL_PATH}/js/vendor/skrollr.min.js" type="text/javascript"></script>
<script src="{TEMPL_PATH}/js/vendor/bootstrap.min.js"></script>

<script src="{TEMPL_PATH}/js/plugins.js"></script>   

<script>    
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


    jQuery(function($){
       $("#phone").mask("?(999) 999-99-99");
    });    

    $(document).ready(function() { 
            
      var wh=($(window).height())-50;
      if(wh>630) { wh=630; }
      if(wh<450) { wh=450; }
      
      $('.klb_4, .klb_4_slider, .klb_4_dynamic_1st, .klb_4_dynamic_2nd, #klb_10').height(wh+'px');
      $('#klb_main_out').css('top',wh+'px');
      $('#klb_5').css('top',(wh-50)+'px');
      $('#klb_main_out').css('height',$('#klb_main_out').height()-20);

      //$('#footer').css('top',(wh-50)+'px');
      
      //$("#footer").height($("#footer").height()+20);
 
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
    
       var s = skrollr.init();
       
       $(window).scroll(function(e) {
             // Get the position of the location where the scroller starts.
             var scroller_anchor = $(".scroller_anchor").offset().top;
             
             // Check if the user has scrolled and the current position is after the scroller start location and if its not already fixed at the top
             if ($(this).scrollTop() >= scroller_anchor && $('.scroller').css('position') != 'fixed')
             {    // Change the CSS of the scroller to hilight it and fix it at the top of the screen.
                 $('.scroller').css({
                     'position': 'fixed',
                     'top': '0px'
                 });
                 $('.scroller_anchor').css('height', '50px');
             }
             else if ($(this).scrollTop() < scroller_anchor && $('.scroller').css('position') != 'relative')
             {   $('.scroller_anchor').css('height', '0px');

                 // Change the CSS and put it back to its original position.
                 $('.scroller').css({
                     'position': 'relative'
                 });
             }
         });

    });
</script>    
{TRACKING}{HEAD_CONTAINER}
</head>