<?php
$user_data=@$detected[1];

    // REGISTER
    if($user_data=="register") { $notuser=1;
        $user_forms=new forms;
        $showform=$user_forms->register_form();
        $info['4bloks']['register_page']=$showform;
        $info['4bloks']['register_page_flag']="1";
    } // // // // //


    // BASKET
    if($user_data=="basket") {  $notuser=1;
        $head_filename=MAINURL_5."/template/".TEMPLATE."/head_add_basket.php";
        $bsk=new products;
        $show_bsk=$bsk->show_basket();
        $info['4bloks']['show_bsk']=$show_bsk;
        $info['4bloks']['show_bsk_flag']="1";
     } // // // // // //


    // COMPARE
    if($user_data=="compare") { $notuser=1; // compare
        $lst=new products;
        $show_lst=$lst->compare();
        $info['4bloks']['compare_prds']=$show_lst;
        $info['4bloks']['compare_prds_flag']="1";
    } // // // // // //
   

    // CONTACT
    if($user_data=="contact") {  $notuser=1; // contact
    $info['4out']['{PAGE_NAZV}']="Контакты";
    if(!isset($newform)) { $newform=new forms; }
    $contacts=$newform->contact_us_form();
    $info['4out']['{CONTACT_FORM}']="<div class=\"contact_form\">".$contacts."</div>";
    } // // // // // 


    //BILLS
    if(substr($user_data,0,6)=="bills/") {  $notuser=1;
    if(!isset($show_order)) { $show_order=new order; }
    $info['4bloks']['BREAK_OUTPUT_FLAG']="1";
    $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/".TEMPLATE."/break_output_billcontent.php";
    $info['4out']['{BILL_CONTENT}']=$show_order->show_bill(substr($user_data,6));
    }


    // ORDER_SUCCESS
    if(substr($user_data,0,7)=="success") {  $notuser=1;
        if(@$_SESSION['success_info_order']!="") {
    $info['4out']['{PAGE_NAZV}']="Готово";
    $info['4out']['{SUCCESS_ORDER}']="<div class=\"success_info\">".@$_SESSION['success_info_order']."</div>"; $_SESSION['success_info_order']="";
      }
    }

    //ORDERS
    if(substr($user_data,0,7)=="orders/") {  $notuser=1;
    if(!isset($show_order)) { $show_order=new order; }
    $so=$show_order->show_order(substr($user_data,7));
    if(@$so['oid']>0) {
    $info['4out']['{SUCCESS_ORDER}']="<div class=\"success_info\">".strtr($so['cache'],array("&quot;"=>"\"","{REG_ORDER_STATUS_NAME}"=>$so['status']))."</div>";
    $info['4out']['{PAGE_NAZV}']="Заказ №".$so['oid']; }
    }


    // CUSTOMERS_PAGE
    if($notuser!="1") {
        $c_page=new customers;
        $olist=$c_page->customers_page();
        if(@$olist!="") {
        $info['4out']['{PAGE_NAZV}']="Заказы";
        $info['4out']['{SUCCESS_ORDER}']="<div class=\"success_info\">".$olist."</div>"; // TODO: переделать страницу клиента
        }}
    