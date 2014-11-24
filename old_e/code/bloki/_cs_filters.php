<?php

$get_url = explode("_", $url_val);
$url_parse = array();
$used_filters['catalog'] = SHOP_NNN;

$out['{FILTER_CATS_FIRST}'] = "Все разделы";
$out['{FILTER_MANUF_FIRST}'] = "Все фабрики";

if($url_val == SHOP_NNN && $url == "") { $chosen_filters['cat'] = ""; } else {    
    
    if($url == "/filter/") {
        foreach($get_url as $k => $v) {
            if(trim($v) == "") { continue; } 
            if($k == 0) { $chosen_filters['cat'] = $v; $url_parse[] = $v; } else {             
                  $chosen_filters['attr'][$v] = $v; $url_parse[] = $v;
            }
            if($k == 0 && $v > 0) { $used_filters['catalog'] = $v; }
            if($k != 0 && $v > 0 && $v != "all") { $used_filters['attrs'][$v] = $v; }
        }
    }   
    
} 

if(!is_array($filt)) { $filt = new filters(); }
$filt2 = $filt->gather($used_filters['catalog'], @$used_filters['attrs']);

if(!is_array($cats)) { $cats=new categories(); }
$look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']

    $url_parse2 = $url_parse;
    unset($url_parse2[0]);
    
$out['{FILTER_CATS}'] = "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"".MAINURL."/filter/0_".@implode("_",$url_parse2)."\" class=\"drdw-lnk\">".$out['{FILTER_CATS_FIRST}']."</a></li>";

$look2 = simplifyarr($look['podrazdel']);
asort($look2[1]);

if(is_array($look2[1])) {
foreach($look2[1] as $k => $v) {
    if(substr($k,-5) == "#nazv") {
        $key = substr($k,1,-5);
    if(is_array($filt2['catalog'])) { if(!isset($filt2['catalog'][$key])) { continue; }}
    
    $out['{FILTER_CATS}'] = $out['{FILTER_CATS}']."<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"".MAINURL."/filter/".
            $key."_".@implode("_",$url_parse2)."\" class=\"drdw-lnk\">".$v."</a></li>\n";
    if($key == $used_filters['catalog']) { $out['{FILTER_CATS_FIRST}'] = "<span class=\"filtered-menu-element\">".$v."</span>"; }
    }}}
    
if(count($used_filters['attrs']) == 1) {
    foreach($used_filters['attrs'] as $k => $v) { $only_attrib = $v; break; }
}
    
if(!is_object(@$attrs)) { $attrs=new attribs; } 

$attrs_manuf = $attrs->gather("производитель");

    $url_parse2 = $url_parse;
    unset($url_parse2[1], $url_parse2[0]);
    
    if($url_parse[0] == "") { $url_parse[0] = 0; }
    
$out['{FILTER_MANUF}'] = "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"".MAINURL."/filter/".$url_parse[0]."_all_".@implode("_",$url_parse2)."\" class=\"drdw-lnk\">".$out['{FILTER_MANUF_FIRST}']."</a></li>";

asort($attrs_manuf['производитель']);

if(is_array($attrs_manuf['производитель'])) {
foreach($attrs_manuf['производитель'] as $k => $v) {
    if(is_array($filt2['attrs']['производитель']) && !isset($attrs_manuf['производитель'][$only_attrib])) { 
        if(!isset($filt2['attrs']['производитель'][$v])) { continue; }}
    $out['{FILTER_MANUF}'] = $out['{FILTER_MANUF}']."<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"".MAINURL."/filter/".
            $url_parse[0]."_".$k."_".@implode("_",$url_parse2)."\" class=\"drdw-lnk\">".$v."</a></li>\n";
    if(isset($used_filters['attrs'][$k])) { $out['{FILTER_MANUF_FIRST}'] = "<span class=\"filtered-menu-element\">".$v."</span>"; }
    }}

$attrs_tree = $attrs->gather();
$use_attrs = array_flip(explode(",",FILTER_ATTRS));
$use_attrs_names = explode(",",FILTER_ATTRS_NAMES);


$additional_attrs = 2;

foreach($attrs_tree['all'] as $k => $v) {
        
     if(isset($use_attrs[$v])) {

         $use_attr = $attrs->gather($v);         
        
         $url_parse2 = $url_parse;
         unset($url_parse2[1], $url_parse2[0]);
         //unset($url_parse2[$additional_attrs]);         
        if($url_parse[0] == "") { $url_parse[0] = 0; }
        if($url_parse[1] == "") { $url_parse[1] = "all"; }
        
        $out['{FILTER_ATTR_'.$additional_attrs.'}'] = "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"".MAINURL."/filter/".$url_parse[0]."_".$url_parse[1]."_";
        $str = ""; $str_before = ""; $str_after = "";
        if(count($url_parse2)>0) {
        foreach($url_parse2 as $kk=>$vv) { 
            
            if($kk == $additional_attrs) { $out['{FILTER_ATTR_'.$additional_attrs.'}'].= "all_"; $str_before = $str; $str = ""; } else {
                $out['{FILTER_ATTR_'.$additional_attrs.'}'].= $vv."_"; $str.=$vv."_";
            }
        }
        } else {
            for($j=2;$j<=$additional_attrs;$j++) { $out['{FILTER_ATTR_'.$additional_attrs.'}'].="all_"; 
            if($j!=$additional_attrs) { $str_before.="all_"; }}
        }

        $str_after = $str;
  
        $out['{FILTER_ATTR_'.$additional_attrs.'}'].="\" class=\"drdw-lnk\">".trim($use_attrs_names[$use_attrs[$v]])."</a></li>";
        $out['{FILTER_ATTR_'.$additional_attrs.'_FIRST}'] = trim($use_attrs_names[$use_attrs[$v]]);
        
        if(is_array($use_attr[$v])) {
        foreach($use_attr[$v] as $kkk => $vvv) {            
            if(is_array($filt2['attrs'][$v]) && !isset($use_attr[$v][$only_attrib])) { if(!isset($filt2['attrs'][$v][$vvv])) { continue; }}
            $out['{FILTER_ATTR_'.$additional_attrs.'}'] = $out['{FILTER_ATTR_'.$additional_attrs.'}']."<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"".MAINURL."/filter/".$url_parse[0]."_".$url_parse[1]."_".$str_before.$kkk."_".$str_after."\" class=\"drdw-lnk\">".$vvv."</a></li>\n";
            if(isset($used_filters['attrs'][$kkk])) { $out['{FILTER_ATTR_'.$additional_attrs.'_FIRST}'] = "<span class=\"filtered-menu-element\">".$vvv."</span>"; }
        }}
 
        $additional_attrs++;
     }
}

// output: products
if(is_array($filt2['products'])) {

    if(count($filt2['products'])> 0) {
        $showprd=new products();
        $prd=$showprd->collect_products($filt2['products'],'nnn_array');       

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

    } else {
        $flag_no_show = 1;
    }

    
} else {   
    
    if($used_filters['catalog'] == SHOP_NNN && !is_array($used_filters['attrs'])) {
        if (file_exists(MAINURL_5 . "/code/bloki/_cs_chosenproducts.php")) {
                    require(MAINURL_5 . "/code/bloki/_cs_chosenproducts.php");
        }
    } else {
    
    }
      
}

// TODO: ничего не найдено
// TODO: сортировка
// TODO: ограничить выдачу и кол-во запросов
