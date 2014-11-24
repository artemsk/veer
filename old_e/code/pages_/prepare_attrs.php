<?php
if($detected[1]=="") { exit; }

if($detected[1]=="all") { $attr_showtype="all"; $detected3['0']="all"; $title_head=ATTR_TXT_1; } else { // all для свойств, а не значений
$check_all=explode("___",$detected[1]);
if(count($check_all)>1) { $detected[1]=trim($check_all[0]); $attr_showtype="all"; } else { $attr_showtype="one"; }
$detected2=mysql_kall("SELECT attr_name, attr_val, attr_descr FROM ".DB_PREFIX."products_attr WHERE nnn='".$detected[1]."'") or die(mysql_error());
$detected3=mysql_fetch_assoc($detected2);
if(mysql_num_rows($detected2)<=0||@$detected3['attr_name']==""||@$detected3['attr_val']=="") { exit; }
$detected3['0']=$detected3['attr_name'];
$detected3['1']=$detected3['attr_val']; $title_head=$detected3['0']; if($attr_showtype=="one") { $title_head.=" - ".$detected3['1']; }
} // !all attr_name

if($attr_showtype=="all") { // info / all
$attrib=new attribs;
$info=$attrib->gather($detected3['0']);
$info['4bloks']['all_attrs']=1;
}

$info['4bloks']['detected']=@$detected3;
$info['4bloks']['detected_nnn']=$detected[1];

if(@$attr_showtype!="all") { // !all / products

$products=new products;

    // сортировка
    if(!isset($detected['sort_type'])) { $detected['sort_type']="star"; }
    if(!isset($detected['sort_direction'])) { $detected['sort_direction']="desc"; }
    if(!isset($detected['more_pages'])&&@$detected['more_pages']<1) { $detected['more_pages']="1"; }
    
$prds=$products->collect_products($detected3, 'attr', 0, 0, $detected['more_pages'], 
                    @$detected['sort_type'], @$detected['sort_direction']);
$prds=$products->collect_pig($prds);

    if(count($prds)>0) {

    
    //$prds=$products->sort_products($prds, $detected['sort_type'], $detected['sort_direction']);
    $sort_arr=sort_links($detected,"attr"); $sort_arr2="";
    foreach($sort_arr as $k=>$v) { $sort_arr2.="<a href=".$k."#prdlist>".$v."</a>, "; }

    $info['4bloks']['sort_arr2']=$sort_arr2;

    if(count($prds)>=CATPAGE_PICS_LST_FLAG&&CATPAGE_PICS_LST_FLAG>0) {
    for($i=0;$i<CATPAGE_PICS_LST_NUMS;$i++) { $check_img[$i]=$prds[array_rand($prds)]['img']; }
    $info['4bloks']['check_img']=$check_img; }

    if(!is_array($cats)) { $cats=new categories(); }
    $look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']

    if(@$detected['sort_type']=="shopcat") { $grp_flag_keyw=1; $maxprd=0; } else { $grp_flag_keyw=0; $maxprd=PRDS_PER_PAGE; }

    if($maxprd>0) {
    $o=prds_show_more(count($prds), $detected['more_pages'], $maxprd);
    $start_from=$o['start_from'];
    $info['4out']['{SHOW_MORE}']=$o['4out']['{SHOW_MORE}'];
    $info['4bloks']['BREAK_OUTPUT_FLAG']=$o['4bloks']['BREAK_OUTPUT_FLAG'];
    $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=$o['4bloks']['BREAK_OUTPUT_FILE_BODY'];
    }

    if(count($prds)<=4) {
    $templ_catpage_prds_top=$products->product_listing($prds, $look, $grp_flag_keyw,"","4","4","cat");
    } else { $templ_catpage_prds=$products->product_listing($prds, $look, $grp_flag_keyw,@$templ_catpage_prds_top['used'],"5",$maxprd,"cat","0",@$start_from); }

    $info['4bloks']['tp1_top']=@$templ_catpage_prds_top;
    $info['4bloks']['tp1']=@$templ_catpage_prds;

    } else { if(@$detected['more_pages']>1) { exit; }} 


} // !all