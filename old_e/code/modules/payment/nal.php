<? function nal($p="", $params=array(), $rules="") {
Debug::log();
    $rules2=explode("[###]",$rules);
    foreach($rules2 as $r1=>$r2) {
        $r3=explode("[#]",$r2);
        if(trim($r3[0])=="") { continue; }
        if($params['deliver_select']==$r3[0]) { // ����� ������������� � ����������� �� ������� ���� ��������, ���� ������� � ��������

            // ���� ���� �������: ���� ��������, �� ������ ����������!
            if($params['deliver_select']=="pickpoint") {
            if(@$params['deliver_piccity']!="") { $piccity=trim(strtr($params['deliver_piccity'],array("-"=>" "))); }
            $r4=explode(",",$r3[1]);
            $r5=array_count_values($r4);
            if(isset($r5[mb_strtolower($piccity)])) { }
            else { return array('flag'=>'0','txt'=>'������ ��������� ��� Pickpoint �������� ������ ��� ������ � ���������� �������','summ'=>'0'); }
              }

            }

        }


        return array('do_not_change'=>"1");

    }
    ?>