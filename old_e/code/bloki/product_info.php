<?php
// prd, add2list, review

// OUT_REMEMBER


$out_remember['{PRODUCT_NAZV}']=$info['4bloks']['prd']['nazv'];
$out_remember['{PRODUCT_PRICE}']=$info['4bloks']['prd']['all_prices']['price_formated'];

if(!is_array($cats)) { $cats=new categories(); }
$look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']

$str="";
if(count($info['4bloks']['prd']['shop_cat'])>0) {
foreach($info['4bloks']['prd']['shop_cat'] as $z1=>$z2) { 
    if($look['podrazdel'][$z1]['type']=="shop"||$z1==SHOP_NNN) { } else {
    if($look['podrazdel'][$z1]['remote_addr']!=""&&$look['podrazdel'][$z1]['remote_always']=="1") { $lnk=$look['podrazdel'][$z1]['remote_addr']; } else { $lnk=MAINURL."/catalog/".$z1; }
    $str.="<div class=\"product_cats\"><a href=".$lnk.">".$info['4bloks']['prd']['shop_cat_nazv'][$z1]."</a></div><p style='clear:left;margin:0 0 0 0;padding:0 0 0 0;'></p>";
    $info['4bloks']['recomend_same_cat'][$z1]=$z1;
    }}}

$out_remember['{PRODUCT_CATS}']=$str;

$str=""; if(count($info['4bloks']['prd']['keywords'])>0) {
foreach($info['4bloks']['prd']['keywords'] as $z1=>$z2) {
    $str.="<a href=".MAINURL."/keyword/".$z1.">".$z2."</a> &middot; ";
    }}
if($str!="") {
$out_remember['{PRODUCT_KEYWORDS}']="<div class=\"product_keywords\"><span class=\"product_small_title\">Ключевые слова</span><hr class=\"divider\">".substr($str,0,-9)."</div>"; }

$str=""; if(count($info['4bloks']['prd']['attr_descr'])>0) {
foreach($info['4bloks']['prd']['attr_descr'] as $z1=>$z2) { $str.=$z1." &rarr; ";
    foreach($z2 as $z3=>$z4) {
    $str.="<a href=".MAINURL."/attr/".$z4.">".$z3."</a> &middot; ";
    } $str=substr($str,0,-9); $str.="<p></p>"; }}

    if($str!="") {
$out_remember['{PRODUCT_ATTRS}']=$str; }

if($info['4bloks']['prd']['production_code']!="") {
$out_remember['{PRODUCTION_CODE}']="код &rarr; ".$info['4bloks']['prd']['production_code']; }

if($info['4bloks']['prd']['type']=="grp") {
    $weight=$info['4bloks']['prd']['grp_weight'];
    } else {
    $weight=$info['4bloks']['prd']['weight'];
        }
if(@$weight>0) {
$out_remember['{PRODUCT_WEIGHT}']="вес &rarr; ".$weight." г."; }

if(count(@$info['4bloks']['prd']['shop_cat_onlyshop_arr'])>0) { $str="";
    foreach($info['4bloks']['prd']['shop_cat_onlyshop_arr'] as $z1=>$z2) { if(isset($z3[$z2['nnn']])) { continue; }
    if($z2['nnn']==SHOP_NNN) { continue; } 
        $str.="<a href=".$z2['remote_addr'].">".$z2['nazv']."</a> &middot; "; $z3[$z2['nnn']]=$z2['nnn'];
        }
         if($str!="") { $out_remember['{PRODUCT_SHOP}']="<div class=\"product_shop\">".substr($str,0,-9)."</div>";  }
    }



if($info['4bloks']['prd']['full_basket_form']!="") { $out_remember['{FULL_BASKET_FORM}']=$info['4bloks']['prd']['full_basket_form']; } else {
    $out_remember['{BASKET_LINK}']="<span class='basket_link_txt'>".$info['4bloks']['prd']['basket_link']."</span>";
    }

if(@$info['4bloks']['prd']['kogda']!="") { $out_remember['{PRODUCT_KOGDA}']="<div class='product_kogda'>".$info['4bloks']['prd']['kogda']."</div>"; }

if($info['4bloks']['prd']['grp_nazv']!=""&&count(@$info['4bloks']['prd']['grp_nazv'])>0) { $str="";
    $z5=1;
    foreach($info['4bloks']['prd']['grp_nazv'] as $z1=>$z2) { foreach($z2 as $z3=>$z4) {
        $str.="(".$z5.") <a href=".MAINURL."/product/".$z3.">".$z4."</a><p></p>"; $z5++;
        }}
        if($str!="") {
    $out_remember['{PRODUCT_GRP_NAZV}']="<div class=\"product_grp_nazv\"><span class=\"product_small_title\">Товары в спецпредложении</span><hr class=\"divider\">".$str."</div>"; }
    }

if($info['4bloks']['prd']['descr']!="") {
$out_remember['{PRODUCT_DESCR}']="<div class=\"product_descr\"><span class=\"product_small_title\" style='font-size:0.7em;'>Описание</span><hr class=\"divider\">".textprocess($info['4bloks']['prd']['descr'])."</div>";
}

$templ_prd3['used'][$url_val]=$url_val;

if($info['4bloks']['prd']['products_connects']>0) {
    if(!is_object($products)) { $products=new products; }
    $templ_connects=$products->product_listing($info['4bloks']['prd']['products_connects'], $look, 0,@$templ_prd3['used'],"5","0","","1");
    if($templ_connects['templ_prd']!="") { // @reviewlate: не обрабатывается show_products проверить ссылки
            $templ_prd3['used']=$templ_connects['used'];
    $out_remember['{PRODUCT_CONNECTS}']="<div class=\"products_list\" style=\"clear:right;margin-top:25px;\"><hr class=\"divider_2\">
        <div class=\"product_listing_name_blok\">Обратите внимание</div>
        ".$templ_connects['templ_prd']."</div>"; }
    }


if($info['4bloks']['prd']['prd_in_grps']>0) {
    if(!is_object($products)) { $products=new products; }
    $templ_grps=$products->product_listing($info['4bloks']['prd']['prd_in_grps'], $look, 0,$templ_prd3['used'],"5","0","","1");
    if($templ_grps['templ_prd']!="") {
        $templ_prd3['used']=$templ_grps['used'];
    $out_remember['{PRODUCT_IN_GRPS}']="<div class=\"products_list\" style=\"clear:right;margin-top:25px;\"><hr class=\"divider_2\">
        <div class=\"product_listing_name_blok\">Спецпредложение</div>
        ".$templ_grps['templ_prd']."</div>"; }
    }

if(isset($info['4bloks']['prd']['rate'])) { $str="";
if(isset($info['4bloks']['prd']['rate']['rate_avr_all'])) { $rrr=$info['4bloks']['prd']['rate']['rate_avr_all']; } else { $rrr=$info['4bloks']['prd']['rate']['rate_avr']; }
    $str="<div class='product_rate_all'>
        <div class='product_rate' style='width:".ceil($rrr*20)."px;'></div>
        <div style='position:absolute;left:0px;top:0px;'><img src=".MAINURL."/template/".TEMPLATE."/images/rate.png></div></div>";
    $out_remember['{PRODUCT_RATE}']=$str;
    }

if(@$info['4bloks']['rate_prd']!="") { $out_remember['{PRODUCT_RATE_YN}']="<div class='product_rate_yn'>".$info['4bloks']['rate_prd']."</div>"; }


//$out_remember['{PRODUCT_IMG}']=$info['4bloks']['prd']['img']['big'];
$out_remember['{PRODUCT_IMG}']="<div class='product_img_blank'></div>";
if(@array_key_exists('big',$info['4bloks']['prd']['img'])) {
    if(@$info['4bloks']['prd']['img']['big']!="") {
$out_remember['{PRODUCT_IMG}']="<div class='product_img'>".$info['4bloks']['prd']['img']['big']."</div>"; }

} else { if(@$info['4bloks']['prd']['img']!="") {
$out_remember['{PRODUCT_IMG}']="<div class='product_img' style='width:250px;'>".$info['4bloks']['prd']['img']."</div>";
    }}

if(@array_key_exists('all',$info['4bloks']['prd']['img'])) { if(@$info['4bloks']['prd']['img']['all']!="") {
    $out_remember['{PRODUCT_IMG_ALL}']="<div class=\"product_img_list\"><hr class=\"divider\">
        <div class=\"product_listing_name_blok\" style='margin-left:0px;'>Изображения</div><div class='product_img'>".$info['4bloks']['prd']['img']['all']."</div></div>";
    }}


$out_remember['{PRODUCT_PAGE_BASKET}']="<div class='product_basket'>".@$out_remember['{FULL_BASKET_FORM}'].@$out_remember['{BASKET_LINK}'].@$out_remember['{PRODUCT_KOGDA}']."</div>";

            $prd_filename=MAINURL_5."/template/".TEMPLATE."/product_list.php";
            $templ_prd_one=get_include_contents($prd_filename); // ###шаблон_ввод
            $templ_prd_types=explode("[####]",$templ_prd_one); //  6, 7, 8, 9

            $templ_prd_one2 = strtr(@$templ_prd_types[($info['4bloks']['prd']['template_type']+6)], array(
                        "{IMG_PATH}" => MAINURL."/template/".TEMPLATE."/images",
                        "{GROUP_PRD_NAME}" => GROUP_PRD_NAME,
                        "{STAR_PRD_NAME}" => STAR_PRD_NAME,
                ));
            if(@$templ_prd_one2!="") { $out_remember['{PRODUCT_SPECIAL_TYPE}']=$templ_prd_one2; }
$out_remember['{PRODUCT_NNN}']=$url_val;

if($_SESSION['customers_id']>0) { $out['{PRODUCT_NAZV_ONTOP}']="product_nazv_ontop_cid";  } else { $out['{PRODUCT_NAZV_ONTOP}']="product_nazv_ontop"; }

////////////////////////////
// OUT_REMEBER --> OUT
//////////////////////////
// {PRODUCT_NAZV}{PRODUCT_PRICE}{PRODUCT_CATS}{PRODUCT_KEYWORDS}{PRODUCT_ATTRS}
// {PRODUCT_CODE}{PRODUCT_WEIGHT}{PRODUCT_SHOP}
// {FULL_BASKET_FORM}{BASKET_LINK}{PRODUCT_KOGDA}
// {PRODUCT_GRP_NAZV}{PRODUCT_DESCR}{PRODUCT_CONNECTS}
// {PRODUCT_IN_GRPS}{PRODUCT_RATE}{PRODUCT_RATE_YN}{PRODUCT_IMG}{PRODUCT_IMG_ALL}
// {PRODUCT_PAGE_BASKET}{PRODUCT_SPECIAL_TYPE}{PRODUCT_NNN}{PRODUCT_NAZV_ONTOP}
              
            $p=explode(",",PRODUCT_ELEMENTS);
            foreach($p as $o=>$o2) { if(isset($out_remember[trim($o2)])) { $out[trim($o2)]=$out_remember[trim($o2)]; } }



?>