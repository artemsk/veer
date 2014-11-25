<?php

if(!is_array(@$prd)) { // передали из bigpic
$showprd=new products();
$prd=$showprd->collect_products(SHOP_NNN,'main'); 
}

if(!is_array($cats)) { $cats=new categories(); }
$look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']

$template_products=$showprd->product_listing($prd, $look, 0, array(), 999, 15, 0, 0, 0, 1, 1);

$kkey = 0;
$mp_array = array();

ksort($template_products['templ_prd']);

foreach ($template_products['templ_prd'] as $k => $v) {
    //$out['{MP_'.$kkey.'_IMG}'] = imgprocess(MAINURL."/upload/".$v['img_full'],"0",IMG_HEIGHT_LIST_MAX); //
    $out['{MP_'.$kkey.'_IMG}'] = MAINURL."/upload/".$v['img_full'];
    $out['{MP_'.$kkey.'_IMGFULL}'] = MAINURL."/upload/".$v['img_full']; //
    $out['{MP_'.$kkey.'_NAZV}'] = $v['nazv']; //
    $out['{MP_'.$kkey.'_BASKET}'] = $v['full_basket_form'];        
    $out['{MP_'.$kkey.'_NAZVLNK}'] = $v['nazv_link'];
    $out['{MP_'.$kkey.'_PRICE}'] = $v['all_prices']['price_formated']; // 
    $out['{MP_'.$kkey.'_CAT}'] = $look['podrazdel'][$v['shop_cat_priority']]['nazv']; //
    $out['{MP_'.$kkey.'_CATLNK}'] = MAINURL."/catalog/".$v['shop_cat_priority']; //
    $kkey++;

    }
