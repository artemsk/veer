<?php
if($detected[1]=="") { exit; } // TODO: 404 page

// comments pages
if($detected[1]=="comments") {
    $title_head="комментарии";
    $comments=new products;
    $comms=$comments->show_comments();
    exit;
}

if($detected[1]=="new") { $title_head="новые поступления"; }
if($detected[1]=="all") { $title_head="все разделы"; }
// [A] собираем info
////////////////////////
    $category=new categories;
    if($detected[1]=="all") { $info=$category->gather(SHOP_NNN,"full"); } else { $info=$category->gather($detected[1],"brief"); }
    if($detected[1]!="new"&&$detected[1]!="ratings") { if(count(@$info)<=0) { exit; }} // TODO: 404 page

    
    if($detected[1]=="ratings") {
    $title_head="рейтинги";
    $info['4bloks']['only_4_ratings']="1";
    }


// [B] собираем новости
/////////////////////////////////
    if($detected[1]!="all"&&$detected[1]!="new"&&$detected[1]!="comments"&&$detected[1]!="ratings") {

    $title_head=$info['nazv'];
    
    $cat_pages=new pages;
    $page_content=$cat_pages->show_pages($detected[1],'news');

    if(count(@$page_content[1])>0) { 
        $news_arr=$cat_pages->prepare_news($page_content[1], 15, 1,array("news_dat_page","news_pic_page","news_txt_page","news","news_imp"));
        $info['4bloks']['news_arr']=$news_arr;
        }
    }


// [C] собираем товары
/////////////////////////////////////
    if($detected[1]!="all"&&$detected[1]!="ratings"&&$detected[1]!="comments") { // !all

    // products
        if(!isset($detected['more_pages'])&&@$detected['more_pages']<1) { $detected['more_pages']="1"; }
         // сортировка
            if(!isset($detected['sort_type'])) { $detected['sort_type']="star"; }
            if(!isset($detected['sort_direction'])) { $detected['sort_direction']="desc"; }             
            if(@$detected[1]=="new") {  $detected['sort_type']="status"; $detected['sort_direction']="asc"; }
             
    $products=new products;
        if($detected[1]=="new") { $prds=$products->collect_products(NEWPRODS_LIMIT, 'new'); } else { 
            $prds=$products->collect_products($detected[1], 'cat', '0', '0', $detected['more_pages'], 
                    $detected['sort_type'], $detected['sort_direction']);

        }

    if(count($prds)>0) {

//            $prds=$products->sort_products($prds, "shopcat_limit", "asc", CATPAGE_LIMIT); // для корневых нет ограничений
//            $prds=$products->sort_products($prds, $detected['sort_type'], $detected['sort_direction']);

            $sort_arr=sort_links($detected); $sort_arr2="";
            foreach($sort_arr as $k=>$v) { $sort_arr2.="<a href=".$k."#prdlist>".$v."</a>, "; }
            $info['4bloks']['sort_arr2']=$sort_arr2;
    
    if(count($prds)>=CATPAGE_PICS_LST_FLAG&&CATPAGE_PICS_LST_FLAG>0) {
    for($i=0;$i<CATPAGE_PICS_LST_NUMS;$i++) { $check_img[$i]=$prds[array_rand($prds)]['img']; }
    $info['4bloks']['check_img']=$check_img; }

    if(!is_array($cats)) { $cats=new categories(); }
    $look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']

    foreach($prds as $k=>$v) { if($prds[$k]['smart_sort']=="1") { $prds_dva[$k]=$v; unset($prds[$k]); }} // отделяем товары из подразделов

    if(@$detected[1]=="new") {
    $templ_catpage_prds_top=$products->product_listing($prds, $look, "0","","4","4");
    $templ_catpage_prds=$products->product_listing($prds, $look, "0",@$templ_catpage_prds_top['used'],"5","0");
        } else { // новинки или нет

    $maxprd=PRDS_PER_PAGE;
    if($maxprd>0) {
        
    $o=prds_show_more(count($prds), $detected['more_pages'], $maxprd);
    $start_from=$o['start_from'];
    $info['4out']['{SHOW_MORE}']=$o['4out']['{SHOW_MORE}'];
    $info['4bloks']['BREAK_OUTPUT_FLAG']=$o['4bloks']['BREAK_OUTPUT_FLAG'];
    $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=$o['4bloks']['BREAK_OUTPUT_FILE_BODY'];
    }
    
    if(count($prds)<=4) {
    $templ_catpage_prds_top=$products->product_listing($prds, $look, "0","","4","4");
    } else { $templ_catpage_prds=$products->product_listing($prds, $look, "0",@$templ_catpage_prds_top['used'],"5",$maxprd,"","0",$start_from); }

    if(count($prds_dva)<=4&&count($prds)<=0) {
    $templ_catpage_prds_dva_top=$products->product_listing($prds_dva, $look, "0","","4","4");
    } else { $templ_catpage_prds_dva=$products->product_listing($prds_dva, $look, "0",@$templ_catpage_prds_dva_top['used'],"5",$maxprd,"","0",$start_from); }

    } // новинки или нет
    
    $info['4bloks']['tp1_top']=@$templ_catpage_prds_top;
    $info['4bloks']['tp1']=@$templ_catpage_prds;
    $info['4bloks']['tp2_top']=@$templ_catpage_prds_dva_top;
    $info['4bloks']['tp2']=@$templ_catpage_prds_dva;

    } else { if(@$detected['more_pages']>1) { exit; }} }  
    