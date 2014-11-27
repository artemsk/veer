<?php
/* order->
7) счета
8) добавление заказа в бд,
в текстовой файл, рассылка */

class order {

    // определяем и отображаем виды доставки
    function delivery_type($i="",$w="",$p="",$city="",$country="",$addit=array()) { // index, weight, payment, city
         Debug::log();
         $city=strtr($city,array("."=>"","'"=>"",","=>"","_"=>""," "=>"+",":"=>"",";"=>"","/"=>"_","http://"=>"http_","?"=>"_","="=>"_"));
         $country=strtr($country,array("."=>"","'"=>"",","=>"","_"=>""," "=>"+",":"=>"",";"=>"","/"=>"_","http://"=>"http_","?"=>"_","="=>"_"));

         $types=explode(",",ORDER_DELIVERY); $nga=9999999;
         foreach($types as $k=>$v) {
             $v2=file(MAINURL_5."/code/modules/delivery/".$v.".txt");
             if($v2[0]=="") { continue; }
             foreach($v2 as $kk=>$vv) { $vv2=explode(":",$vv); unset($vv2[0]); $vv2[1]=implode(":",$vv2);
             if($kk=="1") { $v3[$v][$kk]=trim(nl2br($vv2[1])); } else {
             $v3[$v][$kk]=trim(strip_tags(nl2br($vv2[1]))); }}

                  if($p<$v3[$v][2]) {
                      $v3[$v]['summ']=0; $v3[$v]['txt']="<i>сумма заказа должна быть ".price_format($v3[$v][2])." и выше.</i>"; $v3[$v]['flag']="0";
                      if($v3[$v][2]<@$nga) { $nga=$v3[$v][2]; $nga_txt=$v3[$v]['txt']; } $nga_kaunt=@$nga_kaunt+1;
                      continue; } // несоотв. минимальным значениям по стоимости

                  if($v3[$v][8]=="1") { $v3[$v]['summ']=0; $v3[$v]['txt']=$v3[$v][7]; $v3[$v]['flag']="2"; continue; } // доставку не рассчитывать

                  if($p>=$v3[$v][4]) { $v3[$v]['summ']=0; $v3[$v]['txt']="<strong>бесплатно</strong>"; $v3[$v]['flag']="1"; continue; } // бесплатная если выше определенной суммы

                  if($v3[$v][3]!="нет") {
                      if($v3[$v][3]<=0) { $v3[$v]['txt']="<strong>бесплатно</strong>"; } else { $v3[$v]['txt']="<strong>".price_format($v3[$v][3])."</strong>"; }
                      $v3[$v]['summ']=$v3[$v][3]; $v3[$v]['flag']="1"; continue; } // бесплатная или с фиксированной ценой

                  // 5,6 рассчитываем доставку
                  $v4=0; if(trim($v3[$v][6])=="") {} else {
                  if(file_exists(MAINURL_5."/code/modules/delivery/".$v.".php")) { 
                  require_once(MAINURL_5."/code/modules/delivery/".$v.".php");
                  if(function_exists($v)) { $v4=call_user_func_array($v, array($i,$w,$p,$city,$country,$v3[$v][5], $v3[$v][6],$addit)); }
                  if(@$v4['txt_add']!="") { $v3[$v]['txt_add']=$v4['txt_add']; } else { $v3[$v]['txt_add']=""; }
                  if($v4['flag']<=0) { $v3[$v]['summ']=0; $v3[$v]['txt']=$v4['txt']; $v3[$v]['flag']=0; continue; } else {
                  $v4=$v4['summ']; }
                  }}

                  if(@$v4<=0) { $v3[$v]['summ']=0; $v3[$v]['txt']=$v3[$v][7]; $v3[$v]['flag']="2"; // доставка не рассчиталась
                  } else { $v3[$v]['summ']=$v4; $v3[$v]['txt']="<strong>".price_format($v4)."</strong>"; $v3[$v]['flag']="1"; } // все норм

                  // flag: 0 - не допускаем к оформлению, 1 - допускаем с рассчитанной доставкой, 2 - допускаем пока без доставки
             }

             if(@$nga_kaunt==count($types)) { return $nga_txt; }

          return @$v3;
     }

    // адрес доставки
    function delivery_address() { Debug::log();
     $flag_no_address=1;
         if(@$_SESSION['customers_id']>0) { // собираем
                $addr_book=mysql_kall("SELECT ".DB_PREFIX."customers_address_book.* FROM ".DB_PREFIX."customers_address_book, ".DB_PREFIX."customers
                    WHERE ".DB_PREFIX."customers_address_book.customers_id='".$_SESSION['customers_id']."'
                        AND ".DB_PREFIX."customers_address_book.customers_id=".DB_PREFIX."customers.customers_id
                            AND ".DB_PREFIX."customers.lastlogon='".$_COOKIE['lastlogon']."' AND city!='' AND street_address_1!=''");
                if(mysql_num_rows($addr_book)>0) {
                $ab=mysql_fetch_assoc($addr_book); $flag_no_address=0;
                do { $address[$ab['nnn']]="<span class=\"address_book_1\">";
                    if($ab['country']!="Россия") { $address[$ab['nnn']].=$ab['country'].", "; }
                    if($ab['region']!=$ab['city']) { $address[$ab['nnn']].=$ab['region'].", "; }
                    $address[$ab['nnn']].=$ab['city']." ".$ab['postcode']."</span><p></p><span class=\"address_book_2\">";
                    if($ab['metro']!="") { $address[$ab['nnn']].=$ab['metro'].", "; }
                    $address[$ab['nnn']].=$ab['street_address_1'];
                    if($ab['street_address_2']!="") { $address[$ab['nnn']].=", ".$ab['street_address_2']; }
                    $address[$ab['nnn']].="</span>";
                    if($ab['fio']!=""||$ab['company']!="") { $address[$ab['nnn']].="<p></p><span class=\"address_book_3\">(".$ab['fio']." ".$ab['company'].")</span>"; }
                    if($ab['default_shipping']=="1") { $address['default_shipping']=$ab['nnn']; }
                    if($ab['default_billing']=="1") { $address['default_billing']=$ab['nnn']; }
                    $address['full'][$ab['nnn']]=$address[$ab['nnn']]; unset($address[$ab['nnn']]);
                    $address['postcode'][$ab['nnn']]=$ab['postcode'];
                    $address['city'][$ab['nnn']]=$ab['city'];
                    $address['country'][$ab['nnn']]=$ab['country'];
                } while ($ab=mysql_fetch_assoc($addr_book)); }
             } //
           if($flag_no_address=="1") { return false; } else {
               return $address;
               }
         }

    // определяем формы оплаты
    function payment_type($p="",$params=array()) { Debug::log();
         $params['city']=strtr(@$params['city'],array("."=>"","'"=>"",","=>"","_"=>""," "=>"+",":"=>"",";"=>"","/"=>"_","http://"=>"http_","?"=>"_","="=>"_"));
         $params['country']=strtr(@$params['country'],array("."=>"","'"=>"",","=>"","_"=>""," "=>"+",":"=>"",";"=>"","/"=>"_","http://"=>"http_","?"=>"_","="=>"_"));

         $types=explode(",",ORDER_PAYMENT); $nga=9999999;
         foreach($types as $k=>$v) {
             $v2=file(MAINURL_5."/code/modules/payment/".trim($v).".txt");
             if($v2[0]=="") { continue; }

             foreach($v2 as $kk=>$vv) { 
                 $vv2=explode(":",$vv); unset($vv2[0]); $vv2[1]=implode(":",$vv2); $v3[$v][$kk]=trim(nl2br(strtr($vv2[1],array("\r\n"=>"")))); }
                 
                  $v3[$v]['skidka']=0; $v3[$v]['txt']=""; $v3[$v]['flag']=1;

                  // мин сумма
                  if($p<$v3[$v][2]) {  $v3[$v]['skidka']=0;
                      $v3[$v]['txt']="сумма заказа должна быть ".price_format($v3[$v][2])." и выше."; $v3[$v]['flag']="0";
                      if($v3[$v][2]<@$nga) { $nga=$v3[$v][2]; $nga_txt=$v3[$v]['txt']; } $nga_kaunt=@$nga_kaunt+1;
                      continue; } // несоотв. минимальным значениям по стоимости

                  if($v3[$v][4]!="нет"&&$p>=$v3[$v][4]&&$v3[$v][3]>0&&$v3[$v][3]!="") {
                      $v3[$v]['skidka']=ceil($p*$v3[$v][3]/100);
                      $v3[$v]['txt']="скидка при выборе этой формы оплаты <i>".$v3[$v][3]."%</i> или <strong>".
                              price_format($v3[$v]['skidka'])."</strong>";
                      $v3[$v]['flag']="1";
                      } // скидка если выше определенной суммы

                  // 5 - спец. условия после подтверждения заказа, запоминаем ,6 - загружаем условия сейчас
                  if($v3[$v][5]!="нет") { $v3[$v]['after_flag']=1; }

                  if($v3[$v][6]!="нет") { 
                  if(file_exists(MAINURL_5."/code/modules/payment/".$v.".php")) {    
                  require_once(MAINURL_5."/code/modules/payment/".$v.".php");
                  if(function_exists($v)) { $v4=call_user_func_array($v, array($p,@$params, $v3[$v][6])); } 
                  if(@$v4['do_not_change']=="1") {} else {
                  $v3[$v]['skidka']=$v4['skidka']; $v3[$v]['txt']=$v4['txt']; $v3[$v]['flag']=$v4['flag']; }
                  }}
                  
                  // 7,8,9 - внешний сервис, текст в бд, подтверждение от менеджера
             }

             return $v3;
          }

    function save_basket_selects($post) { Debug::log();
          if(isset($post['update_cart_fin'])) {
          setcookie("deliver_bask_tmp",@serialize(@$post['deliver_bask']),NULL,"/",MAINURL_4);
          setcookie("payment_bask_tmp",@serialize(@$post['payment_bask']),NULL,"/",MAINURL_4);
          }
          setcookie("baskid_tmp",stripslashes(@$post['grpbybasket']),NULL,"/",MAINURL_4);
          setcookie("deliver_addr_book_tmp",@serialize(@$post['deliver_addr_book']),NULL,"/",MAINURL_4);
          setcookie("bask_comments_tmp",serialize($post['bask_comments']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_country_tmp",serialize($post['deliver_new_addr_country']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_region_tmp",serialize($post['deliver_new_addr_region']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_city_tmp",serialize($post['deliver_new_addr_city']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_metro_tmp",serialize($post['deliver_new_addr_metro']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_postcode_tmp",serialize($post['deliver_new_addr_postcode']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_street_address_1_tmp",serialize($post['deliver_new_addr_street_address_1']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_street_address_2_tmp",serialize($post['deliver_new_addr_street_address_2']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_fio_tmp",serialize($post['deliver_new_addr_fio']),NULL,"/",MAINURL_4);
          setcookie("deliver_new_addr_company_tmp",serialize($post['deliver_new_addr_company']),NULL,"/",MAINURL_4);
          if(!isset($_SESSION['customers_id'])) {
          setcookie("order_quick_fio_tmp",@$post['order_quick_fio'], NULL,"/",MAINURL_4);
          setcookie("order_quick_email_tmp",@$post['order_quick_email'], NULL,"/",MAINURL_4);
          setcookie("order_quick_phone_tmp",@$post['order_quick_phone'], NULL,"/",MAINURL_4); }
          setcookie("bask_upd","1",NULL,"/",MAINURL_4);
          }

    function clear_basket_cookies() { Debug::log();
          setcookie("deliver_bask_tmp","",NULL,"/",MAINURL_4);
          setcookie("payment_bask_tmp","",NULL,"/",MAINURL_4);
          setcookie("baskid_tmp","",NULL,"/",MAINURL_4);
        }

    function read_basket_cookies() { Debug::log();
          $c['deliver_bask']=unserialize(stripslashes(@$_COOKIE['deliver_bask_tmp'])); @setcookie('deliver_bask_tmp','',NULL,"/",MAINURL_4);
          $c['deliver_addr_book']=unserialize(stripslashes(@$_COOKIE['deliver_addr_book_tmp'])); @setcookie('deliver_addr_book_tmp','',NULL,"/",MAINURL_4);
          $c['payment_bask']=unserialize(stripslashes(@$_COOKIE['payment_bask_tmp'])); @setcookie('payment_bask_tmp','',NULL,"/",MAINURL_4);
          $c['bask_comments']=unserialize(stripslashes(@$_COOKIE['bask_comments_tmp']));
          $c['deliver_new_addr_country']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_country_tmp']));
          $c['deliver_new_addr_region']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_region_tmp']));
          $c['deliver_new_addr_city']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_city_tmp']));
          $c['deliver_new_addr_metro']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_metro_tmp']));
          $c['deliver_new_addr_postcode']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_postcode_tmp']));
          $c['deliver_new_addr_street_address_1']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_street_address_1_tmp']));
          $c['deliver_new_addr_street_address_2']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_street_address_2_tmp']));
          $c['deliver_new_addr_fio']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_fio_tmp']));
          $c['deliver_new_addr_company']=unserialize(stripslashes(@$_COOKIE['deliver_new_addr_company_tmp']));
          $c['order_quick_fio']=@$_COOKIE['order_quick_fio_tmp'];
          $c['order_quick_email']=@$_COOKIE['order_quick_email_tmp'];
          $c['order_quick_phone']=@$_COOKIE['order_quick_phone_tmp'];
          $c['flag_errors']=@unserialize(stripslashes(@$_COOKIE['flag_errors'])); @setcookie('flag_errors','',NULL,"/",MAINURL_4);
          $c['baskid_last']=@unserialize(stripslashes(@$_COOKIE['baskid_tmp']));
          if(count(@$c['baskid_last'])>0&&is_array($c['baskid_last'])) { foreach($c['baskid_last'] as $c1=>$c2) { $c['baskid_last_swap'][$c['baskid_last'][$c1]['baskid']]=$c1; } }
          unset($c['baskid_last']);
          if(isset($_COOKIE['bask_upd'])&&@$_COOKIE['bask_upd']=="1") { $c['bask_upd']=1; @setcookie('bask_upd','',NULL,"/",MAINURL_4); }
          return $c;
          }

    function check_basket_selects($post) { Debug::log();
      $gbb=unserialize(stripslashes($post['grpbybasket']));
      foreach($gbb as $k=>$v) { $flag[$k]['all']=0;

      if(!isset($post['update_cart_fin_quick'])) { // для быстрого заказа проверяем только контактные данные
              $deliver_arr=__unserialize(stripslashes(trim($post['deliver_arr']['id_'.$k])));

              if(!isset($post['deliver_addr_book']['id_'.$k])) { $flag[$k]['chosen']=1; }
                  if(@$post['deliver_new_addr_city'][$k]=="") { $flag[$k]['new']=1; }
                  if(@$post['deliver_new_addr_street_address_1'][$k]=="") { $flag[$k]['new']=1; }
                  if(@$post['deliver_new_addr_postcode'][$k]=="") { $flag[$k]['new']=1; }

                  if(!isset($post['deliver_bask']['id_'.$k])) { $flag[$k]['deliver_type']=1; $txt["не выбран способ доставки"]="2"; } else { // способ
                      $pp=explode("_",$post['deliver_bask']['id_'.$k]);
                      if(trim($deliver_arr[trim($pp[0])][9])=="нет") { $flag[$k]['deliver']=0; $flag_deliver_skip[$k]=1; }} 
                    // delivery_type №9 находится здесь!)

                 if(@$flag[$k]['chosen']=="1"&&@$flag[$k]['new']=="1"&&@$flag_deliver_skip[$k]!="1") { $flag[$k]['deliver']=1; $txt["нет адреса доставки"]="1"; } // по адресу доставки проблемы
                 // пропускаем, если для способа доставки не нужен адрес

                  if(!isset($post['payment_bask']['id_'.$k])) { $flag[$k]['payment_type']=1; $txt["не выбрана форма оплаты"]="3"; } // оплата

        }


                  if(@$_SESSION['customers_id']<=0&&(trim($post['order_quick_fio'])==""||trim($post['order_quick_email'])==""||trim($post['order_quick_phone'])=="")) {
                      $flag[$k]['c_info']=1; $txt["нет контактных данных"]="4"; }


      if(@$flag[$k]['deliver']=="1"||@$flag[$k]['deliver_type']=="1"||@$flag[$k]['payment_type']=="1"||$flag[$k]['c_info']=="1") { $flag[$k]['all']=1; $flag['total']=1;
      $_SESSION['send_login_message']="Не хватает данных для подтверждения заказа: ".implode(", ",array_flip($txt));
      }

      }
    setcookie('flag_errors',serialize($flag),NULL,"/",MAINURL_4);
    return $flag;
    }

    // определяем статус заказа
    function status_finder($flag="",$db="status") { // flag->first,quick,error,finish,(или массив all) // db->status, bills
        Debug::log();
        if($db=="status") { $db2="orders_status"; } else { $db2="orders_status_bills"; }
        if($flag!=""&&$flag!="all") { $f="AND flag_".$flag."='1'"; } else { $f=""; }
        $sql="SELECT * FROM ".DB_PREFIX.$db2." WHERE catshop='".ORDER_STATUS_DB."' ".$f." ORDER BY sort ASC";
        $findst=mysql_kall($sql); $findst2=mysql_fetch_assoc($findst);
        do {
            $out['id']=$findst2['status_id']; $out['nazv']=$findst2['status_name']; $out['color']=@$findst2['status_color'];
         if($flag!=""&&$flag!="all") {} else { $out_arr[$findst2['status_id']]=$out; }
        } while ($findst2=mysql_fetch_assoc($findst));
         if($flag!=""&&$flag!="all") { return $out; } else { return $out_arr; }
        }

    // cбор адресной книги
    function addrbook($id, $flag="addr") { // addr, default_billing, default_shipping, yur
        Debug::log();
        if($flag=="addr") { $str="WHERE nnn='".$id."'"; }
        if($flag=="bill") { $str="WHERE customers_id='".$id."' AND default_billing='1'"; }
        if($flag=="ship") { $str="WHERE customers_id='".$id."' AND default_shipping='1'"; }

        if($flag=="yur") {  $i=mysql_kall("SELECT * FROM ".DB_PREFIX."customers_yur_book WHERE customers_id='".$id."'"); } else {
        $i=mysql_kall("SELECT * FROM ".DB_PREFIX."customers_address_book ".$str.""); }

        if(mysql_num_rows($i)>0) {
              $i2=mysql_fetch_assoc($i);
              return $i2;
            }
        }

    // собираем pv
    function pvcollect($arr, $arrq) { // arr - массив с товарами, arrq - кол-во
        Debug::log();
       $arr2=implode("' OR nnn='",$arr);
       $pv=mysql_kall("SELECT pv, nnn FROM ".DB_PREFIX."products WHERE nnn='".$arr2."'");
       $pv2=mysql_fetch_assoc($pv); $pvtot=0;
       do {
           $pvtot=$pvtot+($pv2['pv']*$arrq[$pv2['nnn']]);
           } while($pv2=mysql_fetch_assoc($pv));
       return $pvtot;
       }

    // оформляем заказ
    function make($post) { // оформляем заказ
Debug::log();
        $bsk=new products;
        $show_bsk=$bsk->show_basket();

            // разберемся сразу со свойствами товаров
             foreach($post as $k=>$v) { if(substr($k,0,5)=="attr_") { $k2=explode("_",$k);
             foreach($v as $kk=>$vv) { foreach($vv as $kkk=>$vvv) {
             // $k2[1]." / ".$k2[2]." / ".$k2[3]." / prd_id-".$kk." / basket_id-".$kkk." => ".$vvv."<br>"; // debug
             if(substr($vvv,-1)=="_") { $v2=explode("___",$vvv); $vvv=trim($v2[0]); }
             $k22=$k2; unset($k22[0], $k22[1], $k22[2]); $k23=implode(" ",$k22);
             $rearr[$kk][$kkk][$k2[1]."/".$k2[2]][trim($k23)]=trim($vvv);
             }}}}

            // FIO, PHONE, EMAIL
                    $fio = @$_COOKIE['firstname'] . " " . @$_COOKIE['lastname'];

                    $phone = @$_SESSION['telephone'];

                    $email = @$_COOKIE['email'];

                    $cid = @$_SESSION['customers_id'];

                    if (!isset($_SESSION['customers_id'])) {

                        if ($post['order_quick_fio'] != "") {
                            $fio = $post['order_quick_fio'];
                        }

                        if ($post['order_quick_phone'] != "") {
                            $phone = $post['order_quick_phone'];
                        }

                        if ($post['order_quick_email'] != "") {
                            $email = $post['order_quick_email'];
                        }

                        $cid = @mysql_result(mysql_kall("SELECT customers_id FROM " . DB_PREFIX . "customers 
                            WHERE email='" . $email . "'"), '0', 'customers_id');
                    }

                    $o_status = $this->status_finder("first");
                    $reg_order = 1; // обычный заказ

                    if (!isset($_SESSION['customers_id'])) {
                        $o_status = $this->status_finder("quick");
                        $reg_order = 2;
                    }  // без регистрации

                    if (isset($post['update_cart_error_fix'])) {
                        $o_status = $this->status_finder("error");
                        $reg_order = 3;
                    }  // недоформленный

                    if (isset($_SESSION['customers_type'])) {
                        $c_type = $_SESSION['customers_type'];
                    } else {
                        $c_type = "unknown";
                    }

                    if (isset($_SESSION['yur_status'])) {
                        $yur_status = $_SESSION['yur_status'];
                    } else {
                        $yur_status = "0";
                    }

                    $b_status = $this->status_finder("first", "bills");

                    $madeorders = 0;
                    $orders_str = "";
                    $bills_str = "";
                    $bills_str_flag = 0;
                    $ymaps_str = "";
                    $bills_message_afterwards="";

            /* 
             * несколько заказов 
             */
                    
            foreach(unserialize(stripslashes($post['grpbybasket'])) as $k=>$v) { 
               
               $madeorders++;
               $reg_order_sql=$reg_order;
               if(@$_SESSION['customers_coupon_active']=="1") { $reg_order_sql=4; } 
                  // с купоном, TODO: остальные статусы 5по телефону,6от админа,7переданный

               if($v['catshopid']<=0) { $v['catshopid']=SHOP_NNN; }
               if($v['catshopid']==SHOP_NNN) { $cluster=ORDER_CLUSTER; } else {
                   $cluster=@mysql_result(@mysql_kall("SELECT conf_val FROM ".DB_PREFIX."configuration 
                       WHERE shop_cat='".$v['catshopid']."' AND conf_key='ORDER_CLUSTER'"),0,'conf_val');
                   if($cluster=="") { $cluster=0; }
                   }

               $catsup=cats_up($v['catshopid']);

               // delivery address
               if(isset($post['deliver_addr_book']['id_'.$k])) {
                   
                   if(isset($addr_arr[$post['deliver_addr_book']['id_'.$k]])) {} else {
                       $addr_arr[$post['deliver_addr_book']['id_'.$k]]=$this->addrbook($post['deliver_addr_book']['id_'.$k]); }

                    $delivery_name=$addr_arr[$post['deliver_addr_book']['id_'.$k]]['fio']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['company'];
                    
                    $delivery_address=$addr_arr[$post['deliver_addr_book']['id_'.$k]]['city']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['postcode']."<br/>".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['street_address_1']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['street_address_2']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['metro']."<br/>(".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['country']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['region'].")";
                    
                    $city=$addr_arr[$post['deliver_addr_book']['id_'.$k]]['city'];
                    
                    $postcode=$addr_arr[$post['deliver_addr_book']['id_'.$k]]['postcode'];
                    
                    $metro=$addr_arr[$post['deliver_addr_book']['id_'.$k]]['metro'];
                    
                    $delivery_address_simplified=$addr_arr[$post['deliver_addr_book']['id_'.$k]]['city']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['postcode']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['street_address_1']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['street_address_2']." ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['region'];
                    
                    $delivery_address_ymaps=$addr_arr[$post['deliver_addr_book']['id_'.$k]]['region'].", ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['city'].", ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['postcode'].", ".
                            $addr_arr[$post['deliver_addr_book']['id_'.$k]]['street_address_1'];
                    
                  } else {
                      
                    $delivery_name=$post['deliver_new_addr_fio'][$k]." ".$post['deliver_new_addr_company'][$k];
                    
                    $delivery_address=$post['deliver_new_addr_city'][$k]." ".
                            $post['deliver_new_addr_postcode'][$k]."<br/>".
                            $post['deliver_new_addr_street_address_1'][$k]." ".
                            $post['deliver_new_addr_street_address_2'][$k]." ".
                            $post['deliver_new_addr_metro'][$k]."<br/>(".
                            $post['deliver_new_addr_country'][$k]." ".$post['deliver_new_addr_region'][$k].")";
                    
                    $city=$post['deliver_new_addr_city'][$k];
                    
                    $postcode=$post['deliver_new_addr_postcode'][$k];
                    
                    $metro=$post['deliver_new_addr_metro'][$k];
                    
                    $delivery_address_simplified=$post['deliver_new_addr_city'][$k]." ".
                            $post['deliver_new_addr_postcode'][$k]." ".
                            $post['deliver_new_addr_street_address_1'][$k]." ".
                            $post['deliver_new_addr_region'][$k];
                    
                    $delivery_address_ymaps=$post['deliver_new_addr_region'][$k].", ".
                            $post['deliver_new_addr_city'][$k].", ".
                            $post['deliver_new_addr_postcode'][$k].", ".
                            $post['deliver_new_addr_street_address_1'][$k];
                    
                       if(@$cid>0) { // db 2 - адресная книга, новая запись
                           
                       $check=mysql_kall("SELECT customers_id FROM ".DB_PREFIX."customers_address_book 
                           WHERE street_address_1='".textprocess($post['deliver_new_addr_street_address_1'][$k],'sql')."' AND 
                                 street_address_2='".textprocess($post['deliver_new_addr_street_address_2'][$k],'sql')."' AND 
                                 city='".textprocess($post['deliver_new_addr_city'][$k],'sql')."' AND 
                                 postcode='".textprocess($post['deliver_new_addr_postcode'][$k],'sql')."' AND 
                                 country='".textprocess($post['deliver_new_addr_country'][$k],'sql')."' AND 
                                 region='".textprocess($post['deliver_new_addr_region'][$k],'sql')."' AND 
                                 metro='".textprocess($post['deliver_new_addr_metro'][$k],'sql')."' AND 
                                 customers_id='".@$cid."'");
                       
                            if(mysql_num_rows($check)>0) {
                       $sql_2="";
                            } else {
                       $sql_2="INSERT INTO ".DB_PREFIX."customers_address_book 
                           (company, fio, street_address_1, street_address_2, city, postcode, country,
                           region, metro, default_shipping, default_billing, customers_id) VALUES 
                           ('".textprocess($post['deliver_new_addr_company'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_fio'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_street_address_1'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_street_address_2'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_city'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_postcode'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_country'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_region'][$k],'sql')."',
                            '".textprocess($post['deliver_new_addr_metro'][$k],'sql')."','0','0','".@$cid."')"; 
                            }
                       }
                       
                   }

                // billing
                   
                $billing_address=$delivery_address;
                $billing_name=$fio;
                $billing_address_simplified=$delivery_address_simplified;
                $yur_inn="--";
                
                if($c_type!="unknown"&&$yur_status=="1") {
                    
                    if(isset($yur_book)) {} else { $yur_book=$this->addrbook($_SESSION['customers_id'], 'yur'); }
                    
                    $billing_name=$yur_book['company'];
                    $billing_address=$yur_book['city']." ".
                                $yur_book['postcode']."<br/>
                              ".$yur_book['address']."<br/>(".
                                $yur_book['region'].")";
                    
                    $billing_address_simplified="ИНН ".$yur_book['inn'].", ".$yur_book['city'].", р/c ".
                            $yur_book['schet']." в ".$yur_book['bank'].", БИК ".$yur_book['bik'].", корр/c ".$yur_book['korr'];
                    
                    $delivery_address_simplified=$billing_address_simplified; 
                    // TODO: доработать этот момент. сейчас получается, что для юр лиц доставка все время по юрид адресу, а это неправильно
                    
                    $yur_inn=$yur_book['inn'];
                    $postcode=$yur_book['postcode'];
                }

                // delivery: если flag=2 доставка не рассчиталась!
                
                    $deliver_arr=__unserialize(stripslashes($post['deliver_arr']['id_'.$k]));
                    $deliver_bask=explode("_",$post['deliver_bask']['id_'.$k]);
                    $current_deliver=$deliver_arr[trim($deliver_bask[0])];

                // payment: если after_flag=1 есть спец условия!
                    
                    $payment_skidka_flag=0;
                    $payment_arr=__unserialize(stripslashes($post['payment_arr']['id_'.$k]));
                    $payment_bask=explode("_",$post['payment_bask']['id_'.$k]);
                    $current_payment=$payment_arr[trim($payment_bask[0])];
                    
                    if($current_payment['skidka']<0||$current_payment['skidka']>0) { 
                        $payment_skidka_flag=1; 
                        $current_payment2=$current_payment[0]." (скидка ".$current_payment['skidka']."р.)";                         
                    } else { 
                        $current_payment2=$current_payment[0]; 
                    }

                // content
                    
                    $content=""; 
                    $content_price=0; 
                    $pure_price=0; 
                    $a3="";
                    
                    foreach($v as $kk=>$vv) {
                        
                        if($kk=="p") { $content_price=$vv; }
                        
                        if($kk=="p"||$kk=="w"||$kk=="q"||$kk=="catshopid"||$kk=="baskid"||$kk=="info") { continue; }
                        
                         $content.=$post['quantity'][$kk]." x <a href=".$show_bsk[$kk]['nazv_link'].">".
                                                            $show_bsk[$kk]['nazv']."</a> = ".$vv." р.<br/>";
                         
                         $pure_price=$pure_price+$show_bsk[$kk]['total_pure_price'];

                         if($post['attrs_descr'][$kk]!=""&&$post['attrs_descr'][$kk]!="N;") { // сохраняем свойства
                             
                                if(count($show_bsk[$kk]['form']['attr_form'])>0) {
                                       foreach($show_bsk[$kk]['form']['attr_form'] as $a1=>$a2) {
                                            if($a2!="") { 
                                                $a3.="basket_id='".$a1."' OR "; }
                                       }                             
                                }                             
                             }
                        }

                // totalprice
                        
                   $totalprice=0;
                   $totalprice=$content_price+@$current_deliver['summ']-$current_payment['skidka'];

                // pv
                   
                if ($c_type == "distr" || $c_type == "guest") {
                    
                    $pvarr = $v;
                    
                    unset($pvarr['q'], $pvarr['p'], $pvarr['w'], $pvarr['baskid'], $pvarr['catshopid'], $pvarr['info']);
                    
                    $pvtot = $this->pvcollect($pvarr, $post['quantity']);
                    
                    if ($c_type == "guest") {
                        if (isset($guestfrom)) { } else {
                            
                            $guestfrom = mysql_result(mysql_kall("SELECT guest_from_id FROM " . DB_PREFIX . "customers 
                                WHERE customers_id='" . $_SESSION['customers_id'] . "'"), 0, 'guest_from_id');
                        }
                    }
                } else {
                    $guestfrom = 0;
                    $pvtot = 0;
                }

                // определяем oid_real. временно отключаем
                
                $oid_real=@mysql_result(mysql_kall("SELECT orders_id_real FROM ".DB_PREFIX."orders 
                WHERE orders_id_cluster='".$cluster."' AND catshop='".$catsup['nnn']."' ORDER BY orders_id_real DESC LIMIT 1"),0,'orders_id_real');
                
                if($oid_real==""||$oid_real<=0) { $oid_real=0; }
                
                $oid_real=$oid_real+1;

                //
                //$real_oid=@mysql_kall("SELECT oid_real FROM u59221.all_orders 
                //    WHERE mag='yogamag' AND oid_cluster='".$cluster."' ORDER BY nnn DESC LIMIT 1");                
                //$real_oid2=@mysql_result($real_oid,0,'oid_real');
                //$oid_real=$real_oid2+1;

                $oidhash=md5(rand(0,10).time().$oid_real.@$cid.$totalprice); // oidhash
                
                /* DB DB DB DB DB */

                // db 1 - заказ (основная бд)
                
                $sql_1 = "INSERT INTO " . DB_PREFIX . "orders (oidhash, catshop, orders_id_cluster, orders_id_real, reg_order, 
                            customers_id, dat, fio, city, billing_name, billing_address, delivery_name, delivery_address, 
                                customers_type, yur_status, email, phone, last_modified, `status`, content, delivery_method,
                                    delivery_price, delivery_price_flag, content_price, price, payment_method, pv, pv_parent_distr_id) 
                                    
                                VALUES 
                                    
                    ('" . $oidhash . "',
                     '" . $v['catshopid'] . "',
                     '" . $cluster . "',
                     '" . $oid_real . "',
                     '" . $reg_order_sql . "',
                     '" . @$cid . "',
                     '" . time() . "',
                     '" . textprocess(@$fio, 'sql') . "',
                     '" . textprocess(@$city, 'sql') . "',
                     '" . textprocess($billing_name, 'sql') . "',
                     '" . textprocess($billing_address, 'sql') . "',
                     '" . textprocess($delivery_name, 'sql') . "',
                     '" . textprocess($delivery_address, 'sql') . "',
                     '" . @$c_type . "',
                     '" . @$yur_status . "',
                     '" . textprocess(@$email, 'sql') . "',
                     '" . textprocess(@$phone, 'sql') . "',
                     '" . time() . "',
                     '" . @$o_status['id'] . "',
                     '" . textprocess($content, 'sql') . "',
                     '" . $current_deliver[0] . "',
                     '" . $current_deliver['summ'] . "',
                     '" . $current_deliver['flag'] . "',
                     '" . $content_price . "',
                     '" . $totalprice . "',
                     '" . $current_payment2 . "',
                     '" . @$pvtot . "',
                     '" . @$guestfrom . "')"; // -> deliver_flag = 1-рассчитано, 2 - нерассчитано, 0 - не рассчитано

                mysql_kall($sql_1) or die(mysql_error());

                $oid=mysql_insert_id(); // oid new

                // db 2 - address book add
                
                if(trim($sql_2)!="") { mysql_kall($sql_2) or die(mysql_error()); }

                // db 7 - статистика по pv
                
                 if(@$pvtot>0&&isset($_SESSION['customers_id'])) {
                     
                   mysql_kall("INSERT INTO ".DB_PREFIX."customers_scores (customers_id, from_whom_cid, orders_id, orders_price, orders_pv)
                       VALUES ('".@$_SESSION['customers_id']."','".@$guestfrom."','".$oid."','".$totalprice."','".@$pvtot."')");                   
                 }

                // db 3 - товары в заказе
                 
                foreach ($v as $kk => $vv) {
                    if ($kk == "p" || $kk == "w" || $kk == "q" || $kk == "catshopid" || $kk == "baskid" || $kk == "info") {
                        continue;
                    }
                    
                    if ($c_type == "distr" || $c_type == "guest") {
                        $pvfin = $this->pvcollect(array($kk), $post['quantity']);
                    } else {
                        $pvfin = 0;
                    }
                    
                    $sql_3 = "INSERT INTO " . DB_PREFIX . "orders_products 
                        (orders_nnn, reg_prd, products_id, products_name, products_price, products_quantity, final_price, pv_prd_final) 
                        VALUES ('" . $oid . "','1','" . $kk . "','" . $show_bsk[$kk]['nazv'] . "','" . ($vv / ($post['quantity'][$kk])) . "',
                                      '" . $post['quantity'][$kk] . "','" . $vv . "','" . $pvfin . "')";
                    
                    $bill_arr_prd[$kk]['nazv'] = $show_bsk[$kk]['nazv'];
                    $bill_arr_prd[$kk]['price_one'] = ($vv / ($post['quantity'][$kk]));
                    $bill_arr_prd[$kk]['qty'] = $post['quantity'][$kk];
                    $bill_arr_prd[$kk]['price_tot'] = $vv;
                    
                    mysql_kall($sql_3) or die(mysql_error());
                    
                    $op_id[$kk] = mysql_insert_id();
                    
                    mysql_kall("UPDATE " . DB_PREFIX . "products SET ordered=ordered+" . $post['quantity'][$kk] . ", 
                        qty=qty-" . $post['quantity'][$kk] . " WHERE nnn='" . $kk . "'");

                    // обновляем колиество везде
                    if(DB_SERV!="localhost") {
                    $qty = mysql_kall("SELECT qty FROM " . DB_PREFIX . "products WHERE nnn='" . $kk . "'");
                    update_qty_everywhere('', $kk, mysql_result($qty, 0, 'qty')); // UPDATE QTY EVERYWHERE
                    }
                }

                // добавление не товарных позиций! (то что автоматически) в комментарии, т.к. в таблице уже все посчитано!
                // скидка - из-за типа клиента, из-за вида оплаты, из-за купона, из-за свойств?
            
                $comment_add_string = "";

                if ($content_price != $pure_price) {
                    $fl = 0;
                    $comment_add_string.="Стоимость товара показана с учетом следующих скидок:<br>";
                    if ($c_type != "user" && $c_type != "unknown") {
                        $fl = 1;
                        $comment_add_string.="* скидка для типа клиента (в соответствующих товарах), ";
                    }
                    if (@$_SESSION['customers_coupon_active'] == "1") {
                        $fl = 1;
                        $comment_add_string.="* скидка по купону, ";
                    } else {
                        if (@$_SESSION['customers_discount'] > 0) {
                            $fl = 1;
                            $comment_add_string.="* индивидуальная скидка клиента, ";
                        }
                    }
                    if ($fl != "1") {
                        $comment_add_string.="* товар продается со скидкой, ";
                    }
                    $comment_add_string = substr($comment_add_string, 0, -2);
                    $comment_add_string.=": <b>" . number_format(($pure_price - $content_price), 2) . " р.</b><br>";
                    //$morefields[]="Скидки"; $morefields_summ[]=$pure_price-$content_price; <- не надо т.к. это уже включено в цену товара
                }

                if ($payment_skidka_flag == "1") {
                    $comment_add_string.="* скидка для выбранного вида оплаты: <b>" . $current_payment['skidka'] . " р.</b><br>";
                    $morefields[] = "Скидка для вида оплаты";
                    $morefields_summ[] = $current_payment['skidka'];
                }

                if ($post['bask_comments']['id_' . $k] != "" && $comment_add_string != "") {
                    $comment_add_string.="---------------------------------<br>";
                }

                $comment_add_string.=$post['bask_comments']['id_' . $k];

                if ($post['pickpoint_addr']['id_' . $k] != "") {
                    $comment_add_string.="<br>-----------------------------------<br><b>Адрес, где нужно забрать товар:</b> " . 
                            $post['pickpoint_addr']['id_' . $k] .
                            "<br><br><b>Постамат ID:</b> " . $post['pickpoint_id']['id_' . $k] . "";
                } 
                // TODO: сюда новые виды доставки и тд (сделать более универсально)

                // обновляем купон
                
                if (@$_SESSION['customers_coupon_active'] == "1") {
                    
                    mysql_kall("UPDATE " . DB_PREFIX . "customers_specials SET status='0', oid='" . $oid . "'
                            WHERE used_by_customer_id='" . @$_SESSION['customers_id'] . "' 
                                AND catshop='" . SHOP_NNN . "' AND status='1'") or die(mysql_error());
                    
                    $_SESSION['customers_coupon_active'] = 0;
                    $_SESSION['customers_discount'] = @$_SESSION['customers_old_discount'];
                }

                // db 4 - (переносим) свойства товаров
                
                if($a3!="") {
                    $a4=mysql_kall("SELECT * FROM ".DB_PREFIX."customers_basket_attr WHERE ".substr($a3,0,-3)."");
                    if(mysql_num_rows($a4)>0) {
                        $a5=mysql_fetch_assoc($a4);
                        do {
                             $sql_4="INSERT INTO ".DB_PREFIX."orders_products_attr (orders_nnn, orders_products_nnn, grp_sub_id, prd_id, attr_id)
                                 VALUES ('".$oid."','".$op_id[$a5['prd_id']]."','".$a5['grp_sub_id']."','".$a5['prd_id']."','".$a5['attr_id']."')";

                             mysql_kall($sql_4) or die(mysql_error());

                           } while($a5=mysql_fetch_assoc($a4));
                        }
                    }

                // подготавливаем свойства товаров для емейла
                    
                $attr_email="";
                if (count(@$rearr) > 0) {
                    foreach ($rearr as $attr1 => $attr2) {
                        foreach ($attr2 as $attr3 => $attr4) {
                            
                            $attr_email.=$show_bsk[$attr1]['nazv'] . ":<br>";
                            
                            foreach ($attr4 as $attr5 => $attr6) {
                                foreach ($attr6 as $attr7 => $attr8) {
                                    
                                    $attr_email.=$attr7 . " --> " . $attr8 . "<br>";
                                    
                                } 
                                
                                $attr_email.="-----------------<br/>";
                            }
                        }
                    }
                }

                // db 6 - счета
                if (@$current_payment['after_flag'] == "1") { // надо создать счет                    

                    $bills_str_flag = 1;                    
                    
                    if (@$current_deliver['flag'] == "2") { } else { // если доставка не рассчитана, то не создавать счет

                        $qiwiphone2=strtr(@$phone,array("("=>"",")"=>""," "=>"","-"=>"","+"=>""));
                        $qiwiphone=substr(@$qiwiphone2,-10);
                
                        $bill_content=""; 
                        
                        $billhash_id=$cluster."_".$oid_real."_0_".
                                   substr((strtr($catsup['remote_addr'],array("http://"=>"","www."=>""))),0,1); // billid!
                                      
                        if($current_payment[8]=="да") { 
                        $constantname="PAYMENT_TYPE_".mb_strtoupper(trim($payment_bask[0]));
                        if(@constant($constantname)!="") { 
                           $bill_content0=strtr(constant($constantname), array(
                               "{PHONE}"=>textprocess(@$qiwiphone, 'sql'),
                               "{BILLPRC}"=>$totalprice,
                               "{BILLID}"=>$billhash_id,
                               "{OID}"=>$oid_real,
                               "{SHOPNAME}"=>$catsup['nazv'],
                               "{SHOPLNK}"=>$catsup['remote_addr'],
                               "{EMAIL}"=>$email
                           ));
                           $bill_content1=explode("[###]",$bill_content0);
                           $bill_content=$bill_content1[0];
                        }
                        if($current_payment[9]=="нет") { $bills_message_afterwards.=$bill_content; }
                        }
                        
                       if($current_payment[7]!="нет") { // внешний сервис

                           $billhash = md5(rand(0, 10) . time() . trim($payment_bask[0])  . @$cid . $b_status['status_id'] . $oid);
                           
                           if($current_payment[9]=="нет") { // необходимо подтверждение менеджером
                               $makebill_email = $billhash;
                               $bills_str.="<a href=" . $catsup['remote_addr'] . "/user/bills/" . 
                                            $billhash . ">Ссылка</a> для оплаты заказа №" . $oid_real . " (" . $catsup['nazv'] . ")<br/>";

                               $sent=1;
                           } else { $sent=0; }
                           
                           mysql_kall("INSERT INTO " . DB_PREFIX . "orders_bills 
                                (orders_nnn, customers_id, bill_status_id, bill_type, bill_link, bill_content, bill_prc, catshop, sent, external_flag, 
                                payment_method) VALUES
                                ('" . $oid . "','" . @$cid . "','" . $b_status['id'] . "','" . trim($payment_bask[0]) . "',
                                    '" . $billhash . "','".strtr($bill_content,array("target=\"_blank\""=>"","'" => "&#039;"))."',
                                        '".$totalprice."','" .$v['catshopid'] . "','".$sent."','1','" . $current_payment[0] . "')");
 
                       } else {
                        
                        $bill_arr = array("{OID}" => $oid_real,
                            "{DATE_PURCHASED}" => time(),
                            "{BILLING_NAME}" => textprocess($billing_name, 'sql'),
                            "{DELIVERY_NAME}" => textprocess($delivery_name, 'sql'),
                            "prds_arr" => $bill_arr_prd,
                            "catshopid" => $v['catshopid'],
                            "catsup" => $catsup,
                            "{DELIVERY_PRICE}" => $current_deliver['summ'],
                            "morefields" => $morefields,
                            "morefields_summ" => $morefields_summ,
                            "{TOTAL_PRICE}" => $totalprice,
                            "{DELIVERY_ADDRESS}" => textprocess(strtr($delivery_address, array("<br/>" => " / ")), 'sql'),
                            "{BILLING_ADDRESS}" => textprocess(strtr($billing_address, array("<br/>" => " / ")), 'sql'),
                            "delivery_address_simplified" => textprocess($delivery_address_simplified, 'sql'),
                            "billing_address_simplified" => textprocess($billing_address_simplified, 'sql'),
                            "{YUR_INN}" => $yur_inn);
                        
                        $makebill = $this->make_bills($bill_arr);

                        $makebill_email = "";
                        
                        foreach ($makebill as $mb => $mb2) {
                            
                            $billhash = md5(rand(0, 10) . time() . $mb . @$cid . $b_status['status_id'] . $oid);

                            $sent = 0;
                            
                            if ($yur_status == "1") {
                                if ($mb == "bill_1.php") {
                                    
                                    $makebill_email = $billhash;
                                    $sent = 1;
                                    $bills_str.="<a href=" . $catsup['remote_addr'] . "/user/bills/" . 
                                            $billhash . ">Квитанция/счет</a> к заказу №" . $oid_real . " (" . $catsup['nazv'] . ")<br/>";
                                }
                            } else {
                                if ($mb == "bill.php") {
                                    
                                    $makebill_email = $billhash;
                                    $sent = 1;
                                    $bills_str.="* <a href=" . $catsup['remote_addr'] . "/user/bills/" . 
                                            $billhash . ">Квитанция/счет</a> к заказу №" . $oid_real . " (" . $catsup['nazv'] . ")<br/>";
                                }
                            }
                            
                            mysql_kall("INSERT INTO " . DB_PREFIX . "orders_bills 
                                (orders_nnn, customers_id, bill_status_id, bill_type, bill_link, bill_content, bill_prc, catshop, sent, payment_method) 
                                VALUES
                                ('" . $oid . "','" . @$cid . "','" . $b_status['id'] . "','" . $mb . "',
                                    '" . $billhash . "','" . strtr($mb2, array("'" => "&#039;")) . "',
                                        '".$totalprice."', '" . $v['catshopid'] . "','" . $sent . "','" . $current_payment[0] . "')");
                            
                       }}
                    }
                }

                // отправляем емейлы
                
                $delivery_method=$current_deliver[0]; 
                $delivery_method_2part="";
                
                if($current_deliver['flag']=="1") { 
                    $delivery_method.=": ".$current_deliver['summ']." р."; $delivery_method_2part=$current_deliver['summ']." р."; } else {
                    $delivery_method.=": (стоимость доставки рассчитывается.)"; $delivery_method_2part="(стоимость доставки рассчитывается.)";  }

                $templ_arr=array("catshopid"=>$v['catshopid'],
                                 "catsup"=>$catsup,
                                 "{OID}"=>$oid_real,
                                 "oidhash"=>$oidhash,
                                 "{DATE_PURCHASED}"=>date("d.m.Y",time()),
                                 "{REG_ORDER_STATUS_NAME}"=>$o_status['nazv'],
                                 "{CONTENT}"=>$content,
                                 "{PRDS_PRICE}"=>$content_price." р.",
                                 "{DELIVERY_METHOD}"=>$delivery_method,
                                 "{TOTAL_PRICE}"=>$totalprice." р.",
                                 "{DELIVERY_NAME}"=>$delivery_name,
                                 "{DELIVERY_ADDRESS}"=>$delivery_address,
                                 "{BILLING_NAME}"=>$billing_name,
                                 "{BILLING_ADDRESS}"=>$billing_address,
                                 "{PAYMENT_METHOD}"=>$current_payment2,
                                 "bills"=>$makebill_email,
                                 "{ATTRIBUTES}"=>$attr_email,
                                 "{COMMENTS}"=>$comment_add_string
                                );
                
                $email_arr=array($email, EMAIL_ADMIN, "garcia82@yandex.ru"); // TODO: копии емейлов
                
                $mail2all=$this->mail2all(EMAIL_TXT_NEWORDER, $templ_arr, $email_arr);

                $orders_str.=$catsup['nazv'].". <a href=".$catsup['remote_addr']."/user/orders/".
                        $oidhash.">Заказ №".$oid_real."</a> на сумму ".number_format($totalprice,2)." <span class=\"rur\">p</span><br>";

                // яндекс.карты
                
                $ymaps_str2="";                
                if (@$delivery_address_ymaps != "" && @$current_deliver[9] != "нет") {  
                    
                    $ymap_img = $this->ymaps($delivery_address_ymaps, $madeorders, $oidhash); /* * */
                    
                    $ymap_img2 = $catsup['remote_addr'] . "/upload/ymaps/" . $oidhash . ".jpg";
                    $ymap_img3 = MAINURL_5 . "/upload/ymaps/" . $oidhash . ".jpg";
                    if (file_exists($ymap_img3)) {
                        $ymaps_str.="* " . $delivery_address_ymaps . "<br><img src=" . $ymap_img2 . "><br><br>";
                        $ymaps_str2 = "<img src=" . $ymap_img2 . ">";
                    }
                }

                // счет был выписан
                
                if($makebill_email!="") { 
                    if($current_payment[7]!="нет") { 
                    $bill_str2="Ваша ссылка для оплаты находится по этому адресу: <a href=".
                            $catsup['remote_addr']."/user/bills/".$makebill_email.">".$catsup['remote_addr']."/user/bills/".$makebill_email."</a>";     
                    } else {
                    $bill_str2="Ваша квитанция/счет для оплаты находится по этому адресу: <a href=".
                            $catsup['remote_addr']."/user/bills/".$makebill_email.">".$catsup['remote_addr']."/user/bills/".$makebill_email."</a>"; 
                    }
                } else { 
                    $bill_str2=""; }
                    
                // подготавливаем страницу заказа заранее (все кроме статуса)
                    
                $templ_page_arr=strtr(PAGE_OLD_ORDER_TXT,array("catshopid"=>$v['catshopid'],
                                 "{OID}"=>$oid_real,
                                 "{DATE_PURCHASED}"=>date("d.m.Y",time()),
                                 "{CONTENT}"=>$content,
                                 "{PRDS_PRICE}"=>$content_price." р.",
                                 "{DELIVERY_METHOD}"=>$delivery_method,
                                 "{TOTAL_PRICE}"=>$totalprice." р.",
                                 "{DELIVERY_NAME}"=>$delivery_name,
                                 "{DELIVERY_ADDRESS}"=>$delivery_address,
                                 "{BILLING_NAME}"=>$billing_name,
                                 "{BILLING_ADDRESS}"=>$billing_address,
                                 "{PAYMENT_METHOD}"=>$current_payment2,
                                 "{ATTRIBUTES}"=>$attr_email,
                                 "{COMMENTS}"=>$comment_add_string,
                                 "{YMAPS_IMG}"=>@$ymaps_str2,
                                 "{BILLS}"=>@$bill_str2
                                ));

                // db 5 - история статусов и комментарии
                
                $sql_5="INSERT INTO ".DB_PREFIX."orders_status_history (orders_nnn, date_added, orders_status_id, 
                    orders_status_name, comment, order_cache) VALUES ('".$oid."','".time()."','".@$o_status['id']."',
                        '".@$o_status['nazv']."','".textprocess($comment_add_string,'sql')."',
                            '".textprocess(nl2br($templ_page_arr),'sql')."')";

                mysql_kall($sql_5) or die(mysql_error());
                
               // old db
                
               if($c_type=="user") { $c_type_alt="client"; } else { $c_type_alt=$c_type; }
               
               if(DB_SERV!="localhost") { // не для локалхоста
                   /*
                    $this->order2old("u59221_5", "yogamag", 
                            array(  "oid_real"=>$oid_real, 
                                    "email"=>textprocess($email,'sql'),
                                    "reg_order"=>$reg_order,
                                    "c_fio"=>textprocess(@$fio,'sql'),
                                    "c_company"=>$billing_name,
                                    "c_address"=>textprocess($billing_address,'sql'),
                                    "c_address2"=>"",
                                    "c_city"=>textprocess(@$city,'sql'),
                                    "c_postcode"=>$postcode,"c_region"=>"", 
                                    "c_country"=>"",
                                    "c_phone"=>textprocess(@$phone,'sql'), 
                                    "d_fio"=>textprocess($delivery_name,'sql'), 
                                    "d_company"=>$billing_name,
                                    "d_address"=>textprocess($delivery_address,'sql'), 
                                    "d_address2"=>"", 
                                    "d_city"=>textprocess(@$city,'sql'),
                                    "d_postcode"=>$postcode, 
                                    "d_region"=>"", 
                                    "d_country"=>"", 
                                    "b_fio"=>$billing_name, 
                                    "b_company"=>$billing_name,
                                    "b_address"=>textprocess($billing_address,'sql'), 
                                    "b_address2"=>"", "b_city"=>textprocess(@$city,'sql'),
                                    "b_postcode"=>$postcode, 
                                    "b_region"=>"", 
                                    "b_country"=>"", 
                                    "payment_method"=>$current_payment2,
                                    "purchased_dat"=>time(), 
                                    "metro"=>textprocess(@$metro,'sql'), 
                                    "orders_products"=>$bill_arr_prd,
                                    "comments"=>textprocess($comment_add_string,'sql'), 
                                    "prd_price"=>$content_price,
                                    "delivery_method_1"=>$current_deliver[0],
                                    "delivery_method_2"=>$delivery_method_2part,
                                    "delivery_method_price"=>@$current_deliver['summ'],
                                    "final_price"=>$totalprice,
                                    "status_name"=>$o_status['nazv'],
                                    "client_type"=>$c_type_alt, 
                                    "d_full_address"=>textprocess(strtr($delivery_address,array("<br/>"=>", ")),'sql'),
                                    "content"=>textprocess($content,'sql'), 
                                    "billhash"=>@$makebill_email));  
                         */                   
                         }

                    $sql_1 = "";
                    $sql_2 = "";
                    $sql_3 = "";
                    $sql_4 = "";
                    $sql_5 = "";
                    $sql_6 = ""; //?
                    
                    unset($morefields, $morefields_summ, $bill_arr_prd);
                    
            } // несколько заказов-корзин

               
           // финальная страница:
            
           if($madeorders>1) { 
               $message_flag[0]=2; } else { 
               $message_flag[0]=1; }
           
           $message_flag[1]=3; 
            
           if(@$bills_str_flag=="1") { 
               if($bills_str!="") {
                   $message_flag[2]=5; } else { 
                   $message_flag[2]=4; }}
                   
           $message_flag[3]=6; 
           
           if(@$ymaps_str!="") { 
               $message_flag[4]=7; }

           $message_page=explode("###",PAGE_NEW_ORDER_TXT); 
           $message_page2="";
           
           foreach($message_flag as $mf=>$mf2) { $message_page2.=$message_page[$mf2];  }
           
           $message_page2=strtr($message_page2,array("{ORDER_LIST}"=>$orders_str,
                                                     "{BILL_LINK}"=>$bills_str.$bills_message_afterwards,
                                                     "{CONTACT_LINK}"=>$catsup['remote_addr']."/user/contact",
                                                     "{YMAPS_IMG}"=>@$ymaps_str));

           // очистка корзины, кэша, обновление статистики:
           
           if (!isset($_SESSION['customers_id'])) {
                $c_id = "0";
                $temp_session = session_id();
            } else {
                $c_id = $_SESSION['customers_id'];
                $temp_session = "";
                mysql_kall("UPDATE " . DB_PREFIX . "customers 
                    SET num_orders=num_orders+" . $madeorders . " WHERE customers_id='" . $_SESSION['customers_id'] . "'");
            }
            
            mysql_call("DELETE FROM " . DB_PREFIX . "customers_basket_lists 
                WHERE list_name='[basket]' AND customers_id='" . @$c_id . "' AND temp_session='" . $temp_session . "'");
            
            mysql_call("DELETE FROM " . DB_PREFIX . "customers_basket_attr 
                WHERE customers_id='" . $c_id . "' AND temp_session='" . $temp_session . "'");
            
            $_SESSION['customers_basket_num'] = 0;
            
            clearfile('', '0', 'txts', '1'); // удаляем клиентские файлы @reviewlate: насколько это актуально?
            
            $this->clear_basket_cookies();

            $_SESSION['send_login_message'] = "Заказ оформлен. Спасибо!";

           return nl2br($message_page2);
           
           exit; // ? после return @reviewlate
        }

    // создаем счета и квитанции
    function make_bills($b) { // создание счетов!
Debug::log();
                $bill_ret=array();

                global $N0, $Ne0, $Ne1, $Ne2, $Ne3, $Ne6;
                $N0 = 'ноль';
                $Ne0 = array(0 => array('','один','два','три','четыре','пять','шесть',
                                        'семь','восемь','девять','десять','одиннадцать',
                                        'двенадцать','тринадцать','четырнадцать','пятнадцать',
                                        'шестнадцать','семнадцать','восемнадцать','девятнадцать'),
                             1 => array('','одна','две','три','четыре','пять','шесть',
                                        'семь','восемь','девять','десять','одиннадцать',
                                        'двенадцать','тринадцать','четырнадцать','пятнадцать',
                                        'шестнадцать','семнадцать','восемнадцать','девятнадцать')
                             );
                $Ne1 = array('','десять','двадцать','тридцать','сорок','пятьдесят',
                             'шестьдесят','семьдесят','восемьдесят','девяносто');
                $Ne2 = array('','сто','двести','триста','четыреста','пятьсот',
                             'шестьсот','семьсот','восемьсот','девятьсот');
                $Ne3 = array(1 => 'тысяча', 2 => 'тысячи', 5 => 'тысяч');
                $Ne6 = array(1 => 'миллион', 2 => 'миллиона', 5 => 'миллионов');

                // {OID}{SHOPLETTER}{DATE_PURCHASED}{BILLING_NAME}{DELIVERY_NAME}{N_PRD}
                // {PRD_MODEL}{PRD_NAME}{PRD_QUANTITY}{PRD_PRICE_ONE}{PRD_PRICE_TOTAL}
                // {N_LAST}{DELIVERY_PRICE}{TOTAL_PRICE}{N_ALL}{TOTAL_PRICE_PROPIS}
                // {DELIVERY_ADDRESS}{BILLING_ADDRESS}{YUR_INN}
                // {EDINICA}{OKEI}{PRD_QUANTITY_TOTAL}{N_ALL_PROPIS}

                $catsup=$b['catsup'];
                $months=array("","января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
                $ruble = array(1 => 'рубль', 2 => 'рубля', 5 => 'рублей');

                $bills_templates=array("bill_1.php","bill_2.php","bill_3.php","bill.php");
                foreach($bills_templates as $bt) {

                $bill_template=get_include_contents(MAINURL_5."/template/".TEMPLATE."/".$bt);

                $sum0=explode(".",number_format($b['{TOTAL_PRICE}'],2,'.',''));
                $sum = (int)$sum0[0];
                $sum1=written_number($sum);
                $sum2=written_number((int)$sum0[1]);
                $total_price_propis=mb_strtoupper(substr($sum1,0,1)).substr($sum1,1)." ".$ruble[num_125($sum)]." ".$sum2." коп.";

                $b_name=$b['{BILLING_NAME}']; $d_name=$b['{DELIVERY_NAME}'];
                if($bt=="bill_3.php") { $b_name=$b['billing_address_simplified']; $d_name=$b['delivery_address_simplified']; }
                if($bt=="bill_2.php"||$bt=="bill_1.php") { $b_dat=date("d",$b['{DATE_PURCHASED}'])." ".$months[(int)date("m",$b['{DATE_PURCHASED}'])]." ".date("Y",$b['{DATE_PURCHASED}']); } else {
                    $b_dat=date("d.m.Y",$b['{DATE_PURCHASED}']); }

                   $bill_template = strtr($bill_template, array(
                     "{OID}"=>$b['{OID}'],
                     "{SHOPLETTER}"=>mb_strtolower(substr($catsup['nazv'],0,1)),
                     "{DATE_PURCHASED}"=>$b_dat,
                     "{BILLING_NAME}"=>$b_name,
                     "{DELIVERY_NAME}"=>$d_name,
                     "{DELIVERY_PRICE}"=>number_format($b['{DELIVERY_PRICE}'],2),
                     "{TOTAL_PRICE}"=>number_format($b['{TOTAL_PRICE}'],2),
                     "{TOTAL_PRICE_PROPIS}"=>$total_price_propis,
                     "{DELIVERY_ADDRESS}"=>$b['{DELIVERY_ADDRESS}'],
                     "{BILLING_ADDRESS}"=>$b['{BILLING_ADDRESS}'],
                     "{YUR_INN}"=>$b['{YUR_INN}'],
                     "{MAINURL}"=>MAINURL,
                     "{INN}"=>BANK_VAL_INN,
                     "{KPP}"=>BANK_VAL_KPP,
                     "{COMPANY}"=>BANK_VAL_NAME,
                     "{COMPANY_ADDRESS}"=>BANK_VAL_ADDRESS,  
                     "{BANK_NAME}"=>BANK_VAL_BANK,
                     "{RS}"=>BANK_VAL_RS,
                     "{KORS}"=>BANK_VAL_KS,
                     "{BIK}"=>BANK_VAL_BIK,
                     "{COMPANY_DIRECTOR}"=>BANK_VAL_DIRECTOR,
                     "{COMPANY_BUHG}"=>BANK_VAL_BUHG,
                     "{OKUD}"=>BANK_VAL_OKUD,
                     "{OKPO}"=>BANK_VAL_OKPO  
                    ));

                $bill_template_1=explode("####begin",$bill_template);
                $bill_template_2=explode("####end",$bill_template_1[1]);

               $bill_template_3=""; $n=1; $qty_all=0;

               foreach($b['prds_arr'] as $k=>$v) {
                           $bill_template_4 = strtr($bill_template_2[0], array(
                               "{N_PRD}"=>$n,
                               "{PRD_NAME}"=>$v['nazv'],
                               "{PRD_QUANTITY}"=>number_format($v['qty'],3),
                               "{PRD_PRICE_ONE}"=>number_format($v['price_one'],2),
                               "{PRD_PRICE_TOTAL}"=>number_format($v['price_tot'],2),
                               "{EDINICA}"=>"шт",
                               "{OKEI}"=>"796",
                        ));
                           $n++; $qty_all=$qty_all+$v['qty'];
                           $bill_template_3.=$bill_template_4;
                   }
                   if(is_array($b['morefields'])) {
                   foreach($b['morefields'] as $k=>$v) {
                           $bill_template_4 = strtr($bill_template_2[0], array(
                               "{N_PRD}"=>$n,
                               "{PRD_NAME}"=>$v,
                               "{PRD_QUANTITY}"=>"1.000",
                               "{PRD_PRICE_ONE}"=>number_format($b['morefields_summ'][$k],2),
                               "{PRD_PRICE_TOTAL}"=>number_format($b['morefields_summ'][$k],2),
                               "{EDINICA}"=>"-",
                               "{OKEI}"=>"001",
                        ));
                           $n++; $qty_all=$qty_all+1;
                           $bill_template_3.=$bill_template_4;
                   }}
               $bill_template_5=$bill_template_1[0].$bill_template_3.$bill_template_2[1]; // собрали все
               $bill_template_5 = strtr($bill_template_5, array(
                                "{N_LAST}"=>$n, "{N_ALL}"=>$n, "{PRD_QUANTITY_TOTAL}"=>($qty_all), "{N_ALL_PROPIS}"=>written_number($n)));

               $bill_ret[$bt]=$bill_template_5;

               }
         return $bill_ret;
         }

    // показываем счета и кваитанции
    function show_bill($b) { // показать счет
         Debug::log();
         $b=textprocess($b,'sql');
         $b2=mysql_kall("SELECT bill_content FROM ".DB_PREFIX."orders_bills WHERE bill_link='".$b."'");
         if(mysql_num_rows($b2)>0) {
         mysql_kall("UPDATE ".DB_PREFIX."orders_bills SET views=views+1 WHERE bill_link='".$b."'");
         $b3=mysql_fetch_assoc($b2);
         return $b3['bill_content'];
            }
         }

    // рассылка уведомлений по новому заказу! (или вообще)
    function mail2all($templ, $m, $email_arr) {
        Debug::log();
         $catsup=$m['catsup'];
         $email_arr[]=$catsup['admin_email'];
         $m['{STORE_NAME}']=$catsup['nazv'];
         $m['{OID_LINK}']=$catsup['remote_addr']."/user/orders/".$m['oidhash'];
         if($m['bills']!="") {
         $m['{BILLS}']="Ваша квитанция/счет/ссылка для оплаты находится по этому адресу: ".$catsup['remote_addr']."/user/bills/".$m['bills']; } else {
         $m['{BILLS}']=""; }

         $mail_template = strtr($templ, $m);
         $mt=explode("###",$mail_template); $mt1=$mt[0]; unset($mt[0]); $mt2=implode("###",$mt);

         $email_arr=array_unique($email_arr);
         foreach($email_arr as $k=>$v) {
            if($v==EMAIL_ADMIN) { $maintxt=$mt2; } else { $maintxt=$mt1.$mt2; }

$txt="From: ".EMAIL_ADMIN. "
To: ".$v."
Subject: ".$catsup['nazv'].": Заказ №".$m['{OID}']." (".$m['{REG_ORDER_STATUS_NAME}'].")
Content-type: text/html; charset=windows-1251

".nl2br($maintxt);
            $mail = mailenc($txt);
            mailx($mail);
          }
         return $catsup;
         }

    // создаем картинку с адресом!
    function ymaps($a,$oidnum,$oidhash) {
        Debug::log();
        $ll_lookup="http://geocode-maps.yandex.ru/1.x/?geocode=".urlencode($a)."&key=".YAPIKEY;
        $ll2=@file_get_contents($ll_lookup);
        if($ll2!="") {
        $ll4=explode("<pos>",$ll2);
        $ll5=explode("</pos>",$ll4[1]);
        $ll_show="http://static-maps.yandex.ru/1.x/?ll=".strtr($ll5[0],array(" "=>","))."&size=450,450&z=13&l=map&pt=".strtr($ll5[0],array(" "=>",")).",pmwtm".$oidnum."&key=".YAPIKEY;
        @copy($ll_show,MAINURL_5."/upload/ymaps/".$oidhash.".jpg");
        }}

    // показываем кэш заказа (страница заказа)
    function show_order($o) { // показать счет
        Debug::log();
    $o=textprocess($o,'sql');
    $o2=mysql_kall("SELECT order_cache, orders_id_real, status FROM ".DB_PREFIX."orders_status_history, ".DB_PREFIX."orders WHERE ".DB_PREFIX."orders_status_history.orders_nnn=".DB_PREFIX."orders.nnn AND
        ".DB_PREFIX."orders.oidhash='".$o."' ORDER BY ".DB_PREFIX."orders_status_history.nnn DESC LIMIT 1") or die(mysql_error());
    if(mysql_num_rows($o2)>0) {
         $o3=mysql_fetch_assoc($o2);
         $status_find=$this->status_finder("all");
         return array("oid"=>$o3['orders_id_real'],"cache"=>$o3['order_cache'],"status"=>$status_find[$o3['status']]['nazv']);
            }
         }

    // заглушка для добавления в старую бд, только для йогамагазина!
    function order2old($db, $mag, $p) {
Debug::log();
        $c=mysql_kall("SELECT customers_id FROM ".$db.".customers WHERE customers_email_address='".$p['email']."'");
        if(mysql_num_rows($c)>0) { $cid=mysql_result($c,'0','customers_id'); } else { $cid=1440; }

        if($p['reg_order']=="2") { $name_backup=$p['c_fio']." / "; $p['c_fio']="- заказ без регистрации -";
        $p['comments']="заказ без регистрации, контактные данные:<br>* телефон: ".$p['c_phone']."<br>* email: ".$p['email']."<br><br>".$p['comments'];
        }
        if($p['reg_order']=="3") { $name_backup=$p['c_fio']." / "; $p['c_fio']="- недооформленный заказ -";
        $p['comments']="недооформленный заказ, контактные данные:<br>* телефон: ".$p['c_phone']."<br>* email: ".$p['email']."<br><br>".$p['comments'];
        }

        $sql2="INSERT INTO ".$db.".orders (`customers_id`, `customers_name`, `customers_company`, `customers_street_address`, `customers_suburb`,
            `customers_city`, `customers_postcode`, `customers_state`, `customers_country`, `customers_telephone`, `customers_email_address`, `customers_address_format_id`,
            `delivery_name`, `delivery_company`, `delivery_street_address`, `delivery_suburb`, `delivery_city`, `delivery_postcode`, `delivery_state`, `delivery_country`,
            `delivery_address_format_id`, `billing_name`, `billing_company`, `billing_street_address`, `billing_suburb`, `billing_city`, `billing_postcode`, `billing_state`,
            `billing_country`, `billing_address_format_id`, `payment_method`, `cc_type`, `cc_owner`, `cc_number`, `cc_expires`, `last_modified`, `date_purchased`,
            `orders_status`, `orders_date_finished`, `currency`, `currency_value`, `metro`) VALUES ('".$cid."',
                '".$p['c_fio']."','".$p['c_company']."','".$p['c_address']."','".$p['c_address2']."','".$p['c_city']."','".$p['c_postcode']."',
                    '".$p['c_region']."','".$p['c_country']."','".$p['c_phone']."','".$p['email']."','1','".$p['d_fio']."','".$p['d_company']."',
                        '".$p['d_address']."','".$p['d_address2']."','".$p['d_city']."','".$p['d_postcode']."','".$p['d_region']."','".$p['d_country']."','1',
                            '".$p['b_fio']."','".$p['b_company']."','".$p['b_address']."','".$p['b_address2']."','".$p['b_city']."','".$p['b_postcode']."',
                                '".$p['b_region']."','".$p['b_country']."','1','".$p['payment_method']."','','','','','".time()."','".date('Y-m-d H:i:s',$p['purchased_dat'])."',
                                    '1','','RUB','1','".$p['metro']."')";

       mysql_kall($sql2) or die(mysql_error());

       $ins_id=mysql_insert_id();

       foreach($p['orders_products'] as $k=>$v) {
       $k2=mysql_kall("SELECT products_id FROM ".$db.".products_description WHERE products_name='".$v['nazv']."'"); // сомнительно но придется
       if(mysql_num_rows($k2)>0) { $k3=mysql_result($k2,0,'products_id'); } else { $k3=0; }
       $sql3="INSERT INTO ".$db.".orders_products (`orders_id`, `products_id`, `products_model`, `products_name`, `products_price`, `final_price`, `products_tax`,
           `products_quantity`) VALUES ('".$ins_id."','".$k3."','','".$v['nazv']."','".$v['price_one']."','".$v['price_one']."','0','".$v['qty']."')";
       mysql_kall($sql3) or die(mysql_error());
       mysql_kall("UPDATE ".$db.".products SET products_ordered=products_ordered+".$v['qty'].", products_quantity=products_quantity-".$v['qty']." WHERE products_id='".$k3."'");
        }


        $sql4="INSERT INTO ".$db.".orders_status_history (`orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`)
            VALUES ('".$ins_id."','1','".date('Y-m-d H:i:s',$p['purchased_dat'])."','1','! заказ с нового сайта !<br>".$p['comments']."')";
        mysql_kall($sql4) or die(mysql_error());

        $sql5="INSERT INTO ".$db.".orders_total (orders_id, title, text, value, class, sort_order) VALUES
            ('".$ins_id."','Стоимость товара:','<span class=currency_symbol></span>".number_format($p['prd_price'],2)."<span class=currency_symbol>р.</span>',
                '".$p['prd_price']."','ot_subtotal','1')";
        mysql_kall($sql5) or die(mysql_error());

        $sql6="INSERT INTO ".$db.".orders_total (orders_id, title, text, value, class, sort_order) VALUES
            ('".$ins_id."','".$p['delivery_method_1'].":','".$p['delivery_method_2']."','".$p['delivery_method_price']."','ot_shipping','2')";
        mysql_kall($sql6) or die(mysql_error());

        $sql7="INSERT INTO ".$db.".orders_total (orders_id, title, text, value, class, sort_order) VALUES
            ('".$ins_id."','Всего:','<b><span class=currency_symbol></span>".number_format($p['final_price'],2)."<span class=currency_symbol>р.</span></b>',
                '".$p['final_price']."','ot_total','4')";
        mysql_kall($sql7) or die(mysql_error());

// p['status_name'] - убрали
        $sql8="INSERT INTO u59221.all_orders (`mag`, `oid`, `dat`, `fio`, `city`, `status`, `price`, `client_type`, `payment_method`, `delivery_method`,
            `full_address`, `content`, `content_price`, `delivery_price`, `email`, `client_id`, `delivery_dat`, `delivery_dat_end`, `doubledouble`, `bill`,
            `bill_done`, `oid_cluster`, `oid_real`) VALUES ('".$mag."','".$ins_id."','".$p['purchased_dat']."','".$p['c_fio']."','".@$name_backup.$p['d_postcode']." ".$p['d_city']."',
                '1. Новый заказ','".$p['final_price']."','".$p['client_type']."','".$p['payment_method']."','".$p['delivery_method_1']."',
                    '".$p['d_full_address']."','".$p['content']."','".$p['prd_price']."','".$p['delivery_method_price']."','".$p['email']."','".$cid."','0',
                '0','0','".$p['billhash']."','0','4','".$p['oid_real']."')"; // cluster
        mysql_kall($sql8) or die(mysql_error());

        mysql_kall("UPDATE ".$db.".customers SET customers_orders=customers_orders+1 WHERE customers_id='".$cid."'");


        }

}

// функция для сумм прописью
    function written_number($i, $female=false) { Debug::log();
                  global $N0;
                  if ( ($i<0) || ($i>=1e9) || !is_int($i) ) {
                    return false; // Аргумент должен быть неотрицательным целым числом, не превышающим 1 миллион
                  }  if($i==0) { return $N0;  }
                  else {
                    return preg_replace( array('/s+/','/\s$/'),
                                         array(' ',''),
                                         num1e9($i, $female));
                    return num1e9($i, $female);
                  }}
    function num_125($n) { Debug::log();
                  /* форма склонения слова, существительное с числительным склоняется
                   одним из трех способов: 1 миллион, 2 миллиона, 5 миллионов */
                  $n100 = $n % 100;
                  $n10 = $n % 10;
                  if( ($n100 > 10) && ($n100 < 20) ) {
                    return 5;
                  }
                  elseif( $n10 == 1) {
                    return 1;
                  }
                  elseif( ($n10 >= 2) && ($n10 <= 4) ) {
                    return 2;
                  }
                  else {
                    return 5;
                  }
                }
    function num1e9($i, $female) { Debug::log();
                  global $Ne6;
                  if($i<1e6) {
                    return num1e6($i, $female);
                  }
                  else {
                    return num1000(intval($i/1e6), false) . ' ' .
                      $Ne6[num_125(intval($i/1e6))] . ' ' . num1e6($i%1e6, $female);
                  }
                }
    function num1e6($i, $female) { Debug::log();
                  global $Ne3;
                  if($i<1000) {
                    return num1000($i, $female);
                  }
                  else {
                    return num1000(intval($i/1000), true) . ' ' .
                      $Ne3[num_125(intval($i/1000))] . ' ' . num1000($i%1000, $female);
                  }
                }
    function num1000($i, $female) { Debug::log();
                  global $Ne2;
                  if( $i<100) {
                    return num100($i, $female);
                  }
                  else {
                    return $Ne2[intval($i/100)] . (($i%100)?(' '. num100($i%100, $female)):'');
                  }
                }
    function num100($i, $female) { Debug::log();
                  global $Ne0, $Ne1;
                  $gender = $female?1:0;
                  if ($i<20) {
                    return $Ne0[$gender][$i];
                  }
                  else {
                    return $Ne1[intval($i/10)] . (($i%10)?(' ' . $Ne0[$gender][$i%10]):'');
                  }
                }

    ///////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    if(DB_SERV!="localhost") {
        require_once("/home/u390764/koalalab.ru/www/_/functions/_shops.php");
        require_once("/home/u390764/koalalab.ru/www/_/functions/_class.zero.php"); 
    } else { 
        require_once("z:/home/test1.ru/www/shop-admin/functions/_shops.localhost.php");
        require_once("z:/home/test1.ru/www/shop-admin/functions/_class.zero.php");         
    }
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////