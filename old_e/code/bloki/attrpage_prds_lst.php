<?php
if(isset($info['4bloks'])) {
$out['{PAGE_NAZV}']=ATTR_TXT_1;

if(@$info['4bloks']['detected'][0]=="all") { $arr="all"; } else { $arr=@$info['4bloks']['detected']['attr_name']; }

$str="";
if(count(@$info[@$arr])>0) { foreach($info[$arr] as $arr2=>$arr3) { 
    if($arr=="all") { $l=$arr2."___all"; } else { $l=$arr2; }
    $str.="— <a href=".MAINURL."/attr/".$l.">".$arr3."</a><p></p>";
    }
    $out['{PAGE_ALL}']="<p></p><div class='attr_lst'>".$str."</div>";
    }
        

if($info['4bloks']['detected']['attr_name']!="") { // есть свойство!

 $out['{PAGE_NAZV}']=$info['4bloks']['detected']['attr_name'];
 $out['{PAGE_FLAG}']="<div class=\"page_flag\">[свойство]</div>";

        if(isset($info['4bloks']['all_attrs'])) { // пара свойство - _все_ значения    
        } else { // пара свойство-значение

        if($info['4bloks']['detected']['attr_val']!="") {
        $out['{PAGE_NAZV}']="<a href=".MAINURL."/attr/".$info['4bloks']['detected_nnn']."___all>".$out['{PAGE_NAZV}']."</a> &rarr; ".$info['4bloks']['detected']['attr_val']; }
        if($info['4bloks']['detected']['attr_descr']!="") {
        $out['{PAGE_DESCR}']="<div id=\"pagedescr\" class=\"page_descr\" style=\"margin-top:20px;\">".textprocess(@$info['4bloks']['detected']['attr_descr'])."</div>"; }

        $tp1_top=@$info['4bloks']['tp1_top'];
        $tp1=@$info['4bloks']['tp1'];
        $out['{SORT_PRDS}']=@substr(@$info['4bloks']['sort_arr2'],0,-2);
        if(@$tp1['templ_prd']!="") {  $out['{PRDS_LISTING}']="<div class=\"products_list\" style=\"clear:right;\"><p></p>".@$tp1['templ_prd']."</div>";    }
        if(@$tp1_top['templ_prd']!="") { $out['{PRDS_LISTING_TOP}']="<div class=\"products_list\">".@$tp1_top['templ_prd']."</div>";    }
        if(@$out['{PRDS_LISTING_TOP}']!=""||@$out['{PRDS_LISTING}']!="") {
            $out['{PRDS_LISTING_NAME}']="<div class=\"page_prds_listing_name\">Товары</div>";
            $out['{PRDS_DIVIDER}']="<hr class=\"prds_divider\">";   }

        }           
}

}
?>