<?php

require_once(MAINURL_5."/code/modules/global.php"); 

cats_tree();

follow(@$detected, @$_SESSION['customers_id']); // follow visits

$body_filename=MAINURL_5."/template/".TEMPLATE."/body_all.php";
$body_filename=MAINURL_5."/template/".TEMPLATE."/body_index.php";
            
 if(@$_SESSION['customers_basket_num']<=0) { @$_SESSION['customers_basket_num']=0; }

 if(isset($_SESSION['send_login_message'])&&@$_SESSION['send_login_message']!="") {
 $notify=$_SESSION['send_login_message']; $_SESSION['send_login_message']="";}
             
 $body_filename=MAINURL_5."/template/".TEMPLATE."/body.php";

$manual_change_arr=array(
             "{NOTIFY}" => @$notify,
             "{BASKET}" => @$_SESSION['customers_basket_num'],
             "{BASKET_HEAD}" => basket_head(@$detected[0], @$detected[1], @$title_head), 
             "{LOGIN_F}" => @login_header(LOGIN_ENABLE),
             "{TITLE}"   => SHOP_NAME." - ".@$title_head,
             "{TRACKING}" => TRACKING_CODE,
);
