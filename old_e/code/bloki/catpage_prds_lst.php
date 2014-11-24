<?php

$news_arr=@$info['4bloks']['news_arr'];
$tp1_top=@$info['4bloks']['tp1_top'];
$tp1=@$info['4bloks']['tp1'];
$tp2_top=@$info['4bloks']['tp2_top'];
$tp2=@$info['4bloks']['tp2'];

// неиспольз: $info['nnn'], $info['status'], $info['nadrazdel']
// ###### OUT TEMPLATE ###############################################

$out['{PAGE_NAZV}']=@$info['nazv'];

if(@$info['descr']!=""&&@$news_arr['imp']!="") {
    $navig1="<div id=\"navig1\" class=\"page_navig\" style=\"display:block;\">Описание &middot; <a href=javascript:show('pagenews');hide('pagedescr');show('navig2');hide('navig1')>Новости</a></div>";
    $navig2="<div id=\"navig2\" class=\"page_navig\" style=\"display:none;\"><a href=javascript:show('pagedescr');hide('pagenews');show('navig1');hide('navig2')>Описание</a> &middot; Новости</div>";
    if(@$news_arr['all']!="") {
    $navig2="<div id=\"navig2\" class=\"page_navig\" style=\"display:none;\"><a href=javascript:show('pagedescr');hide('pagenews');show('navig1');hide('navig2')>Описание</a> &middot; Новости &middot; <a href=javascript:show('pagenews_full');hide('navig2');hide('pagenews');hide('prdsall');hide('prdsall_2')>Еще новости</a></div>";
    }
}
if(@$info['descr']!=""&&@$news_arr['imp']=="") {
    $navig1="<p></p>";
}

if(@$info['type']=="shop") {
    $out['{PAGE_FLAG}']="<div class=\"page_flag\">".CATSHOP_TEMPLATE_FLAG."</div>";
}
if(@$info['remote_addr']!="") { // remote_always не нужен
    $out['{PAGE_NAZV}']="<a href=".@$info['remote_addr'].">".@$info['nazv']."</a>";
}
if(@$info['descr']!="") {
    $out['{PAGE_DESCR}']=@$navig1."<div id=\"pagedescr\" class=\"page_descr\" style=\"display:block;\">".textprocess(@$info['descr'])."</div>";
}

$out['{SORT_PRDS}']=substr(@$info['4bloks']['sort_arr2'],0,-2);

if(@$tp1['templ_prd']!="") {
    $out['{PRDS_LISTING}']="<div class=\"products_list\" style=\"clear:right;\">".@$tp1['templ_prd']."</div>";
}
if(@$tp1_top['templ_prd']!="") {
    $out['{PRDS_LISTING_TOP}']="<div class=\"products_list\">".@$tp1_top['templ_prd']."</div>";
}
if(@$tp2['templ_prd']!="") {
    $out['{PRDS_LISTING_DVA}']="<div class=\"products_list\" style=\"clear:right;\">".@$tp2['templ_prd']."</div>";
}
if(@$tp2_top['templ_prd']!="") {
    $out['{PRDS_LISTING_DVA_TOP}']="<div class=\"products_list\">".@$tp2_top['templ_prd']."</div>";
}

if($out['{PRDS_LISTING_TOP}']!=""||$out['{PRDS_LISTING}']!="") {
    $out['{PRDS_LISTING_NAME}']="<div class=\"page_prds_listing_name\">Товары</div>";
    $out['{PRDS_DIVIDER}']="<hr class=\"prds_divider\">";
if($url_val=="new") { $out['{PRDS_LISTING_NAME}']="<div class=\"page_prds_listing_name_new\" style='color:#333333;'>Новинки</div>";
                      $out['{SORT_PRDS}']=""; $out['{PRDS_DIVIDER}']="<hr class=\"prds_divider_new\">"; }

}
if($out['{PRDS_LISTING_DVA_TOP}']!=""||$out['{PRDS_LISTING_DVA}']!="") {
    $out['{PRDS_LISTING_NAME_DVA}']="<div class=\"page_prds_listing_name\">Новые товары в подразделах</div>";
    $out['{PRDS_DIVIDER_DVA}']="<hr class=\"prds_divider\">";
    if($out['{PRDS_LISTING_TOP}']==""&&$out['{PRDS_LISTING}']=="") {
        $out['{SORT_PRDS_DVA}']=substr(@$info['4bloks']['sort_arr2'],0,-2);
        $out['{SORT_PRDS}']="";
    }
}

$pagenews_disp="none;";
if(@$info['descr']==""&&@$news_arr['imp']!="") {
    $pagenews_disp="block;";
    $navig2="<div id=\"navig2\" class=\"page_navig\" style=\"display:block;\">Новости";
    if(@$news_arr['all']!="") {
    $navig2.=" &middot; <a href=javascript:show('pagenews_full');hide('navig2');hide('pagenews');hide('prdsall');hide('prdsall_2')>Еще новости</a>"; }
    $navig2.="</div>";
}
if(@$news_arr['imp']!="") {
    $out['{CAT_NEWS_ONE}']=@$navig2."<div id=\"pagenews\" class=\"page_news\" style=\"display:".$pagenews_disp.";\">".$news_arr['imp']."</div>";
}
if(@$news_arr['all']!="") {
    $out['{CAT_NEWS_ALL}']="<div id=\"pagenews_full\" class=\"page_news_full\" style=\"display:none;\">".$news_arr['all']."</div>";
}
?>