<?php
function ruspost($i="",$w="",$p="",$city="",$country="",$nadbavka="0",$rules="",$addit=array()) { Debug::log();
    $country=strtr($country,array("+"=>" ","-"=>" ")); $city=strtr($city,array("+"=>" ","-"=>" "));
    if($i==""||$w<=0||$p<=0) { return array('flag'=>'2','txt'=>'','summ'=>'0'); }
    if(mb_strtolower($country)!="россия"&&mb_strtolower(substr($country,0,10))!="российская"&&mb_strtolower($country)!="russia"&&mb_strtolower($country)!="russian federation") { return array('flag'=>'2','txt'=>'','summ'=>'0'); }
    $w=ceil($w*1.25);
    if($w<=500) { $w=500; }

    $costpost=0; // itogo

    $costpost_1=mysql_kall("SELECT zone FROM ".DB_PREFIX."configuration_ruspost WHERE indexes='".trim($i)."'");

    if(mysql_num_rows($costpost_1)>0) {
    
        $rules2=explode(",",$rules);
        $tarifs=array("1"=>trim($rules2[0]),"2"=>trim($rules2[1]),"3"=>trim($rules2[2]),"4"=>trim($rules2[3]),"5"=>trim($rules2[4])); // тарифы
        $tarifs2=array("1"=>trim($rules2[5]),"2"=>trim($rules2[6]),"3"=>trim($rules2[7]),"4"=>trim($rules2[8]),"5"=>trim($rules2[9]));
        $tarifs2_gabarit=(trim($rules2[10])/100);
        $tarifs2_nacenka=trim($rules2[11]);

        $weight_2=$w/500;
        if($weight_2<1) { $weight_2=0; } else { $weight_2=ceil($weight_2)-1; if($weight_2<=0) { $weight_2=0; }}

        $costpost_2=mysql_fetch_assoc($costpost_1);
        if($costpost_2['zone']==1||$costpost_2['zone']==2||$costpost_2['zone']==3||$costpost_2['zone']==4||$costpost_2['zone']==5) {
        $costpost_3=@$tarifs[$costpost_2['zone']]+($tarifs2[$costpost_2['zone']]*$weight_2)+($p*$tarifs2_nacenka); // для посылок
        //$costpost_3=($tarifs[$costpost_2['zone']]*$w*1.2)+($p*0.03); // для бандеролей

        if($w>10000) { $costpost_3=$costpost_3+((@$tarifs[$costpost_2['zone']]+($tarifs2[$costpost_2['zone']]*$weight_2))*$tarifs2_gabarit); }

        $costpost=$costpost_3;
        
    }} else { $fix_unknown_post=1; }

    if($costpost<=0) { } else {
    $costpost=$costpost+trim($nadbavka); }

    if(@$fix_unknown_post=="1"&&@$i!="") {
    $debug_emspost2=fopen(MAINURL_5."/temp/debug_unknown_post.txt","a+");
    flock($debug_emspost2, LOCK_EX);
    fwrite($debug_emspost2,$i."[###]");
    flock($debug_emspost2, LOCK_UN);
    fclose($debug_emspost2);
    }

    $costpost_4['flag']=1; $costpost_4['txt']=""; $costpost_4['summ']=$costpost;
    return $costpost_4;
    }

?>