<?php
if(isset($info['4bloks'])) { 
$tp1_top=@$info['4bloks']['tp1_top'];
$tp1=@$info['4bloks']['tp1'];

$out['{SORT_PRDS}']=@substr(@$info['4bloks']['sort_arr2'],0,-2);

if(!isset($info['4bloks']['all_keywords'])) { $out['{PAGE_FLAG}']="<div class=\"page_flag\">[�������� �����]</div>"; } else {
    $out['{PAGE_ALL}']="<p></p>".$info['4bloks']['all_keywords'];
    }
}
?>