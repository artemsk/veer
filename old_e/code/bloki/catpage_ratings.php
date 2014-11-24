<?php  
if(@$info['4bloks']['only_4_ratings']=="1") {

    $info['nazv']="Рейтинги";

    if(!is_object($rating)) { $rating=new ratings; }

    // pop month
    $prd_lst=$rating->pop("views","month","10");
    if(count($prd_lst['prds'])>0) {
    if(!is_object($cats)) { $cats=new categories(); }
    if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
    if(!is_object($showprd)) { $showprd=new products(); }
    $templ_prd99=$showprd->product_listing($prd_lst['prds'], $look, '0', @$templ_prd3['used'],'4',RATING_PAGE_VIEWS_LIMIT);
    $templ_prd3['used']=$templ_prd99['used'];    }
    if(@$templ_prd99['templ_prd']!="") {
    $out['{RATING_PAGE_VISITS}']="<div style=\"text-align:left;margin-left:45px;\">
        <div class=\"page_prds_listing_name\">".@$prd_lst['zag']."</div><hr class=\"prds_divider\"></div>
        <div class=\"products_list\">".@$templ_prd99['templ_prd']."</div>"; unset($templ_prd99['templ_prd']);
    }

    // order month
    $prd_lst=$rating->pop("orders","month","10");
    if(count($prd_lst['prds'])>0) {
    if(!is_object($cats)) { $cats=new categories(); }
    if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
    if(!is_object($showprd)) { $showprd=new products(); }
    $templ_prd99=$showprd->product_listing($prd_lst['prds'], $look, '0', @$templ_prd3['used'],'5',RATING_PAGE_ORDERS_LIMIT);
    $templ_prd3['used']=$templ_prd99['used'];    }
    if(@$templ_prd99['templ_prd']!="") {
    $out['{RATING_PAGE_ORDERS}']="<div style=\"text-align:left;margin-left:45px;\">
        <div class=\"page_prds_listing_name\">".@$prd_lst['zag']."</div><hr class=\"prds_divider\"></div>
        <div class=\"products_list\">".@$templ_prd99['templ_prd']."</div>"; unset($templ_prd99['templ_prd']);
    }

    // rates
    unset($prd_lst);
    $prd_lst=$rating->pop_rate(10);
    if(count($prd_lst)>0) { $prd_lst2=implode("' OR ".DB_PREFIX."products.nnn='",$prd_lst);
    if(!is_object($showprd)) { $showprd=new products(); }
    $prd_rec=$showprd->collect_products($prd_lst2,'nnn','1');
    if(!is_object($cats)) { $cats=new categories(); }
    if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
    $prd_rec=$showprd->sort_products($prd_rec, 'as_array', 'desc','0',$prd_lst);
    $templ_prd99=$showprd->product_listing($prd_rec, $look, '0', @$templ_prd3['used'],'5',RATING_PAGE_RATES_LIMIT);
    $templ_prd3['used']=$templ_prd99['used'];  }
    if(@$templ_prd99['templ_prd']!="") {
    $out['{RATING_PAGE_RATES}']="<div style=\"text-align:left;margin-left:45px;\">
        <div class=\"page_prds_listing_name\">Высшие оценки</div><hr class=\"prds_divider\"></div>
        <div class=\"products_list\">".@$templ_prd99['templ_prd']."</div>"; unset($templ_prd99['templ_prd']);
    }

    // popular cats у всех посетителей и у зарегистрированных
    $popcat="";
    $prd_lst=$rating->recomend_cats(0);
    if(!is_object($cats)) { $cats=new categories(); }
    if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
    $str="";
    if(count($prd_lst['lst'])>0) {
    foreach($prd_lst['lst'] as $z) {
    if($look['podrazdel'][$z]['remote_addr']!=""&&$look['podrazdel'][$z]['remote_always']=="1") {} else { $look['podrazdel'][$z]['remote_addr']=MAINURL."/catalog/".$z; }
    $str.="<a href=".$look['podrazdel'][$z]['remote_addr'].">".$look['podrazdel'][$z]['nazv']."</a><p></p>";
    }
    $popcat.="<div class='rating_page_popcat'><div class='popcat_title'>Популярные разделы у посетителей</div>".@$str."</div>";
    }
 
    $prd_lst=$rating->recomend_cats(0,LIMIT_POP_CAT,'1'); // все  
    if(!is_object($cats)) { $cats=new categories(); }
    if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
    $str="";
    if(count($prd_lst['lst'])>0) {
    foreach($prd_lst['lst'] as $z) {
    if($look['podrazdel'][$z]['remote_addr']!=""&&$look['podrazdel'][$z]['remote_always']=="1") {} else { $look['podrazdel'][$z]['remote_addr']=MAINURL."/catalog/".$z; }
    $str.="<a href=".$look['podrazdel'][$z]['remote_addr'].">".$look['podrazdel'][$z]['nazv']."</a><p></p>";
    }
    $popcat.="<div class='rating_page_popcat'><div class='popcat_title'>Популярные разделы у покупателей</div>".@$str."</div>";
    }

    
    $kwords=$rating->pop_keywords(@$templ_prd3['used']);
    if(count($kwords)>0) { $str="";
    foreach($kwords as $z1=>$z2) {
    $str.="<a href=".MAINURL."/keyword/".$z1.">".$z2."</a><p></p>";
    }
    $popcat.="<div class='rating_page_popcat'><div class='popcat_title'>Популярные ключевые слова</div>".@$str."</div>";
    }

    if($popcat!="") { $out['{RATING_PAGE_POPCAT}']=$popcat; }
    
    }
?>