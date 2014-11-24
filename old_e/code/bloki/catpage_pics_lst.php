<?php
if(isset($info['4bloks']['check_img'])) {
$check_img3="";
    foreach($info['4bloks']['check_img'] as $check_img) { $img_temp=explode(";",$check_img);
    if(@$img_temp[0]!="") {  
        $check_img2=imgprocess(MAINURL."/upload/".@$img_temp[0], 0, 50, '1', 'upload/thumb/', 'arr');
        if($check_img2['endwidth']=="") { $check_img2['endwidth']=50; }  
        $check_img3.="<div class='page_imgs' style='width:".$check_img2['endwidth']."px;height:".$check_img2['endheight']."px;'>
            <img style='overflow:hidden;' src=".$check_img2['fotoname']."></div>";
        }}
    if($check_img3!="") { $out['{PAGE_IMGS}']="<div class='page_imgs_lst'>".$check_img3."</div>"; }
    }

    // добработка
if($out['{PAGE_IMGS}']!="") {  $out['{PAGE_BLANK}']="<p style='margin:20px 0 0 0;padding:0;'></p>"; }
?>