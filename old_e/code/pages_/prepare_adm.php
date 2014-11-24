<?
$info['4bloks']['BREAK_OUTPUT_FLAG']="1";
$info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/admin/body.php";

$info['4bloks']['BREAK_OUTPUT_FILE_HEAD']=MAINURL_5."/template/".TEMPLATE."/head.php";
$head_filename=MAINURL_5."/template/admin/head_adm_add.php";; // additional head params

$info['4out']['{ADM_IMG_PATH}']=MAINURL."/template/admin/images";
$info['4out']['{MAINURL_ADM}']=MAINURL."/adm";

$newadm=new adm;

if($detected[1]=="logout") {
    $newadm->logout();
    }

$newdetected=$newadm->login_check($detected[1]); // logstat, newdetect; проверяем залогинен или нет. detected[1] - сохраняется!

// LOGIN FORM
if(@$newdetected['newdetect']=="login") {
$user_forms=new forms;
$info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/admin/body_login.php";
$info['4out']['{LOGIN_FORM}']=$user_forms->adm_form_login();
}
// LOGIN_FORM

if(@$newdetected['logstat']=="1") { // все делаем только если залогинены! LOGSTAT


// MESSAGES
// SESSION: adm_username, adm_email, adm_dostup, adm_shopwatch, lastactive
$msgs=$newadm->adm_msgs_show($_SESSION['adm_username']);
$info['4out']['{MESSAGES}']=$newadm->adm_msgs_show_format($msgs);
// MESSAGES

// MESSAGE_INPUT_FORM
$list_users=$newadm->list_users();
$user_forms=new forms;
$info['4out']['{MESSAGE_FORM}']=$user_forms->adm_form_message($list_users);
// MESSAGE_INPUT_FORM

$info['4out']['{ADMIN_USERNAME}']=$_SESSION['adm_username'];

// SHOPS
if(@$detected[1]=="shops") {
    $s=$newadm->collect_shops();
    $s_tree=$newadm->show_shops_tree($s);
    $s_tree=strtr($s_tree, array("{EDIT_ICON}"=>"<img src=".MAINURL."/template/admin/images/edit.png border=0 align=absbottom>",
                                 "{ADM_SHOP_LINK}"=>MAINURL."/adm/shops"));
    $info['4out']['{MAIN_BLOCK}']=$s_tree;
    }

if(substr(@$detected[1],0,5)=="shops") {
    $info['4out']['{CONTEXT_MENU}']="<br/><br/><br/><br/>".strtr($newadm->context_menu(@$detected[1]),array("{MAINURL_ADM}"=>MAINURL."/adm"));
    }
// SHOPS

// EDIT SHOPS
if(substr(@$detected[1],0,10)=="shops/edit") {
    $shop2edit=substr(@$detected[1],10);
    $s=$newadm->collect_shops();
    $s2=$newadm->collect_shop_configuration($shop2edit);
    $s3=$newadm->collect_shop_o_b_statuses($shop2edit,$s2['ORDER_STATUS_DB']);

    $edit1="<div class='adm_shop_edit_zag'>Общие параметры</div><table class='adm_shop_edit'><tr><td>".
    $user_forms->adm_form_shopedit_catshop($shop2edit,$s,$newadm->adm_shops_statuses())."</td></tr></table>";

    $edit2_form=$user_forms->adm_form_shopedit_configuration($shop2edit,$s2);
    if(trim($edit2_form)!="") {
    $info['4out']['{CONTEXT_MENU}'].="".strtr($newadm->context_menu(substr(@$detected[1],0,10)),array("{MAINURL_ADM}"=>MAINURL."/adm"));
    $edit2="<br/><div class='adm_shop_edit_zag'>Расширенная настройка</div><table class='adm_shop_edit'><tr><td>".
    $edit2_form."</td></tr></table>"; }

    $edit3="<br/><div class='adm_shop_edit_zag'>Статусы заказов</div><table id='adm_status_block' class='adm_shop_edit'><tr><td>".
    strtr($user_forms->adm_form_shopedit_statuses_ob($shop2edit, $s3, "o", array($s2['ORDER_STATUS_DB'],$s['nazv'][trim($s2['ORDER_STATUS_DB']['conf_val'])])),array("{MAINURL_ADM}"=>MAINURL."/adm")).
            "</td></tr></table>";
    $edit4="<br/><div class='adm_shop_edit_zag'>Статусы счетов</div><table id='adm_bills_block' class='adm_shop_edit'><tr><td>".
    strtr($user_forms->adm_form_shopedit_statuses_ob($shop2edit, $s3, "b", array($s2['ORDER_STATUS_DB'],$s['nazv'][trim($s2['ORDER_STATUS_DB']['conf_val'])])),array("{MAINURL_ADM}"=>MAINURL."/adm")).
            "</td></tr></table>";

    $info['4out']['{MAIN_BLOCK}']=$edit1.@$edit3.@$edit4.@$edit2;
    
    
    }

} // logstat