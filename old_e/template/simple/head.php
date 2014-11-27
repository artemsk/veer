<? // <html>? ?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>{TITLE}</title>
<meta name="keywords" content="{KWORDS}" />
<meta name="description" content="{DESCRIPTIONS}" />
<!--<link href='http://fonts.googleapis.com/css?family=Noto+Serif&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Lobster&subset=latin,cyrillic' rel='stylesheet' type='text/css'>-->
<link rel='stylesheet' type='text/css' href='{TEMPL_PATH}/css.css' />
<!--[if IE]>
<link rel='stylesheet' type='text/css' href='{TEMPL_PATH}/css_ie.css' />
<![endif]-->
<!--<link rel="search" href="" title="" type="application/opensearchdescription+xml" />-->
<link rel="shortcut icon" href="{IMG_PATH}/favicon.ico" />
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>
	!window.jQuery && document.write('<script src="{EXTPTH}/fancybox/jquery-1.4.3.min.js"><\/script>');
</script>
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

    $(".callme_nav").ready(function() {
        $('.callme_nav').click(function(){
            var base = window.location.href.substring(0, window.location.href.lastIndexOf("/") + 1);
            var base2=$(this).attr('href');
            base2=base2.replace(base,'');
            var toLoad = '{EXTPTH}/callme/'+base2;
            $("#callme_content").load(toLoad,'', function() {
                $('#callme_content').show('fast');
            });
            return false;
        });
    });

        $(document).ready(function resizewin() {
            var	QtySize = 145;
            var	QtyrMarg = 444;

            var QtyrMarg2 = $(window).width(); 
            if(QtyrMarg2>1024) { var QtyrMarg2=1024; }

            var	Qtyrwidth = Math.floor(QtyrMarg2- QtyrMarg);

            var Qty2NewSize = ((parseInt(Qtyrwidth / 4))-35);
            var imageWidth=$(".products_in_list").width();
            var NewSize=Qty2NewSize;
            if(Qty2NewSize>=imageWidth) { var NewSize = imageWidth; }
            if(Qty2NewSize<90) { var NewSize = 90; }
            $(".products_in_list").width(NewSize);
            $(".groups_in_list_one").width(NewSize);
            if(Qty2NewSize<110) { $(".groups_in_list").width(245); }
            var Qty2New=parseInt(Qtyrwidth / (NewSize+35));
            for(I=5;I>Qty2New;I--) {
                $(".pinline"+I).hide();
                }
            if(QtyrMarg2<=910) {
                $("#login_top").width(QtyrMarg2/15);
                $("#pssw_top").width(QtyrMarg2/15);
                }
        });

    $(function() {
        $('.show_more_lnk').live("click",function() {
            var ID = $(this).attr("id").substr(2);
            if(ID) { 
                $("#show_more_next_"+ID).html('<img src="{IMG_PATH}/loader.gif" />');
                $.ajax({
                    type: "POST",
                    url: "{MAINURL}{DETECTED0}{DETECTED1}/sort/{DETECTED_SORT}/{DETECTED_SORT_DIR}/more/"+ID,
                    success: function(html){
                        $("#show_more_prds_content").append(html);
                        $("#show_more_next_"+ID).remove(); // removing old more button
                    }
                });
            } else {

            }

            return false;
        });
    });

    /* $(document).ready(function () {
           var blokheight = $('#bigpic_nazv').height();
           var blok2height = $('.big_price').height(); var changeflag=0; var blokheight_diff=0;
           var blok2height_diff=0;
           if(blokheight>66) { blokheight_diff=blokheight-66; changeflag=1; }
           if(blok2height>22) { blok2height_diff=blok2height-22; changeflag=1; }
           if(changeflag>0) { var heightsumm=220+blokheight_diff+blok2height_diff;
                  $('#bigpic_img').css('height',(100-(heightsumm-220)));
                }
        }); */
        
    $(window).ready(function() {
    /* $("#rightdiv").height($("#rightdiv").height()+200);*/
           
           var ldh=$("#leftdiv").height(); 
           var wh=$(window).height();
           var rh=$("#rightdiv").height(); 
           var fh=$("#footer").height();
           
           /*if(rh<wh) { $("#rightdiv").height($(document).height()-fh+200); }  */         
           /* if(ldh>wh & ldh>rh) { $("#rightdiv").height(ldh+200);  } */
           if(ldh>rh) { $("#rightdiv").height(ldh+200);  }
           
           /*alert("l " + ldh + " r " + rh + " f " + fh + " w " + wh + " d " + $(document).height());*/
           
           if(ldh<=wh & rh>=wh) { 
               $("#leftdiv").removeClass("left_div").addClass("left_div_fixed");
           
               var rdh3=rh-ldh;
              $(window).scroll(function(){  
                  /*alert(wh + "  " + ldh + " " + " " + rh + " " + $(window).scrollTop() + " ?" + rdh3);*/
                 if($(window).scrollTop()>rdh3) { 
                     var ldshift=Math.abs($(window).scrollTop()-rdh3)*(-1); 
                     $("#leftdiv").css('top',ldshift+'px');
                    } else { 
                     $("#leftdiv").css('top','0px'); 
                    }
              }); 
           }
    });
    
    $(document).ready(function () {
       $('#notifyblock').slideDown('fast').delay(3000).slideUp('slow');        
            
           
           if( $('.basketimg').length ){ 
           $('.basketimg').mouseenter(function() {
           var i=$(this).html();
           var i2=i.split('basket.png');
           if(i2.length>1) {
           var i3=i2[0]+'basket2.png'+i2[1];
           $(this).html(i3); } 
           }).mouseleave(function() {
           var i=$(this).html();
           var i2=i.split('basket2.png');
           if(i2.length>1) {
           var i3=i2[0]+'basket.png'+i2[1];
           $(this).html(i3); } 
           }); }
           
           /* не главная */
           if( ($('.markd_all').length > 0) & ($('#yeap').length > 0||$('#yeap_product').length > 0)) {
           $('.markd_all').remove();           
           }
           
           /* главная */           
           if( ($('.markd_i').length > 0) & ($('#yeap_index').length > 0) ) {
           $('.markd_i').remove();
           }
           
           if($('#yeap').length > 0||$('#yeap_product').length > 0) {
           $('.headerback').css('opacity','0.75');
           }
           
           /* товар */
           if($('#yeap_product').length > 0) {
           $('.add2listblok').show('slow');
           $('#login_div').css('display','none');
           } else { $('#login_div').css('display','block'); } 
           
           if($('.compare_table').length) { 
           $("#rightdiv").width('94%');
           $("#leftdiv").width('5%');
           $("#leftdiv").html('<div class=backimgbutton><a href=javascript:window.history.back()>&larr;</a>');
           } 
           
           if($('#yeap_index').length > 0 ) {
           var contentwidth=$('.chosen_products').width();
           $('.chosen_products .products_in_list_onmain').width(contentwidth/3.35);
           }
                 
    });
    



     
</script>
{TRACKING}{HEAD_CONTAINER}
</head>