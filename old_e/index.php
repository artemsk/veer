<?php

require_once(MAINURL_5."/code/modules/global.php"); 
cats_tree();
////////////////////////////////////////////
////////////////////////////////////////////

    follow(@$detected, @$_SESSION['customers_id']); // follow visits

    /////////////////////////////////////////////
    // �������� ������ �� ������� ��������     //   ������ product ��������
    /////////////////////////////////////////////

        if(@$detected[0]!=""&&@$detected[1]!="") {
            $navigate=navigation($detected[0]); 
            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_all.php";            
            if(@$navigate!="") { require($navigate); }}
        if(@$detected[0]==""&&@$detected[1]=="") {
            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_index.php";
            }       
            
    /////////////////////////////////
    // ������������ � �������      //
    /////////////////////////////////
         
        require(navigation("head_prepare")); // <head></head>  TODO: �������� �������� �������� � ����
        require(navigation("body_prepare")); // �������� �����

