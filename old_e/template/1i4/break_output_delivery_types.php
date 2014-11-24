<script>
    function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

      $(document).ready(function () {
   $('.tblrow').click(function() {
        var clckid=$(this).find('input[type=radio]').attr('id');
        if($('#'+clckid).attr('disabled')) {} else {
        $('#'+clckid).attr('checked','checked'); }

            var clckid=$(this).find('input[type=radio]').attr('id');
            if($('#'+clckid).attr('disabled')) {} else {
            if(clckid.substr(0,8)=="delivert"||clckid.substr(0,7)=="payment") {
                if(clckid.substr(0,8)=="delivert") {
                var bskid=clckid.substr(8).split("_");
                var d4=clckid;
                var p=$('.tblrow_selected').find('input[id^=payment'+bskid[0]+']').attr('id');
                } else {
                var bskid=clckid.substr(7).split("_");
                var p=clckid;
                var d4=$('.tblrow_selected').find('input[id^=delivert'+bskid[0]+']').attr('id');
                }

                if(d4!=undefined) {
                var d3=$('#'+d4).val().split("_");
                var d=d3[1]; } else { var d=0; }
                if(p!=undefined) {
                var p2=$('#'+p).val().split("_");
                var p3=p2[1]; } else { var p3=0; }

                var t=$('#basket_total_form').find('.onebasktotalprice[id^=bask_total_price'+bskid[0]+']').attr('id');
                var t2=t.split("_");
                t2[3]=t2[3].replace('k','.');
                var summ2=parseFloat(t2[3])+parseFloat(d)-parseFloat(p3);
                var summ=t2[3]+" <span class=\"rur\">p</span> ";
                if(d>0) { var summ=summ+"+ "+d+" <span class=\"rur\">p</span> "; }
                if(p3>0) { var summ=summ+"- "+p3+" <span class=\"rur\">p</span> "; }
                var summ=summ+"= "+summ2.toFixed(2)+" <span class=\"rur\">p</span>";
                $('#'+t).html(summ);
                $('#'+t).css('background-color','#ffcc66');
                $('#'+t).css('padding','5px 5px 5px 5px');  

                    if(summ!="") { var flag_order=0; var bask_k=0; var tprc=0;
                       $(".onebasktotalprice").each(function() { bask_k=bask_k+1;
                       var id=$(this).css('background-color');
                       if(id!="transparent"&&id!="rgba(0, 0, 0, 0)") { if(d4!=undefined&&p!=undefined) { flag_order=flag_order+1; }
                            var nprc=$(this).html().split("= "); if(nprc[1]!=undefined) {
                            var nprc2=nprc[1].split(" ");
                            nprc2[0]=nprc2[0].replace(',','');
                            tprc=tprc+parseFloat(nprc2[0]); }
                                } else {
                                    var nprc=$(this).attr('id').split("_");
                                    tprc=tprc+parseFloat(nprc[3]);
                                    }
                       });
                       if(tprc>0) { $('#total_prc').html(addCommas(tprc.toFixed(2))+"&nbsp;<span class=\"rur\">p</span>");
                                   $('#update_cart_fin_summ').html(addCommas(tprc.toFixed(2))+"&nbsp;<span class=\"rur\">p</span>&nbsp; ");
                       }
                       if(bask_k==flag_order) {
                        $('#update_cart_fin').removeAttr('disabled');
                        $('#update_cart_fin').attr('name','update_cart_fin');
                        if($('#update_cart_fin').attr('name')!="update_cart") { $('#update_cart_fin_txt').html('Все готово для оформления заказа.'); }
                       }
                    }

                 }}


                if(clckid.substr(0,8)=="delivert") {
                    var bskid=clckid.substr(8).split("_");
                    var paddr=$("#pickpoint_addr"+bskid[0]).val();
                    if(paddr!=undefined) {
                    var piccity2=paddr.split('<br/>');
                    var piccity3=piccity2[1].split(',');
                    var piccity4=piccity3[2].replace(' ','-');
                    } else { var piccity4=""; }
                    clckid=clckid + "_" + piccity4;
                    $("#paymentform"+bskid[0]).html('<img src="{IMG_PATH}/loader.gif" height=15/>');
                        $.post( '{EXTPTH}/payment/index.php', { a: clckid } , function( data ) {
                         $("#paymentform"+bskid[0]).html(data);
                            });
                    }
    });

           $('.pickpointchoose').click(function() {
           var clckid=$(this).attr('id').split('_');
                              PickPoint.open(function (result){
                            var pic_city=result['address'].split(',');
                            var pic_city2=pic_city[2].replace(' ','-');
                            var clckid2="deliver_addr"+clckid[0]+"_"+clckid[1]+"_"+clckid[2]+"_"+clckid[3]+"_"+clckid[4]+"_"+clckid[5]+"_"+result['id']+"_"+pic_city2;
                            $(".deliverytypes_loading"+clckid[0]).html('<img src="{IMG_PATH}/loader.gif" height=15/>');
                             $.post( '{EXTPTH}/deliver/index.php', { a: clckid2 } , function( data ) {
                                $("#deliverytypes"+clckid[0]).html(data);
                                $("#pickpoint_address"+clckid[0]).html(result['name']+"<br/>"+result['address']);
                                $("#pickpoint_addr"+clckid[0]).val(result['name']+"<br/>"+result['address']);
                                $("#pickpoint_id"+clckid[0]).val(result['id']);
                                 });
                        });
           });
      });
      </script>
{DELIVERY_REFORM}