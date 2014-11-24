<?php
if(isset($info['4bloks'])) {
$tp1_top=@$info['4bloks']['tp1_top'];
$tp1=@$info['4bloks']['tp1'];

$out['{SORT_PRDS}']=@substr(@$info['4bloks']['sort_arr2'],0,-2);

if(@$tp1['templ_prd']!="") {
    $out['{PRDS_LISTING}']="<div class=\"products_list\" style=\"clear:right;\"><p></p>".@$tp1['templ_prd']."</div>";
}
if(@$tp1_top['templ_prd']!="") {
    $out['{PRDS_LISTING_TOP}']="<div class=\"products_list\">".@$tp1_top['templ_prd']."</div>";
}

if(@$out['{PRDS_LISTING_TOP}']!=""||@$out['{PRDS_LISTING}']!="") {
    $out['{PRDS_LISTING_NAME}']="<div class=\"page_prds_listing_name\">Товары</div>";
    $out['{PRDS_DIVIDER}']="<hr class=\"prds_divider\">";
}

if(!isset($info['4bloks']['all_keywords'])) { $out['{PAGE_FLAG}']="<div class=\"page_flag\">[поиск]</div>"; }
}
?>