<?php

 /* ������ �����:
  * 
  * 1) ������ ��� ������ - ����� �� ���� ������ ��������: HEAD_IMGS 
  * 2) ���������� ���������� ��� ����������� ���� � ���������: MAINURL, TEMPLATE
  * 
  * 3) ��������� ������� �������� �������� � ������ ������
  * 
  * 4) ��������� ������ � ���������� out
  */ 
 
    $bigpic=explode("\r\n",HEAD_IMGS);
    $bigpic2=$bigpic[array_rand($bigpic)];
    $bigpic3=explode("|",$bigpic2);
    
    $out['{KLB_BIGPIC_ADV_IMG}']=trim($bigpic3[0]);
    $out['{KLB_BIGPIC_ADV_SLOGAN}']=trim($bigpic3[1]);
    
    /*
    $bigpic3="";
    foreach($bigpic as $k=>$v) { 
        $v2=explode("|",$v);
        $bigpic3.="<li class='klb_4' style=\"background-image: url('{IMG_PATH}/".$v2[0]."');\"></li>";
    }
    
    $out['{KLB_BIGPIC_ADV_IMG}']="<ul class=\"bxslider\">".$bigpic3."</ul>";
     * 
     */
?>