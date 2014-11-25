<?php
$out["{ATTR_2}"]="";
/*
if(!is_object(@$attrs)) { $attrs=new attribs; } // ���� ����� ��� �� ��������

if($url!="/catalog/") { $between="<p class='commonp'></p>";
    $manuf_cloud=$attrs->gather(mb_strtolower(ATTR_2_NAME),SHOP_NNN,"10"); $manuf_str="{ATTR_2}"; $attrs_str2="attr_title"; $attrs_str3="<p style=\"margin:5px 0 0 0;padding:0;\"></p>";
    } else { if($url_val!="new"&&$url_val!="all"&&$url_val!="comments"&&$url_val!="ratings") { $between="&nbsp; ";
        $manuf_cloud=$attrs->gather(mb_strtolower(ATTR_2_NAME),$url_val,"10"); $manuf_str="{ATTR_2_CAT}";  $attrs_str2="attr_title_page";
    $attrs_str3="<hr class=\"divider\">"; }}

if(count(@$manuf_cloud)>0) { $v2="";
            foreach($manuf_cloud[mb_strtolower(ATTR_2_NAME)] as $k => $v) {
                $v2=$v2."<a href=".MAINURL."/attr/".$k.">".$v."</a>".$between;
                }


            $out[$manuf_str]="<span class=\"".$attrs_str2."\">".ATTR_2_TXT_1."</span>".$attrs_str3.$v2."
                <span class=\"attr_title\">&rarr; <a href=\"".MAINURL."/attr/".$k."___all\">".ATTR_2_TXT_2."</a></span>";
}*/