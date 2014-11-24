<?php

// big piс & product of the day (когда будет готова бд) TODO: pofd сюда
$out["{TOP_MAINPAGE_PIC}"]="";
$out["{HEADER_NAME_CSS}"]="";
$out["{HEADER_NAME_PRD}"]="";

$bigpic=explode(",",HEAD_IMGS);
$bigpic2=MAINURL."/template/".TEMPLATE."/images/".$bigpic[array_rand($bigpic)];
$bigpic2_thumb="template/".TEMPLATE."/images/thumb/";

$showprd_flag=1;
if($url=="/page/") { $showprd_flag=0; $bigpic2_backup=$bigpic2; $bigpic2_thumb_backup=$bigpic2_thumb;
    if(@$info['4bloks']['show_in_bigpic']!="") { $bigpic2=MAINURL."/upload/pages/".$info['4bloks']['show_in_bigpic'];
    $bigpic2_thumb="upload/pages/thumb/"; } }


if($showprd_flag=="1") {
$showprd=new products();
$prd=$showprd->collect_products(SHOP_NNN,'main'); // TODO: Ё“ќ√ќ ƒ≈Ћј“№ Ќ≈Ћ№«я!
$prd_temp=$prd['group_by_cat']; $prd_temp2=$prd['group_by_shop']; unset($prd['group_by_cat'], $prd['group_by_shop']);
$check=array_rand($prd); //$check="1003"; //2163 2269
if($check=="") { $check=PRD_ZAGLUSHKA; }
$prd['group_by_cat']=$prd_temp; $prd['group_by_shop']=$prd_temp2; unset($prd_temp, $prd_temp2);
$prd_temp=explode(";",$prd[$check]['img']);
$prd_tiny=$showprd->show_product($prd[$check], $check, "tiny"); // тормоз
unset($prd[$check]); $_SESSION['check_used']=$check;

$tiny_nazv_max=45;
}

if($prd_tiny['nazv']!=""&&$showprd_flag=="1") { 
    $header_name_css="header_name"; 
//$size=@getimagesize(MAINURL."/upload/".$prd_temp);
//if(@$size[0]>=600) { $back_img=imgprocess(MAINURL."/upload/".$prd_temp,"600","0","1");  } else {
    $back_img=imgprocess($bigpic2,"350","0","1",$bigpic2_thumb);
//}
    $bigpic3="<div class=\"bigpic\" style=\"background-image:url(".$back_img."); background-repeat:repeat-x; background-position:center; height:230px;\"></div>";
    $chosen_img=imgprocess(MAINURL."/upload/".@$prd_temp[0],"0","100");
    if(strlen($prd_tiny['nazv'])>$tiny_nazv_max) {
        $alt_nazv=$prd_tiny['nazv'];
        $prd_tiny['nazv']=wordwrap(substr($prd_tiny['nazv'],0,$tiny_nazv_max)."...",15," <wbr>",1);
        //if(strlen($prd_tiny['all_prices']['price_formated'])>120) { $prd_tiny['nazv']="<font style='font-size:0.85em;'>".$prd_tiny['nazv']."</font>"; }
    } else { $alt_nazv=$prd_tiny['nazv']; } 
    $chosen_str="<a id=\"bigpic_nazv\" title=\"".@$alt_nazv."\" href=".$prd_tiny['nazv_link'].">".$prd_tiny['nazv']."</a>";
    if($prd_tiny['all_prices']['price_formated']!="") {
        $chosen_str.="<p class=\"big_price\">".$prd_tiny['all_prices']['price_formated']."</p>";
    }

    $top_prd="<div style=\"width:150px;overflow:hidden;\"><img id=\"bigpic_img\" src=\"".$chosen_img."\"></div><p style=\"margin:7px 0 0 0;padding:0;\"></p>".$chosen_str;


} else { // показывать “ќЋ№ ќ большу картинку!
    $header_name_css="header_name_blank";  
    $bigpic3_tmp=imgprocess($bigpic2,"530","0","1",$bigpic2_thumb,"arr");

    if($url=="/page/"&&$bigpic3_tmp['endwidth']<500) { $out['{BOTTOM_PAGE_PIC}']="<img src=".$bigpic3_tmp['fotoname'].">";
    $bigpic3_tmp=imgprocess($bigpic2_backup,"530","0","1",$bigpic2_thumb_backup,"arr"); }

    $bigpic3="<div class=\"article_bigpic\" style=\"background-image:url(".$bigpic3_tmp['fotoname']."); background-repeat:no-repeat; background-position:center left;\"></div>";
}
unset($prd_tiny);

$out["{TOP_MAINPAGE_PIC}"]=@$bigpic3;
$out["{HEADER_NAME_CSS}"]=@$header_name_css;
$out["{HEADER_NAME_PRD}"]=@$top_prd;
$out["{HEADER_TOP_CSS}"]="header_top_css";
                     
// out -> $top_prd, bigpic3, header_name_css


// out -> $prd - каким-то образом передать массив дальше!

?>