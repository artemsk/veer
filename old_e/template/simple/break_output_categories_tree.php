<script type="text/javascript">
    $.ajaxSetup ({ cache: false });
    $("#catcontent").ready(function() {
        $('.nav').click(function(){
            var base = window.location.href.substring(0, window.location.href.lastIndexOf("/") + 1);
            var base2=$(this).attr('href');
            base2=base2.replace(base,'');
            var toLoad = '{EXTPTH}/htmlload/{SHOP_NNN}_'+base2;
//          window.location.hash = 'c'+$(this).attr('href').substr(0,$(this).attr('href').length-5);
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
</script>
{CATEGORIES}