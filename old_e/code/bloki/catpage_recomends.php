<?php
if(@$url_val!=""&&$url_val!="all"&&$url_val!="new"&&$url_val!="comments"&&$url_val!="ratings") {
    $rate=new ratings();
    $connected=$rate->cats2cats(@$url_val);
    if(count($connected)>0) {
    if(!is_object($cats)) { $cats=new categories(); }
    if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']
    $str="";
    foreach($connected as $z) { if($look['podrazdel'][$z]['type']=="shop") { continue; }
            if($look['podrazdel'][$z]['remote_addr']!=""&&$look['podrazdel'][$z]['remote_always']=="1") {} else { $look['podrazdel'][$z]['remote_addr']=MAINURL."/catalog/".$z; }
            $str.="<a href=".$look['podrazdel'][$z]['remote_addr'].">".$look['podrazdel'][$z]['nazv']."</a><p></p>";  }
            if($str!="") {
    $out['{POPULAR_CATS}']="<div class='catpage_recomends'><div class='popcat_title'>Обратите внимание:</div>".@$str."</div>"; }
            }}
?>
