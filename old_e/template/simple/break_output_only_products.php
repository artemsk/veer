<script>     $(document).ready(function resizewin() {
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
</script>
{PRDS_LISTING_TOP}{PRDS_LISTING}
{PRDS_LISTING_TOP_DVA}{PRDS_LISTING_DVA}
{SHOW_MORE}