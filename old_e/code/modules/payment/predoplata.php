<?php
function predoplata($p="", $params=array(), $rules="") {
Debug::log();
    $skidka=0; $txt=""; $flag=1;

    // �.�. ������� �� ���������� ���� ������ � ���������, �� �� ������� (����� ����� �� nal.php)
     $rules2=explode(";",$rules);
     foreach($rules2 as $nf=>$nf2) {
     $nf3=explode(",",$nf2);
     $pickpoint_cities[trim($nf3[0])]=trim($nf3[1]);
     }

    if(@$params['deliver_select']=="pickpoint") { // pickpoint!
     if(@$params['deliver_piccity']!="") { $piccity=trim(strtr($params['deliver_piccity'],array("-"=>" "))); }

        if($params['deliver_price']>0&&@$piccity!="") {
        $p2=$params['deliver_price']-($pickpoint_cities[mb_strtolower(trim($piccity))])-1; // 1 ��� ��� �����������
        $skidka=$p2; $txt="������� �� <u>".$p2." �.</u>, ���� �� ������� �������� � �������� �������� Pickpoint."; $flag=1;
        }}


        return array('skidka'=>$skidka,'txt'=>$txt,'flag'=>$flag);
    }

?>