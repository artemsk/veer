<?php

require_once(MAINURL_5."/code/modules/global.php"); 
cats_tree();

    follow(@$detected, @$_SESSION['customers_id']); // follow visits

$bsk_filename=MAINURL_5."/template/".TEMPLATE."/head.php";
if(@$info['4bloks']['BREAK_OUTPUT_FLAG']=="1") { $bsk_filename=@$info['4bloks']['BREAK_OUTPUT_FILE_HEAD']; } // BREAK_OUTPUT!

if($bsk_filename!="") { $templ_head=get_include_contents($bsk_filename); } // ###������_����

  if(isset($head_filename)) { $templ_head_container=get_include_contents($head_filename); // ###������_����
  if(@$templ_head_container!="") { $templ_head = strtr($templ_head, array("{HEAD_CONTAINER}"=>@$templ_head_container)); }
  } // ������ � ����������� �� ����������


  if(@$title_head=="") { $title_head=""; } 

            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_all.php";
            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_index.php";
            
 if(@$_SESSION['customers_basket_num']<=0) { @$_SESSION['customers_basket_num']=0; }

 if(isset($_SESSION['send_login_message'])&&@$_SESSION['send_login_message']!="") {
 $notify=$_SESSION['send_login_message']; $_SESSION['send_login_message']="";}
             
 if(isset($body_filename)) { $templ_body_container=get_include_contents($body_filename); } // ###������_���� (������ � ����������� �� ����������)
 
 $body_filename=MAINURL_5."/template/".TEMPLATE."/body.php";

 if(@$info['4bloks']['BREAK_OUTPUT_FLAG']=="1") { $body_filename=@$info['4bloks']['BREAK_OUTPUT_FILE_BODY']; } // BREAK_OUTPUT!

 $templ_body=get_include_contents($body_filename); // ###������_����

          if(@$templ_body_container!="") { $templ_body = strtr($templ_body, array("{CONTAINER}"=>@$templ_body_container)); }
          
          
           $templ_body=@$templ_head.$templ_body; // @reviewlate: ������� ��� ��������?
                      
            ///////////////////////////// funcs, constants, pages, bloks ////////////////
            if(is_array($change_arr)) {  $templ_body=strtr($templ_body, @$change_arr);  } // ����� �����. ��������
            /////////////////////////////////////////////////////////////////////////////

            $manual_change_arr=array(
             "{NOTIFY}" => @$notify,
             "{BASKET}" => @$_SESSION['customers_basket_num'],
             "{BASKET_HEAD}" => basket_head(@$detected[0], @$detected[1], @$title_head), 
             "{LOGIN_F}" => @login_header(LOGIN_ENABLE),
             "{TITLE}"   => SHOP_NAME." - ".@$title_head,
             "{TRACKING}" => TRACKING_CODE,
        );

 // cache

