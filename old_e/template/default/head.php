<? // <html>? ?>
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
    $("#catcontent").ready(function() {
        $('.nav').click(function(){
            var base = window.location.href.substring(0, window.location.href.lastIndexOf("/") + 1);
            var base2=$(this).attr('href');
            base2=base2.replace(base,'');
            var toLoad = '{EXTPTH}/htmlload/{SHOP_NNN}_'+base2;
            //window.location.hash = 'c'+$(this).attr('href').substr(0,$(this).attr('href').length-5);
             window.location.hash = '#catlist';   
            $("#catcontent").hide('fast', function() {
            $("#catcontent").html('<img src="{IMG_PATH}/loader.gif" />');
            $('#catcontent').show();
            $("#catcontent").load(toLoad,'', function() {
                $('#catcontent').show('fast');
            });
            });
            return false;
        });
    });
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
            if(QtyrMarg2>'{MAXWIDTH}') { var QtyrMarg2='{MAXWIDTH}'; }

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

    $(document).ready(function () {
           var blokheight = $('#bigpic_nazv').height();
           var blok2height = $('.big_price').height(); var changeflag=0; var blokheight_diff=0;
           var blok2height_diff=0;
           if(blokheight>66) { blokheight_diff=blokheight-66; changeflag=1; }
           if(blok2height>22) { blok2height_diff=blok2height-22; changeflag=1; }
           if(changeflag>0) { var heightsumm=220+blokheight_diff+blok2height_diff;
                  $('#bigpic_img').css('height',(100-(heightsumm-220)));
                }
        });
</script>
{TRACKING}{HEAD_CONTAINER}
</head>