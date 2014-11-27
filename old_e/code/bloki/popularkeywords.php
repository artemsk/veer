<?php
$out["{POPULARKEYWORDS}"]="";
$keyws=new keyws;

if($url!="/catalog/") {
    $pop_keyws=$keyws->gather(); $pop_keyws_str="{POPULARKEYWORDS}";
    if(@$pop_keyws!="") {
        $out[$pop_keyws_str]="<span class=\"keyword_title\">".KEYWORDS_TXT_1."</span>
                <p style=\"margin:5px 0 0 0;padding:0;\"></p>".$pop_keyws."
                <span class=\"keyword_title\">&rarr; <a href=\"".MAINURL."/keyword/all\">".KEYWORDS_TXT_2."</a></span>";
        }
} else { if($url_val!="new"&&$url_val!="all"&&$url_val!="comments"&&$url_val!="ratings") {

    $pop_keyws=$keyws->gather(trim($url_val)); $pop_keyws_str="{POPULARKEYWORDS_CAT}";
    if(@$pop_keyws!="") {

               $out[$pop_keyws_str]="<span class=\"keyword_title_page\">".KEYWORDS_TXT_1."</span>
                <hr class=\"divider\">".$pop_keyws."
                <span class=\"keyword_title\">&rarr; <a href=\"".MAINURL."/keyword/all\">".KEYWORDS_TXT_2."</a></span>";

    }}
}


?>