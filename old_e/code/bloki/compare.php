<?php
if(@$info['4bloks']['compare_prds_flag']!=""&&is_array(@$info['4bloks']['compare_prds'])) {

   $compare_types=array("img"=>"&nbsp;",
                        "template_type"=>"",
                        "nazv"=>"название",
                        "nazv_link"=>"",
                        "price_formated"=>"цена",
                        "status"=>"",
                        "kogda"=>"",
                        "qty"=>"кол-во",
                        "weight"=>"вес",
                        "pv"=>"pv",
                        "shop_cat_nazv"=>"разделы",
                        "shop_cat"=>"",
                        "shop_cat_onlyshop"=>"магазин",
                        "keywords"=>"слова",
                        "attr_descr"=>"параметры",
                        "prd_in_grps"=>"наличие спецпред-<br/>ложений",
                        "rate"=>"оценка",
                        "rate_yes"=>"",
                        "rate_no"=>"",
                        "ordered_month"=>"заказов за последнее время",
                        "viewed_month"=>"просмотров в последнее время",
                        "review"=>"количество отзывов",
                        "full_basket_form"=>"",
                        "out_of_list");

    $show_lst=$info['4bloks']['compare_prds'];
// "<pre>";
//print_r($show_lst);
// "</pre>";

    $qtys=explode(",",QTY_NAMES); // 0,(1),2,(3),4,(5),6
    $prd_filename=MAINURL_5."/template/".TEMPLATE."/product_list.php";
    $templ_prd_one=get_include_contents($prd_filename); // ###шаблон_ввод
    $templ_prd_types=explode("[####]",$templ_prd_one); //  6, 7, 8, 9
    if(!is_array($cats)) { $cats=new categories(); }
    $look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']

    $prd_arr=array();
    foreach($show_lst as $o1=>$o2) { $str2="";
     if($o1=="nazv_link"||$o1=="status"||$o1=="kogda"||$o1=="shop_cat"||$o1=="rate_yes"||$o1=="rate_no"||$o1=="template_type") {  continue; }
     $str2.="<tr><td><font class='compare_fields'>".$compare_types[$o1]."</font></td>";
     foreach($o2 as $o3=>$o4) { $prd_arr[$o3]=$o3;
       $o5=$o4;
       if($o1=="img") { if(is_array($o4)) { $o5=$o4['big']; } else { $o5=$o4; } }
       if($o1=="nazv") { $o5="<font style='font-size:1.2em;margin-left:-10px;'>".strtr(@$templ_prd_types[($show_lst['template_type'][$o3]+6)], array("{IMG_PATH}" => MAINURL."/template/".TEMPLATE."/images","{GROUP_PRD_NAME}" => GROUP_PRD_NAME,
                                                    "{STAR_PRD_NAME}" => STAR_PRD_NAME))."<br/></font><a href=".$show_lst['nazv_link'][$o3].">".$o4."</a>"; }
       if($o1=="qty") { if($o4>$qtys[5]) { $o5=$qtys[6]; } if($o4<=$qtys[5]) { $o5=$qtys[4]; } if($o4<=$qtys[3]) { $o5=$qtys[2]; } if($o4<=$qtys[1]) { $o5=$qtys[0]; }
       if($show_lst['status'][$o3]!="buy") { $o5="&nbsp;"; } 
       }
       if($o1=="weight") { if($o4<=0) { $o5="-"; } else { $o5=$o4." г."; } }
       if($o1=="shop_cat_nazv") { $str=""; foreach($o4 as $z1=>$z2) {
                        if($look['podrazdel'][$z1]['type']=="shop"||$z1==SHOP_NNN) { } else {
                         if($look['podrazdel'][$z1]['remote_addr']!=""&&$look['podrazdel'][$z1]['remote_always']=="1") { $lnk=$look['podrazdel'][$z1]['remote_addr']; } else {
                         $lnk=MAINURL."/catalog/".$z1; }
                         $str.="<a href=".$lnk.">".$z2."</a> &middot; ";  $show_lst2[$o1][$z2][$o3]="<li style='list_style:circle;'></li>"; }} /*$o5=substr($str,0,-9);*/ $o5="&nbsp;"; }
       if($o1=="shop_cat_onlyshop") { $o5="<a href=".$o4['remote_addr'].">".$o4['nazv']."</a>"; }
       if($o1=="keywords") { if(is_array($o4)) { $str=""; foreach($o4 as $z1=>$z2) { $str.="<a href=".MAINURL."/keyword/".$z1.">".$z2."</a> &middot; ";
       $show_lst2[$o1][$z2][$o3]="<li style='list_style:circle;'></li>"; } /*$o5=substr($str,0,-9);*/ $o5="&nbsp;"; } }
       if($o1=="attr_descr") { if(is_array($o4)) { foreach($o4 as $z1=>$z2) { $str="";
            if(is_array($z2)) { foreach($z2 as $z3=>$z4) { $str.="<a href=".MAINURL."/attr/".$z4.">".$z3."</a> &middot; "; } $str=substr($str,0,-9); } else { $str=$z2; }
           $show_lst2[$o1][$z1][$o3]=$str; } $o5="&nbsp;"; } }
       if($o1=="prd_in_grps") { if($o4=="1") { $o5="<li style='list_style:circle;'></li>"; } else { $o5="-"; } }
       if($o1=="rate") { if(isset($o4['rate_avr_all'])) { $o5="<div class='product_rate_all' style='float:left;'>
        <div class='product_rate' style='width:".ceil($o4['rate_avr_all']*20)."px;'></div>
        <div style='position:absolute;left:0px;top:0px;'><img src=".MAINURL."/template/".TEMPLATE."/images/rate.png></div></div>"; } else { $o5="&nbsp;"; }}
       if($o1=="review") { if(is_array($o4)) { $o5=count($o4); } else { $o5="&nbsp;"; } }
       if($o1=="full_basket_form"||$o1=="out_of_list") { $show_lst4[$o1][$o3]=$o4; $o5="&nbsp;"; }
       if($o1=="price_formated") { $o5="<font class='compare_price'>".$o5."</font>"; } 
       $str2.="<td>".$o5."</td>";     }
       $str2.="</tr>";
       $check_str=strtr(strip_tags($str2,'<img>'),array("<tr>"=>"","</tr>"=>"","<td>"=>"","</td>"=>"","&nbsp;"=>"",$compare_types[$o1]=>""));
       if($check_str!="") { $show_lst3[$o1]=$str2; } unset($show_lst[$o1]);
        }

$str3="<table cellpadding=10>";
        foreach($show_lst3 as $o1=>$o2) { if(isset($show_lst2[$o1])||isset($show_lst4[$o1])) { continue; } $str3.=$o2; }

        foreach($show_lst2 as $o1=>$o2) {
           $str3.="<tr><td class=\"compare_".$o1."\" colspan=".(count($prd_arr)+1).">".$compare_types[$o1]."</td></tr>";
           foreach($o2 as $o3=>$o4) { $str3.="<tr><td class='compare_fields3'>".@$o3."</td>";
           foreach($prd_arr as $o5=>$o6) { if(!isset($o4[$o5])) { $str3.="<td>-</td>"; } else { $str3.="<td class='compare_fields2'>".$o4[$o5]."</td>"; } }
           $str3.="</tr>"; }  }

           foreach($show_lst4 as $o1=>$o2) {
           $str3.="<tr><td></td>";
           foreach($o2 as $o3=>$o4) { $str3.="<td>".$o4."</td>"; }
           $str3.="</tr>";
           }
$str3.="</table>";

$out['{PAGE_NAZV}']="Сравнение товаров";
$out['{COMPARE_TABLE}']="<div class=\"compare_table\">".$str3."</div>";
$out['{COMPARE_CLEAN}']="display:none;";
$out['{BODY_DIV}']="compare_body_div"; // @reviewlate: непонятно заменяется ли или уже поздно
    }

if(@$info['4bloks']['compare_prds_flag']!=""&&!is_array($info['4bloks']['compare_prds'])) {
$out['{PAGE_NAZV}']="Сравнение товаров";
$out['{PAGE_DESCR}']="<div class=\"compare_descr\">".$info['4bloks']['compare_prds']."</div>";
$out['{BODY_DIV}']="compare_body_div";
    }

?>