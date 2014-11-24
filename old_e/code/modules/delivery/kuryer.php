<?php
function kuryer($i="",$w="",$p="",$city="",$country="",$nadbavka="0",$rules="",$addit=array()) { Debug::log();
    $country=strtr($country,array("+"=>" ","-"=>" ")); $city=strtr($city,array("+"=>" ","-"=>" "));
    if(mb_strtolower($country)!="россия"&&mb_strtolower(substr($country,0,10))!="российская"&&mb_strtolower($country)!="russia"&&mb_strtolower($country)!="russian federation") { return array('flag'=>'2','txt'=>'','summ'=>'0'); }
    $rules2=explode(",",$rules);
    $prc=$rules2[0]; unset($rules2[0]);
    $rules3=array_flip($rules2);
    if(isset($rules3[mb_strtolower($city)])) { return array('flag'=>'1','txt'=>'','summ'=>$prc); } else {
        return array('flag'=>'0','txt'=>'Курьерская доставка пока доступна только для Москвы','summ'=>'0'); }
    }
    ?>