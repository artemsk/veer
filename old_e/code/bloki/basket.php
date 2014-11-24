<?php 
if($info['4bloks']['show_bsk_flag']!="") {


    $out['{PAGE_NAZV}']="Корзина";
    
    $show_bsk=$info['4bloks']['show_bsk'];

    if(@substr(@$show_bsk,0,5)=="{###}") { $show_bsk_note=substr($show_bsk,5); $show_bsk=array(); } else { $show_bsk_note=""; }

 $bsk_filename=MAINURL_5."/template/".TEMPLATE."/basket.php";
 $templ_basket=get_include_contents($bsk_filename); // ###шаблон_ввод

 $templ_disassemble=explode("[####]",$templ_basket);

if($show_bsk_note=="") { // not empty
    $rows_kaunt=0;
    foreach($show_bsk as $k=>$v) { if($k=="total"||$k=="grp_by_shop"||$k=="shops") { continue; }
        $rows_kaunt++;

        $vvv=""; $vvv_kaunt=0; foreach($show_bsk[$k]['form']['attr_form'] as $kk=>$vv) { 
            if(trim($vv)!="") { $vvv_kaunt++; $vvv=$vvv."#".$vvv_kaunt."<hr class=\"divider3\">".$vv."<p></p>"; }}

        $templ_basket_str[$k] = strtr($templ_disassemble[3], array( // строка корзины
        "{CURRENT_PRICE}"   => price_format(@$show_bsk[$k]['current_price']),
        "{NAZV}" => @$show_bsk[$k]['nazv'],
        "{NAZV_LINK}" => @$show_bsk[$k]['nazv_link'],
        "{TYPE}" => @$show_bsk[$k]['type'],
        "{IMG}" => @$show_bsk[$k]['img'],
        "{PRICE_STAR}" => @$show_bsk[$k]['price_star'],
        "{ATTR_DESCR}" => @$show_bsk[$k]['form']['attr_descr'],
        "{PRD_QUANTITY}" => @$show_bsk[$k]['form']['quantity'],
        "{PRD_DEL}" => @$show_bsk[$k]['form']['del_prd'],
        "{ATTR_FORM}" => @$vvv,
        ));
        }

    foreach($show_bsk['grp_by_shop'] as $k=>$v) {
    foreach($v as $kk=>$vv) { 

    $kk_temp="##"; // TODO: отрубаем статусы 3,2,1
    if($k=="5") { $kk_temp="##"; }
    if($k=="4") { $kk_temp=$kk; }
    //"shopid=".$kk_temp.":";

    foreach($vv as $kkk=>$vvv) { 
    $bask_str=$k.$kk_temp.$kkk; $bask_shop_id[$bask_str]=$kk;
    if($k=="4") { $bask_shop[$bask_str]=$show_bsk['shops'][$kk]['nazv'];  } else { $bask_shop[$bask_str]=""; }
    $bask_shop_w[$bask_str]=@$bask_shop_w[$bask_str]+$show_bsk['shops'][$kk]['totalbask'][$kkk]['bask_weight'];
    $bask_shop_q[$bask_str]=@$bask_shop_q[$bask_str]+$show_bsk['shops'][$kk]['totalbask'][$kkk]['bask_quant'];
    $bask_shop_p[$bask_str]=@$bask_shop_p[$bask_str]+$show_bsk['shops'][$kk]['totalbask'][$kkk]['bask_price'];

    foreach($vvv as $kkkk=>$vvvv) { 
      $bask_shop_str[$bask_str][]=$kkkk;
     }}}}

     $ord=new order; // класс для формирования заказа      
     $delivery_address=$ord->delivery_address();
      
     $savedselects=$ord->read_basket_cookies();
     $error_style="style=\"border:1px #ff3366 dotted; background-image:url(".MAINURL."/template/".TEMPLATE."/images/err.jpg);\" ";

     if(isset($savedselects['baskid_last_swap'])&&is_array(@$savedselects['baskid_last_swap'])) { $resort_bask=array(); asort($savedselects['baskid_last_swap']);
         foreach($savedselects['baskid_last_swap'] as $c1=>$c2) { if(isset($bask_shop_str[$c1])) { $resort_bask[$c1]=$bask_shop_str[$c1]; unset($bask_shop_str[$c1]); }}
         $bask_shop_str=$resort_bask+@$bask_shop_str;
         } // TODO: протестировать не теряется ли чего
   
     if(!isset($newform)) { $newform=new forms; }

     if(@$savedselects['flag_errors'][1]['c_info']=="1") { $error_style4=$error_style; } else { $error_style4=""; }
     $quickie="<div style=\"margin-left:5px;\"><div class=\"basket_steps\">Информация о покупателе</div><hr class=\"divider\">".$newform->basket_form_quickie($savedselects, $error_style4)."<p></p></div>"; // форма быстрого заказа

     $bask_kaunt=0; $bask_out_main="";
     foreach($bask_shop_str as $k=>$v) { $bask_kaunt++; 
     $bask_out=""; $bask_out=$bask_out."корзина #".$bask_kaunt." "; if($bask_shop[$k]!="") { $bask_out=$bask_out.$bask_shop[$k]; }
     $bask_out_temp=""; foreach($v as $kk=>$vv) { $grpbybasket_send[$bask_kaunt][$vv]=$show_bsk[$vv]['current_price']; // сохр товары по корзине
     $bask_out_temp.=$templ_basket_str[$vv]; }

     $flag4upd_newaddr=0;
     if(count(@$savedselects['bask_upd'])>0) { if(count($savedselects['deliver_addr_book']['id_'.$bask_kaunt])>0) {} else {
     if($savedselects['deliver_new_addr_city'][$bask_kaunt]!=""||$savedselects['deliver_new_addr_postcode'][$bask_kaunt]!="") { $flag4upd_newaddr=1; }}}

     // адресная книга
     $delivery_address_form=$newform->basket_form_address_book($delivery_address, $bask_kaunt, @$bask_shop_w[$k], ceil(@$bask_shop_p[$k]), @$savedselects, @$flag4upd_newaddr); // адресная книга

     if(isset($delivery_address['default_shipping'])) { $def_postcode=$delivery_address['postcode'][$delivery_address['default_shipping']]; 
     $def_city=$delivery_address['city'][$delivery_address['default_shipping']]; $def_country=$delivery_address['country'][$delivery_address['default_shipping']];
     } else { $def_postcode=""; $def_city=""; $def_country="Россия"; }

     $flag4_newaddr_check=0;
        if(count(@$savedselects['bask_upd'])>0) {
            if(count($savedselects['deliver_addr_book']['id_'.$bask_kaunt])>0) {
                $def_city=$delivery_address['city'][$savedselects['deliver_addr_book']['id_'.$bask_kaunt]];
                $def_postcode=$delivery_address['postcode'][$savedselects['deliver_addr_book']['id_'.$bask_kaunt]];
                $def_country=$delivery_address['country'][$savedselects['deliver_addr_book']['id_'.$bask_kaunt]];
            } else { $flag4_newaddr_check=1; }}
    
         if(@$savedselects['deliver_new_addr_city'][$bask_kaunt]!=""&&($flag4_newaddr_check=="1"||!is_array($delivery_address))) {
                $def_city=@$savedselects['deliver_new_addr_city'][$bask_kaunt];         }
         if(@$savedselects['deliver_new_addr_postcode'][$bask_kaunt]!=""&&($flag4_newaddr_check=="1"||!is_array($delivery_address))) { 
                $def_postcode=@$savedselects['deliver_new_addr_postcode'][$bask_kaunt];          }
         if(@$savedselects['deliver_new_addr_country'][$bask_kaunt]!=""&&($flag4_newaddr_check=="1"||!is_array($delivery_address))) {
                $def_country=@$savedselects['deliver_new_addr_country'][$bask_kaunt];           }

     // способы доставки
     $delivery_arr=$ord->delivery_type($i=$def_postcode,$w=@$bask_shop_w[$k],$p=@$bask_shop_p[$k],$def_city,$def_country,array('baskid'=>$bask_kaunt)); // определяем доставку
     $delivery_form=$newform->basket_form_delivery($delivery_arr, $bask_kaunt, @$bask_shop_p[$k], @$savedselects['deliver_bask']['id_'.$bask_kaunt]); // создаем форму выбора доставки

     if(@$savedselects['flag_errors'][$bask_kaunt]['deliver']=="1") { $error_style1=$error_style; } else { $error_style1=""; }
     if(@$savedselects['flag_errors'][$bask_kaunt]['deliver_type']=="1") { $error_style2=$error_style; } else { $error_style2=""; }
     if(@$savedselects['flag_errors'][$bask_kaunt]['payment_type']=="1") { $error_style3=$error_style; } else { $error_style3=""; }
  
     if(is_array($delivery_arr)) { $daf="<td valign=\"top\" style=\"padding-right:35px;\"><div class=\"basket_steps\">Адрес доставки</div><hr class=\"divider\">
        <div class=\"basket_tbl_address\" ".@$error_style1."><span id='addressform".$bask_kaunt."'>".@$delivery_address_form."</span></div></td>"; } else { $daf=""; }

     // способы оплаты
     $payment_arr=$ord->payment_type(@$bask_shop_p[$k], array('city'=>$def_city, 'country'=>$def_country)); // собираем виды оплаты
     $payment_form=$newform->basket_form_payment($payment_arr, $bask_kaunt,@$savedselects['payment_bask']['id_'.$bask_kaunt]); // создаем форму оплата

     if(@$payment_form!=""&&is_array($delivery_arr)) { $pf="<td valign=\"top\" style=\"padding-right:35px;\"><div class=\"basket_steps\">Способы оплаты</div><hr class=\"divider\">
        <div class=\"basket_tbl_payment\" ".@$error_style3."><span id='paymentform".$bask_kaunt."'>".@$payment_form."</span></div></td>"; } else { $pf=""; }

     // подставляем форму быстрого заказа, если не залогинен, если 1 корзина
     if(@$_SESSION['customers_id']<=0&&count($bask_shop_str)<=1&&$bask_kaunt=="1") { $quickie2=@$quickie; $flag4quickie=1; } else { $quickie2=""; }

     // заменяем стоимость корзины, если уже были выбраны доставка и оплата
     if(@$savedselects['deliver_bask']['id_'.$bask_kaunt]!=""&&@$savedselects['payment_bask']['id_'.$bask_kaunt]!="") { 
         $newd=explode("_",$savedselects['deliver_bask']['id_'.$bask_kaunt]);
         $newp=explode("_",$savedselects['payment_bask']['id_'.$bask_kaunt]);
                $newsumm=$bask_shop_p[$k]."".ROUBLE;
                if($newd[1]>0) { $newsumm.="+ ".$newd[1]."".ROUBLE." "; }
                if($newp[1]>0) { $newsumm.="- ".$newp[1]."".ROUBLE." "; }
                $newsumm.="= ".number_format(($bask_shop_p[$k]+$newd[1]-$newp[1]),2)."".ROUBLE;
                $newsumm="<span class=\"onebasktotalprice\" id=\"bask_total_price".$bask_kaunt."_".strtr($bask_shop_p[$k],array("."=>"k"))."\"
                    style=\"background-color:#ffcc66;padding:5px 5px 5px 5px;\">".$newsumm."</span>";
         } else { $newsumm="<span class=\"onebasktotalprice\" id=\"bask_total_price".$bask_kaunt."_".strtr($bask_shop_p[$k],array("."=>"k"))."\">".price_format($bask_shop_p[$k])."</span>"; }

     // комментарии
     if(is_array($delivery_arr)) { $bsk_comm="<td valign=\"top\" width=50%>".$newform->basket_form_comments($bask_kaunt,@$savedselects)."</td>"; }

     $bask_out_temp_2 = strtr($templ_disassemble[4], array( // строка корзины
        "{CONTENT}"   => @$bask_out_temp,
        "{BASK_INFO}" => @$bask_out,
        "{BASK_P}" => $newsumm,
        "{BASK_W}" => @$bask_shop_w[$k],
        "{BASK_Q}" => @$bask_shop_q[$k],
        "{BASK_DELIVERY}" => "<span id='deliverytypes".$bask_kaunt."'>".@$delivery_form."</span>",
        "{BASK_ADDRESS}" => $daf,
        "{BASK_PAYMENT}" => $pf,
        "{BASK_COMMENTS}" => @$bsk_comm,
        "{ERROR_STYLE2}" => @$error_style2,
        "{QUICK_ORDER_2}" => @$quickie2,
        ));

     $bask_out_main=$bask_out_main.$bask_out_temp_2;

     $grpbybasket_send[$bask_kaunt]['p']=@$bask_shop_p[$k];
     $grpbybasket_send[$bask_kaunt]['w']=@$bask_shop_w[$k];
     $grpbybasket_send[$bask_kaunt]['q']=@$bask_shop_q[$k];
     $grpbybasket_send[$bask_kaunt]['info']=@$bask_out;
     $grpbybasket_send[$bask_kaunt]['catshopid']=@$bask_shop_id[$k];
     $grpbybasket_send[$bask_kaunt]['baskid']=$k; // TODO: здесь id корзины на тот случай, если мы хотим все-таки сохранить порядок!!
     } // BASK_IDS

      
     if(@$flag4quickie!="1"&&@$_SESSION['customers_id']<=0) { $quickie3="<div class=\"basket_form_quickie_top\">".@$quickie."</div>"; }

     }

 $templ_basket = strtr($templ_basket, array(
    "{BASKET_NOTE}"   => @$show_bsk_note,
    "{TOTAL_QUANTITY}" => @$show_bsk['total']['quantity'],
    "{TOTAL_DISCOUNT}" => price_format(@$show_bsk['total']['discount']),
    "{TOTAL_CURRENT_PRICE}" => price_format(@$show_bsk['total']['current_price'],@$show_bsk['total']['pure_price'],@$show_bsk['total']['price_star']),
    "{TOTAL_PRICE_NO_ATTR}" => price_format(@$show_bsk['total']['current_price_no_attr']),
    "{TOTAL_WEIGHT}" => @$show_bsk['total']['weight'],
    "{TOTAL_PRICE_STAR}" => @$show_bsk['total']['price_star'],
     ));

 $templ_disassemble=explode("[####]",$templ_basket);

 if($show_bsk_note=="") { $out['{PAGE_DESCR}']=$templ_disassemble[2]; } else {  $out['{PAGE_DESCR}']=$templ_disassemble[1];  }
 
 if(!isset($newform)) { $newform=new forms; }
 $out['{BASKET_FORM}']=$newform->basket_form(@$quickie3.@$bask_out_main,@$grpbybasket_send);

}


 ?>