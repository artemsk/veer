<?php
$out["{POPULARATTRS}"]="";
if(!is_object($attrs)) { $attrs=new attribs; } // если класс еще не объ€влен
if($url!="/catalog/") {
    $attrs_cloud=$attrs->gather(); $attrs_str="{POPULARATTRS}"; $attrs_str2="attr_title"; $attrs_str3="<p style=\"margin:5px 0 0 0;padding:0;\"></p>";
    } else { if($url_val!="new"&&$url_val!="all"&&$url_val!="comments"&&$url_val!="ratings") {
        $attrs_cloud=$attrs->gather("all",$url_val); $attrs_str="{POPULARATTRS_CAT}"; $attrs_str2="attr_title_page"; $attrs_str3="<hr class=\"divider\">"; }}

if(count(@$attrs_cloud)>0) { $v2="";
            foreach($attrs_cloud['all'] as $k => $v) {
                $v2=$v2." &nbsp;<a href=".MAINURL."/attr/".$k."___all>".$v."</a>";
                }


            $out[$attrs_str]="<span class=\"".$attrs_str2."\">".ATTR_TXT_1."</span>".$attrs_str3.$v2."
                <span class=\"attr_title\">&rarr; <a href=\"".MAINURL."/attr/all\">".ATTR_TXT_2."</a></span>";
}?>
