<?php
    $info['4bloks']['BREAK_OUTPUT_FLAG']="1";
    $detected_upd=explode("?_=",@$detected[1]);

    // CALLME AJAX
    if($detected_upd[0]=="callme/callme.php") {
    $info['4out']['{CALLME_FULL}']=callme(@$_COOKIE['firstname'],@$_COOKIE['lastname'],@$_SESSION['telephone'],'full');   
    $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/".TEMPLATE."/break_output_callme_form.php"; }


    // HTMLLOAD CATEGORIES htmlload/289__287.html
    if(substr($detected_upd[0],0,9)=="htmlload/") {
        $ineedtree=explode("_",substr($detected_upd[0],9,-5));
       
        $cats=new categories;
        $curr['nnn']=$ineedtree[1];
        $info['4out']['{CATEGORIES}']=$cats->show_tree(SHOP_NNN,$curr,$ineedtree);
        $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/".TEMPLATE."/break_output_categories_tree.php";

        }

   if(substr($detected_upd[0],0,20)=="deliver/deliver_addr") {

       $parseurl=explode("_",substr($detected_upd[0],20));
       if(!isset($ord)) { $ord=new order; }
       if(!isset($newform)) { $newform=new forms; }

       // pickpoint
       $addit=array("baskid"=>$parseurl[0],"pickpointid"=>iconv("UTF-8","Windows-1251",urldecode(@$parseurl[6])),
      "pickpoint_city"=>iconv("UTF-8","Windows-1251",urldecode(@$parseurl[7])));
       
      $delivery_arr=$ord->delivery_type(@$parseurl[1],@$parseurl[2],@$parseurl[3],
              iconv("UTF-8","Windows-1251",urldecode(@$parseurl[4])),iconv("UTF-8","Windows-1251",urldecode(@$parseurl[5])),$addit); // определяем доставку
      $info['4out']['{DELIVERY_REFORM}']=$newform->basket_form_delivery($delivery_arr, @$parseurl[0], @$parseurl[3]); // создаем форму выбора доставки
      $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/".TEMPLATE."/break_output_delivery_types.php";
       }

  if(substr($detected_upd[0],0,16)=="payment/delivert") {
      $parseurl=explode("_",substr($detected_upd[0],16));
       if(!isset($ord)) { $ord=new order; }
       if(!isset($newform)) { $newform=new forms; }
 
      $payment_arr=$ord->payment_type(@$parseurl[4], array('deliver_select'=>@$parseurl[2], 'deliver_price'=>@$parseurl[3], 
          'deliver_piccity'=>iconv("UTF-8","Windows-1251",urldecode(@$parseurl[5]))));
      //// собираем виды оплаты + отдельно для пикпоинта
      $payment_form=$newform->basket_form_payment($payment_arr, $parseurl[0]); // создаем форму оплата

      $info['4out']['{PAYMENT_REFORM}']=$payment_form; // создаем форму выбора доставки
      $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/".TEMPLATE."/break_output_payment_types.php";

      }

  if(substr($detected_upd[0],0,13)=="messages/show") {
       $newadm=new adm;
       $msgs=$newadm->adm_msgs_show(@$_SESSION['adm_username']);
       $info['4out']['{MESSAGES}']=$newadm->adm_msgs_show_format($msgs);
       $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/admin/break_output_messages.php";
    }

  if(substr($detected_upd[0],0,13)=="rotatinghead/") {
      $previmg=substr($detected_upd[0],13);
      $bigpic_default=explode("\r\n",HEAD_IMGS);      
      if($previmg!="") {
          $bigpic=explode($previmg,HEAD_IMGS);
          $bigpic2=explode("\r\n", @$bigpic[1]);
          unset($bigpic2[0]);
          if(count($bigpic2)<=0) { $nextimg=$bigpic_default[0]; } else {
              $nextimg=$bigpic2[1];
          }
      } else {
         $nextimg=$bigpic_default[array_rand($bigpic_default)]; 
      }   
      
      $bigpic3=explode("|",$nextimg);    
      
    $info['4out']['{KLB_BIGPIC_ADV_IMG}']=trim($bigpic3[0]);
    $info['4out']['{KLB_BIGPIC_ADV_SLOGAN}']=trim($bigpic3[1]);
    $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/".TEMPLATE."/break_output_rotating_head.php";
      
  }
  
  
  /*  
  if(substr($detected_upd[0],0,11)=="adm/edit/1/") {
      $newadm=new adm; $newform=new forms;
      $shop2edit=substr($detected_upd[0],11);
      $s=$newadm->collect_shops();
      $edit_new=$newform->adm_form_shopedit_catshop($shop2edit,$s,$newadm->adm_shops_statuses(),"nosubmit");
      $info['4out']['{FORMS}']=$edit_new;
      $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/admin/break_output_adm_forms.php";
      }

  if(substr($detected_upd[0],0,11)=="adm/edit/2/"||substr($detected_upd[0],0,11)=="adm/edit/3/") {
      if(substr($detected_upd[0],0,11)=="adm/edit/2/") { $what="o"; } else { $what="b"; } 
      $newadm=new adm; $newform=new forms;
      $shop2edit=substr($detected_upd[0],11);
      $s=$newadm->collect_shops();
      $s2=$newadm->collect_shop_configuration($shop2edit);
      $s3=$newadm->collect_shop_o_b_statuses($shop2edit,$s2['ORDER_STATUS_DB']);
      $edit_new=strtr($newform->adm_form_shopedit_statuses_ob($shop2edit, $s3, $what,
              array($s2['ORDER_STATUS_DB'],$s['nazv'][trim($s2['ORDER_STATUS_DB']['conf_val'])]), "nosubmit"),array("{MAINURL_ADM}"=>MAINURL."/adm"));
      $info['4out']['{FORMS}']=$edit_new;
      $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/admin/break_output_adm_forms.php";
      }
      
   if(substr($detected_upd[0],0,11)=="adm/edit/4/") { 
      $newadm=new adm; $newform=new forms;
      $detects=explode("/",substr($detected_upd[0],11)); 
      $shop2edit=$detects[0]; $confnnn=$detects[1]; 
      $s2=$newadm->collect_shop_configuration($shop2edit);
      $edit_new=$newform->adm_form_shopedit_configuration($shop2edit,$s2,'nosubmit',$confnnn);
      $info['4out']['{FORMS}']=$edit_new;
      $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/admin/break_output_adm_forms.php";
      }
   * 
   */  
