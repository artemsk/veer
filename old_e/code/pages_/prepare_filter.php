<?php
if($detected[1]=="") { exit; } // TODO: 404 page

// �������� ������
$body_filename=MAINURL_5."/template/".TEMPLATE."/body_index.php";

/*
// [A] �������� info
////////////////////////
    $category=new categories;
    if($detected[1]=="all") { $info=$category->gather(SHOP_NNN,"full"); } else { $info=$category->gather($detected[1],"brief"); }

    
// [C] �������� ������
/////////////////////////////////////

    // products
        if(!isset($detected['more_pages'])&&@$detected['more_pages']<1) { $detected['more_pages']="1"; }
         // ����������
            if(!isset($detected['sort_type'])) { $detected['sort_type']="star"; }
            if(!isset($detected['sort_direction'])) { $detected['sort_direction']="desc"; }             
             
    $products=new products;
            $prds=$products->collect_products($detected[1], 'cat', '0', '0', $detected['more_pages'], 
                    $detected['sort_type'], $detected['sort_direction']);



    if(count($prds)>0) {

//            $prds=$products->sort_products($prds, "shopcat_limit", "asc", CATPAGE_LIMIT); // ��� �������� ��� �����������
//            $prds=$products->sort_products($prds, $detected['sort_type'], $detected['sort_direction']);

            $sort_arr=sort_links($detected); $sort_arr2="";
            foreach($sort_arr as $k=>$v) { $sort_arr2.="<a href=".$k."#prdlist>".$v."</a>, "; }
            $info['4bloks']['sort_arr2']=$sort_arr2;
    
    if(count($prds)>=CATPAGE_PICS_LST_FLAG&&CATPAGE_PICS_LST_FLAG>0) {
    for($i=0;$i<CATPAGE_PICS_LST_NUMS;$i++) { $check_img[$i]=$prds[array_rand($prds)]['img']; }
    $info['4bloks']['check_img']=$check_img; }

    if(!is_array($cats)) { $cats=new categories(); }
    $look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']

    foreach($prds as $k=>$v) { if($prds[$k]['smart_sort']=="1") { $prds_dva[$k]=$v; unset($prds[$k]); }} // �������� ������ �� �����������

    if(@$detected[1]=="new") {
    $templ_catpage_prds_top=$products->product_listing($prds, $look, "0","","4","4");
    $templ_catpage_prds=$products->product_listing($prds, $look, "0",@$templ_catpage_prds_top['used'],"5","0");
        } else { // ������� ��� ���

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

    } // ������� ��� ���
    
    $info['4bloks']['tp1_top']=@$templ_catpage_prds_top;
    $info['4bloks']['tp1']=@$templ_catpage_prds;
    $info['4bloks']['tp2_top']=@$templ_catpage_prds_dva_top;
    $info['4bloks']['tp2']=@$templ_catpage_prds_dva;

    } else { if(@$detected['more_pages']>1) { exit; }} 
    */