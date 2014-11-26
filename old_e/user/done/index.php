<?php

require_once("../../define_names.php");

  if(isset($_POST['addkli'])) { // reg_done
    $newcustomer=new customers;
    $customer_id=$newcustomer->add_customer($_POST);
    if($customer_id>0) {
    $refer=$newcustomer->login(array("login"=>$_POST['email'],"passwrd"=>$_POST['pssw'],"remember_me"=>"0","referer_url"=>"", "referer_host"=>"", "logdone"=>"�����"));
     header("Location: ".MAINURL.""); // ��������������
     } else { header("Location: ".MAINURL."/user/register");  }
     exit;
    }

  // LOGDONE
  if(isset($_POST['logdone'])) { 
    $newcustomer=new customers;
    $refer=$newcustomer->login($_POST); 
     header("Location: ".$refer."");
     exit;
    } // logdone


  // LOGOUT
  if(isset($_POST['logout'])) {
      $return_customer=new customers;
      $refer=$return_customer->logout($_POST);
     header("Location: ".$refer."");
     exit;
      } // LOGOUT

  // FORGOTTEN PASSWRD
  if(isset($_POST['forgot'])&&@$_POST['email']!="") {
      $return_customer=new customers;
      $refer=$return_customer->remember($_POST);
     header("Location: ".$refer."");
     exit;
      } // forgotten passwrd

   // ADD2LISTS
   if(isset($_POST['add2list'])) {
     $product_action=new products;
     $product_action->add2list($_POST); // ��������� � ������
     header("Location: http://".$_POST['referer_host'].$_POST['referer_url']."");
     exit;
     } // add2lists

   // ADD2CART
   if(isset($_POST['add2cart_x'])||isset($_POST['add2cart'])) {
     $product_action=new products;
     $product_action->add2basket($_POST); // ��������� � ������
     header("Location: http://".$_POST['referer_host'].$_POST['referer_url']."");
     exit;
     } // add2cart

   // UPDATE CART
   if(isset($_POST['update_cart'])||isset($_POST['update_cart_fin'])||isset($_POST['update_cart_error_fix'])||isset($_POST['update_cart_fin_quick'])) {
     $prepare_ord=new order;
     $prepare_ord->save_basket_selects($_POST);

         if(isset($_POST['update_cart_fin'])||isset($_POST['update_cart_fin_quick'])) {
         $errors=$prepare_ord->check_basket_selects($_POST); // �������� ����� ��������������
             if(@$errors['total']=="1") {
             header("Location: http://".$_POST['referer_host'].$_POST['referer_url']."");
             exit; }
         }
     
     $product_action=new products;
     $product_action->update_basket($_POST); // ��������� � ������

        if(!isset($_POST['update_cart_error_fix'])&&(@$_SESSION['basket_log_changed_a']=="1"||@$_SESSION['basket_log_changed_q']=="1")) {} else {
            //// ���������� ���� ������� �� ����������! TODO: ���������!
        if(isset($_POST['update_cart_fin'])||isset($_POST['update_cart_fin_quick'])||isset($_POST['update_cart_error_fix'])) { // �������, �������, ��_����������
         $neword=new order;
         $success_info=$neword->make($_POST);
         $_SESSION['success_info_order']=$success_info;
         header("Location: ".MAINURL."/user/success");
         exit;
         }} //
         
     header("Location: http://".$_POST['referer_host'].$_POST['referer_url']."");
     exit;       
       }

   // REVIEW SEND
   if(isset($_POST['review_send'])) {
        $product_action=new products;
        $product_action->add_review($_POST); // ��������� � ������
        header("Location: http://".$_POST['referer_host'].$_POST['referer_url']."");
        exit;
       }

   // DELETE FROM COMPARE LIST
   if(isset($_POST['outoflist'])) { 
        $product_action=new products;
        $product_action->compare_del($_POST); // ��������� � ������
        header("Location: ".MAINURL."/user/compare");
        exit;
       }

   // COMMENT 2 PAGE SEND
   if(isset($_POST['comment_send'])) {
        $product_action=new pages;
        $product_action->add_comment($_POST); // ��������� � ������
        header("Location: http://".$_POST['referer_host'].$_POST['referer_url']."");
        exit;
       }

   // RATE_PRODUCT
   if(isset($_POST['vote_y_x'])||isset($_POST['vote_n_x'])) {
        $product_action=new products;
        $product_action->add_review_only_rate($_POST); // ��������� � ������
        header("Location: http://".$_POST['referer_host'].$_POST['referer_url']."");
        exit;
       }

   // CALLME
   if(isset($_POST['callme_send'])&&@$_POST['phone_to']!="") {
        $guestbook=new customers;
        $guestbook->guestbook($_POST,$_SERVER['HTTP_REFERER'],'callme'); // ��������� � ������
        header("Location: ".$_SERVER['HTTP_REFERER']."");
        exit;
       }

    // CONTACT
    if(isset($_POST['guestbook_send'])&&@$_POST['msg']!="") {
        $guestbook=new customers;
        $guestbook->guestbook($_POST,$_SERVER['HTTP_REFERER'],'contact'); // ��������� � ������
        header("Location: ".$_SERVER['HTTP_REFERER']."");
        exit;
       }

  // ADMIN LOGDONE
  if(isset($_POST['a_logdone'])) {
    $newadm=new adm;
    $refer=$newadm->login($_POST);
    header("Location: ".$refer."");
     exit;
    } // logdone

  // ADMIN POST MESSAGE
  if(isset($_POST['a_msgsend'])) {
    $newadm=new adm;
    $refer=$newadm->adm_msgs_post($_POST);
    header("Location: ".MAINURL."/code/ext/messages/show");
     exit;
   } // postmessage

   // ADMIN POST MESSAGE
  if(isset($_POST['a_msgsend'])) {
    $newadm=new adm;
    $refer=$newadm->adm_msgs_post($_POST);
    header("Location: ".MAINURL."/code/ext/messages/show");
     exit;
   } // postmessage

  if(isset($_POST['a_editshop_hid'])&&isset($_COOKIE['alog'])) {
        $newadm=new adm;
        $refer=$newadm->adm_editshop_post($_POST);
        header("Location: ".MAINURL."/code/ext/adm/edit/1/".$_POST['shop2edit']);
        exit;
      }

  if(isset($_POST['a_editshop_conf_hid'])&&isset($_COOKIE['alog'])) {
      $newadm=new adm;
      $refer=$newadm->adm_editshop_post_conf($_POST);
      // header("Location: ".MAINURL."/code/ext/adm/edit/4/".$_POST['shop2edit']."/".$_POST['a_editshop_conf_hid']);
      // TODO: ����������� ������ �� �������� ���������� ��� ������������, � ���� ��������:
      header("Location: ".MAINURL."/adm/shops/edit".$_POST['shop2edit']."#conf_".$_POST['a_editshop_conf_hid_key']);
      exit;
      }

  if(isset($_POST['a_editshop_stats_hid'])&&isset($_COOKIE['alog'])) {
      $newadm=new adm;
      $refer=$newadm->adm_editshop_post_stats($_POST);
      header("Location: ".MAINURL."/code/ext/adm/edit/".$refer."/".$_POST['shop2edit']);
      exit;
      }

  if(isset($_POST['_send_tmpl'])&&@$_POST['_usercl']!=""&&@$_POST['_userfunc']!="") {
      $rp_classname="__userfunc_".$_POST['_usercl'];
      $rp_class=new $rp_classname;
      $refer=call_user_func(array($rp_class, $_POST['_userfunc']),$_POST);
      if(@$refer!="") { header($refer); exit; } 
      }   
  
 header("Location: ".MAINURL."");
?>