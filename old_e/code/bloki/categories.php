<?php
// in -> url, url_val, info
$out["{CATEGORIES}"]="";

$cats=new categories;
if($url!="/catalog/") {
$out["{CATEGORIES_ANCHOR}"]="<span id=\"catlist\"> </span>";
$out["{CATEGORIES}"]="<div id=\"catcontent\">".$cats->show_tree(SHOP_NNN,"")."</div>";
    } else {
        if($url_val=="all") { $out_key="{PAGE_ALL}"; $out_key_2="{PAGE_ALL_ANCHOR}"; } else { $out_key="{CATEGORIES}"; $out_key_2="{CATEGORIES_ANCHOR}"; }
        
        $out[$out_key]=$cats->show_tree(SHOP_NNN, $info);
        $out[$out_key_2]="<span id=\"catlist\"> </span>";
        if($url_val=="all") { $out[$out_key]="<div id=\"catcontent\" style=\"margin-left:35px;margin-top:35px;font-size:0.8em;\">".$out[$out_key]."</div>"; } else {
        $out[$out_key]="<div id=\"catcontent\">".$out[$out_key]."</div>"; }
}
?>