<?
function pickpoint($i="",$w="",$p="",$city="",$country="",$nadbavka="0",$rules="",$addit=array()) { // TODO: пока без расчета по городам
Debug::log();
    $newf2=explode(";",$rules);
    foreach($newf2 as $nf=>$nf2) {
        $nf3=explode(",",$nf2);
       $pickpoint_cities[trim($nf3[0])]=trim($nf3[1]);
        }

   $str="";

   if(@$addit['pickpoint_city']!="") { $piccity2=strtr($addit['pickpoint_city'],array("-"=>" "));

     if(isset($pickpoint_cities[mb_strtolower(trim($piccity2))])) { $flagg=1;
     $prc=(($pickpoint_cities[mb_strtolower(trim($piccity2))]+$p)+(($pickpoint_cities[mb_strtolower(trim($piccity2))]+$p)*0.05))-$p;
     $flag=1;  } else { $flag=2; $prc=0; }
       } else { $flag=0; $prc=0; }

    $data=$addit['baskid']."_".$i."_".$w."_".strtr(ceil($p),array("."=>"-"))."_".$city."_".$country;

    if($flag>0) {
        $txt_add='<div id=pickpoint_address'.$addit['baskid'].' class=pickpoint style=font-size:0.8em;></div>
            <input type=hidden class=pickpoint name=pickpoint_id[id_'.$addit['baskid'].'] id=pickpoint_id'.$addit['baskid'].' value='.$addit['pickpointid'].' />
            <input type=hidden name=pickpoint_addr[id_'.$addit['baskid'].'] id=pickpoint_addr'.$addit['baskid'].' /><a href=# class=pickpointchoose id='.$data.'>»зменить пункт доставки</a>';
    } else { $txt_add='<a href=# class=pickpointchoose id='.$data.'>¬ыбрать пункт доставки</a>'; } 

    return array('flag'=>$flag,'txt'=>$txt_no,'summ'=>$prc, 'txt_add'=>$txt_add);

    }

?>