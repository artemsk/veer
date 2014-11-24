<?php
if(trim(BANNERS)!="") {
$banners=explode(",",BANNERS); array_walk($banners,'trim_blank');
if(!is_object(@$ppp)) { $ppp=new pages; }

         $vv2=implode("' OR nnn='",$banners);
         $vv3=$ppp->show_page($vv2); $k2=1;

       $vv4=$vv3; // if(count($banners)<=1) {  $vv4[$vv3['nnn']]=$vv3; } else { $vv4=$vv3; }
       
        foreach($vv4 as $kkk=>$vvv) { if($kkk!="") {
            $vv5=explode("+++",$vv4[$kkk]['txt']);
            if(@$vv5[1]!="") { $pic=imgprocess(@MAINURL."/upload/pages/".$vv4[$kkk]['pic'],@$vv4[1],"0","1","upload/pages/thumb/");
            } else { $pic=MAINURL."/upload/pages/".$vv4[$kkk]['pic']; }

            $out["{BANNERS_".$k2."}"]="<a href=".@$vv5[0]."><img src=".$pic." border=0></a>"; $k2++;
            
            
            }}
}
?>