<?php
            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_all.php";
            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_index.php";
            
 $basket_l="<a href=\"".MAINURL."/user/basket\">"; $basket_l2="</a>";
 if(@$_SESSION['customers_basket_num']<=0) { @$_SESSION['customers_basket_num']=0; $basket_l=""; $basket_l2=""; }

 // send login message
 $notify_cls="up_notify_blank"; $everything_else="everything_else"; 
 if(isset($_SESSION['send_login_message'])&&@$_SESSION['send_login_message']!="") {
 $notify=$_SESSION['send_login_message']; $_SESSION['send_login_message']=""; $notify_cls="up_notify"; 
                $everything_else="everything_else_notify"; }
             
//////////////////////////////////////// ��� ��� ���� ������ ������                
/*
 if(isset($body_filename)) { $templ_body_container=get_include_contents($body_filename); } // ###������_���� (������ � ����������� �� ����������)
 
 $body_filename=MAINURL_5."/template/".TEMPLATE."/body.php";

 if(@$info['4bloks']['BREAK_OUTPUT_FLAG']=="1") { $body_filename=@$info['4bloks']['BREAK_OUTPUT_FILE_BODY']; } // BREAK_OUTPUT!

 $templ_body=get_include_contents($body_filename); // ###������_����

          if(@$templ_body_container!="") { $templ_body = strtr($templ_body, array("{CONTAINER}"=>@$templ_body_container)); }
          
          
           $templ_body=@$templ_head.$templ_body; // @reviewlate: ������� ��� ��������?
                      
            ///////////////////////////// funcs, constants, pages, bloks ////////////////

            $elements=new elements; 
            $change_arr=$elements->making(@$detected[0],@$detected[1],@$info);

            if(is_array($change_arr)) {  $templ_body=strtr($templ_body, @$change_arr);  } // ����� �����. ��������
            /////////////////////////////////////////////////////////////////////////////

            $manual_change_arr=array(
             "{IMG_PATH}"   => MAINURL."/template/".TEMPLATE."/images",
             "{TEMPL_PATH}" => MAINURL."/template/".TEMPLATE,
             "{NOTIFY}" => @$notify,
             "{NOTIFY_CLS}" => @$notify_cls,
             "{BASKET}" => @$_SESSION['customers_basket_num'],
             "{BASKET_L}" => @$basket_l,
             "{BASKET_L2}" => @$basket_l2,
             "{BASKET_HEAD}" => basket_head(@$detected[0], @$detected[1], @$title_head), 
             "{LOGIN_F}" => @login_header(LOGIN_ENABLE),
             "{SHOP_PATH}" => MAINURL,
             "{SPEED_LOAD}" => timer_stop($_SESSION['timer_global'],"GLOBAL_1",@$detected[0],@$detected[1]),
             "{ENGINE_VERSION}" => ENGINEVER,  
             "{BODY_DIV}" => "body_div",
             "{EXTPTH}" => MAINURL."/code/ext",
             "{SHOP_NNN}" => SHOP_NNN,
             "{KWORDS}"   => SHOP_KWORDS,
             "{DESCRIPTIONS}"   => SHOP_DESCR,
             "{TITLE}"   => SHOP_NAME." - ".@$title_head,
             "{EXTPTH}" => MAINURL."/code/ext",
             "{MAINURL}" => MAINURL,
             "{DETECTED0}" => @$detected[0],
             "{DETECTED1}" => @$detected[1],
             "{DETECTED_SORT}" => @$detected['sort_type'],
             "{DETECTED_SORT_DIR}" => @$detected['sort_direction'],
             "{TRACKING}" => TRACKING_CODE,
             "{FORM_PATH}" => MAINURL_2."/user/done/"
        );
                        
            $templ_body = strtr($templ_body, $manual_change_arr);  // ������ �������
                   
        $_SESSION['num_of_sqls']="";

if(TEMPLATE_MODE_ON!="1") { 
 $templ_body=preg_replace("/\{([A-Z0-9_][^}]+)\}/","",$templ_body); } // TODO: ������� ���������������� �������, ��������� ����-������?
// ������� 2: /\{([A-Z0-9_][^}]+)\}/
// ������� 1: /\{([^}]+)\}/

    * 
 */
                
echo iconv("Windowd-1251", "UTF-8", $templ_body); // ###������_�����

