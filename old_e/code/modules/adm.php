<?
class adm {

    function login_relogin($login="",$sess_id="") { // релогин если не хватает данных
        Debug::log();
        if($login!=""&&$sess_id!="") {
            $checka=mysql_kall("SELECT email, dostup, shopwatch FROM ".DB_PREFIX."adm_users WHERE login='".$login."' AND sess_id='".$sess_id."'");
            if(mysql_num_rows($checka)>0) { 
                $checka2=mysql_fetch_assoc($checka);
                $_SESSION['adm_username']=$login;
                $_SESSION['adm_email']=$checka2['email'];
                $_SESSION['adm_dostup']=$checka2['dostup'];
                $_SESSION['adm_shopwatch']=$checka2['shopwatch'];                
                return 1;
               } else { return 0; }
            } else { return 0; }
        }

    function login_check($detected="login") { // 0 - не выполнен, 1 - выполнен
        Debug::log();
         $login_status=0; $detected_return="login";

         if((isset($_COOKIE['alog'])&&isset($_COOKIE['sess_id']))&&$detected!="logout") {
         $login_status=1; $detected_return=$detected;
         $activesess=readfromfile("adm_".$_COOKIE['alog']."_active_time", "0.15", "txt");
         if($activesess=="") { 
         $checka=mysql_kall("SELECT login FROM ".DB_PREFIX."adm_users WHERE login='".$_COOKIE['alog']."' AND sess_id='".$_COOKIE['sess_id']."'");
         if(mysql_num_rows($checka)>0) { 
         write2file(time(), "adm_".$_COOKIE['alog']."_active_time", "txt", "0.1");
         mysql_kall("UPDATE ".DB_PREFIX."adm_users SET lastactive='".time()."' WHERE login='".$_COOKIE['alog']."'");
         $_SESSION['lastactive']=time();
         } else { $login_status=0; $detected_return="login"; }}
         }

         if($login_status=="1"&&(!isset($_SESSION['adm_username'])||!isset($_SESSION['adm_email'])||!isset($_SESSION['adm_dostup'])||!isset($_SESSION['adm_shopwatch']))) {
             $result=$this->login_relogin($_COOKIE['alog'],$_COOKIE['sess_id']);
             if($result!="1") { $login_status=0; $detected_return="login"; }
             }
         
         return array('logstat'=>$login_status,'newdetect'=>$detected_return);
         }

/////////////////// LOGOUT
    function logout($post="") { // logout
         Debug::log();
        setcookie("alog","",time()-36000,"/",MAINURL_4);
        setcookie("sess_id","",time()-36000,"/",MAINURL_4);
        clearfile(array("adm_".$_COOKIE['alog']."_active_time"), "0", "txts");
        unset($_SESSION['adm_username'],$_SESSION['lastactive'], $_SESSION['adm_email'], $_SESSION['adm_dostup'],
                $_SESSION['adm_shopwatch']);
    } // LOGOUT

/////////////////////    // LOGIN
    function login($post="") { // login passwrd remember_me referer_url
        Debug::log();
       $log_succes=0;
       if(isset($post['remember_me'])) { $rememberme=time()+(60*60*24*14); } else { $rememberme=0; }
       if($post['login']!=""&&$post['passwrd']!="") {

       $checka=mysql_kall("SELECT * FROM ".DB_PREFIX."adm_users WHERE login='".textprocess($post['login'],'sql')."' AND pssw='".md5(textprocess($post['passwrd'],'sql'))."' AND ban!='1'");
       if(mysql_num_rows($checka)>0) {
         $checka2=mysql_fetch_assoc($checka);

         $sess_id="";
         $nabor=array("a","b","c","d","e","f","G","H","I","J","K","L","M",0,1,2,3,4,5,6,7,8,9);
         for($j=0;$j<10;$j++) { $sess_id=$sess_id.$nabor[rand(0,(count($nabor)-1))]; }
         $ips=explode(";",$checka2['ips'].";".@$_SERVER['REMOTE_ADDR']);
         $ips2=array_unique($ips); $ips=implode(";",$ips2);

         mysql_kall("UPDATE ".DB_PREFIX."adm_users SET sess_id='".$sess_id."', numlogs=numlogs+1, lastlog='".time()."', lastactive='".time()."', ips='".$ips."' WHERE nnn='".$checka2['nnn']."'");
         write2file(time(), "adm_".$post['login']."_active_time", "txt", "0.1");

         setcookie("alog",$post['login'],$rememberme,"/",MAINURL_4);
         setcookie("sess_id",$sess_id,$rememberme,"/",MAINURL_4);

         $_SESSION['adm_username']=$post['login'];
         $_SESSION['lastactive']=time();
         $_SESSION['adm_email']=$checka2['email'];
         $_SESSION['adm_dostup']=$checka2['dostup'];
         $_SESSION['adm_shopwatch']=$checka2['shopwatch'];

         $_SESSION['send_login_message']=""; $success=1;
         
           } else { $_SESSION['send_login_message']="Неверные логин и пароль."; }
       } else { $_SESSION['send_login_message']="Ошибка."; }
        
        $refer="http://".@$post['referer_host'].@$post['referer_url'];

        if(@$_SERVER['HTTP_REFERER']!="") { $refer=$_SERVER['HTTP_REFERER']; }

        if(@$success=="1") { $refer=strtr($refer,array("login"=>"catalog","logout"=>"catalog")); }

       return $refer;
       } // LOGIN

    // list users
    function list_users() {
           Debug::log();
           $a4=readfromfile("adm_listusers");
           if(@$a4=="") {
           $a=mysql_kall("SELECT login FROM ".DB_PREFIX."adm_users WHERE ban!='1' ORDER BY nnn ASC");
           $a2=mysql_fetch_assoc($a);
           do { $a3[$a2['login']]=$a2['login']; } while($a2=mysql_fetch_assoc($a));
           write2file(implode(";",$a3), "adm_listusers", "txt", "24");
           } else { $a3=explode(";",$a4); foreach($a3 as $a5=>$a6) { $a33[$a6]=$a6; } $a3=$a33; }
           return $a3;           
           }

////////////////////       // SHOW_MSGS
    function adm_msgs_show($user="", $limit=100) { // пользователь, количество показываемых сообщений
Debug::log();
           $r_ser=readfromfile("adm_msgs_".@$_COOKIE['alog']."","0.5");
           
           $last_msg=readfromfile("adm_refresh_msgs");
           $last_reload_time=readfromfile("adm_msgs_".@$_COOKIE['alog']."_loadtime");
           if(@$last_msg>=(@$last_reload_time)) { $flag_reload=1; }
           if($r_ser=="") { $flag_reload=1; }

           //$flag_reload=1;
           if(@$flag_reload=="1") {

           $listusers=$this->list_users();
           unset($listusers[$user]);
           $lu2=" `txt` NOT LIKE '%%%@".implode("%%%' AND `txt` NOT LIKE '%%%@",$listusers)."%%%' ";

           $user_tw="@".$user;
           $q=mysql_kall("SELECT * FROM ".DB_PREFIX."adm_msgs WHERE `from`='".$user_tw."' OR `txt` LIKE '%%%".$user_tw."%%%' OR (".$lu2.") ORDER BY dat DESC LIMIT ".$limit."") or die(mysql_error());
           if($user=="") {
           $q=mysql_kall("SELECT * FROM ".DB_PREFIX."adm_msgs ORDER BY dat DESC");
           }
           if(mysql_num_rows($q)>0) {
           $q2=mysql_fetch_assoc($q); $q5=array(); $q6=array();
           do { // nnn, dat, from, txt, haveread, receivers, email_notify


             $q2['txt']=preg_replace("/(@)((?:[a-z][a-z0-9_]*))/","<span class=usertw>\\1\\2</span>",$q2['txt']);

             if($q2['from']==$user_tw) { $mine_flag=1; } else { $mine_flag=0; }

             $upd_db=0;
             if($user!="") {
                 $q7=explode(":".$user_tw.":",$q2['receivers']);
                 if(count($q7)>1) { $q8=1; } else { $q8=0; } 
                 $q2['receivers']=strtr($q2['receivers'],array(":".$user_tw.":"=>"")); $upd_db=1; }
             if(trim($q2['receivers'])==""||trim($q2['receivers'])=="::") { $q2['haveread']="0"; $upd_db=1; }
             if($upd_db=="1") { mysql_kall("UPDATE ".DB_PREFIX."adm_msgs SET receivers='".$q2['receivers']."', haveread='".$q2['haveread']."' WHERE nnn='".$q2['nnn']."'");  }
             
             $q4['dat'][$q2['nnn']]=$q2['dat'];
             $q4['from'][$q2['nnn']]=$q2['from'];
             $q4['mine'][$q2['nnn']]=$mine_flag;
             $q4['txt'][$q2['nnn']]=$q2['txt'];

             $q3=explode($user_tw,$q2['txt']);
             if(count($q3)>1&&@$q8=="1") { $q5[$q2['nnn']]=$q2['nnn']; } else { $q6[$q2['nnn']]=$q2['nnn']; }
             if(count($q3)>1) { $q4['2me'][$q2['nnn']]="1"; } else { $q4['2me'][$q2['nnn']]="0"; }
           } while($q2=mysql_fetch_assoc($q));

           $q6=@array_merge(@$q5,@$q6);

           }

           $r=array("msgs_order"=>@$q6,"data"=>@$q4);
           write2file(serialize($r),"adm_msgs_".@$_COOKIE['alog']."","txt");
           write2file(time(),"adm_msgs_".@$_COOKIE['alog']."_loadtime","txt");

           } else { $r=unserialize($r_ser); }

           return $r;
           }
    // SHOW_MSGS

    // SHOW MSGS_DESIGNS
    function adm_msgs_show_format($msgs) { Debug::log();
            $msgs_bloks="";
            if(count($msgs['msgs_order'])>0) {
            foreach($msgs['msgs_order'] as $k1=>$k2) { $msgs_bloks.="<div ";
                if($msgs['data']['mine'][$k2]=="1") { $msgs_bloks.="class='adm_msg_mine' "; } else { 
                if($msgs['data']['2me'][$k2]=="1") { $msgs_bloks.="class='adm_msg_2me' "; } else { $msgs_bloks.="class='adm_msg_usual' "; } }
                $msgs_bloks.=">";
                if(date("d.m.Y",$msgs['data']['dat'][$k2])==date("d.m.Y",time())) { $tm=date("H:i",$msgs['data']['dat'][$k2]); } else { $tm=date("d.m.",$msgs['data']['dat'][$k2]); }
                $msgs_bloks.="<span class='adm_msg_from'>".$msgs['data']['from'][$k2]."</span> <span class='adm_msg_dat'>".$tm."</span><p></p>";
                
                $msgs_bloks.="<span ";
                if((time()-$msgs['data']['dat'][$k2])<=120) { $msgs_bloks.="class='adm_msg_txt_new'"; }
                $msgs_bloks.=">".$msgs['data']['txt'][$k2]."<p></p></span>";
                
                $msgs_bloks.="</div>";
                }   }
            return $msgs_bloks;
            }
    //

/////////////////////       // POST_MSGS
    function adm_msgs_post($post="") { // msgtxt, em_notify
           Debug::log();
           $post['msgtxt']=iconv("UTF-8","Windows-1251",$post['msgtxt']);
           $txt1=explode(" ",$post['msgtxt']); $receiver=array(); $r_str="";

           foreach($txt1 as $t1=>$t2) {
               if(substr($t2,0,1)=="@") { $receiver[substr(trim($t2),1)]=trim($t2); $r_str.=":".trim($t2).":"; }
               }

           if(count($receiver)>0) {
               foreach($receiver as $r1=>$r2) {  
                   if(@$post['em_notify']=="1") {
                   $r3=@mysql_result(mysql_kall("SELECT email FROM ".DB_PREFIX."adm_users WHERE login='".$r1."'"),0,'email');
                 
// notification email for message
$txt2="From: ".@$_SESSION['adm_email']. "
To: ".$r3."
Subject: Вам оставлено сообщение в системе управления магазинами (".SHOP_NAME.")
Content-type: text/html; charset=windows-1251

В панеле администрирования магазинов, где вы присутствуете под именем <b>".$r1."</b>, вам было оставлено сообщение:<br><br>".@$_COOKIE['alog'].": ".$post['msgtxt']."";
$mail = mailenc($txt2);
mailx($mail);
//
                     }}}

           mysql_kall("INSERT INTO ".DB_PREFIX."adm_msgs (`from`, `txt`, `dat`, `haveread`, `receivers`, `email_notify`)
               VALUES ('@".@$_COOKIE['alog']."',
                       '".strtr($post['msgtxt'],array("'"=>""))."',
                       '".time()."',
                       '1',
                       '".$r_str."',
                       '".$post['em_notify']."')") or die(mysql_error());
           write2file(time(),"adm_refresh_msgs");
           } // POST_MSGS

///////////////////////// COLLECT SHOPS
    function collect_shops($nnn="") { Debug::log();
                   $products=new products;
                   if($nnn=="") {
                   $a=mysql_kall("SELECT * FROM ".DB_PREFIX."catshop_config WHERE type='shop' ORDER BY sort_cat ASC");
                   } else {
                      $a=mysql_kall("SELECT * FROM ".DB_PREFIX."catshop_config WHERE type='shop' AND nnn='".$nnn."' ORDER BY sort_cat ASC");
                       }
                   $a2=mysql_fetch_assoc($a);
                   do {
                       $a3=cats('sql', $a2['nnn']); $a4=explode("IN ('",$a3); $a5=explode("', '",$a4[1]);
                       $a6=count($a5)+1;
                       $prds_nums=$products->collect_products($a2['nnn'], 'cat', 0, 1);

                       foreach($a2 as $k=>$k2) {  $s[$k][$a2['nnn']]=$k2; }
                       $s['nums_cats'][$a2['nnn']]=$a6;
                       $s['nums_prds'][$a2['nnn']]=$prds_nums;
                       $s['parent_flip'][$a2['parent']][$a2['nnn']]=$a2['nnn'];
                   } while ($a2=mysql_fetch_assoc($a));
                   return $s;
               }

    // SHOP STATUSES
    function adm_shops_statuses($b="") { // типы доступных возможных статусов магазина определяются прямо здесь (реализовано ли это в движке?)
                // 1,2,3,4,5 - отношения с другими магазинами
            Debug::log();    
                $a=array("1"=>"<span class='adm_status_warn'><b>внешний:</b> xml или текстовой файл. страницы товара не существует, но товары отображаются в разделах. внешние ссылки. в корзину добавить <b>нельзя.</b></span>",
                         "2"=>"<span class='adm_status_warn'><b>внешний:</b> xml или текстовой файл. страницы товара не существует, но товары отображаются в разделах. внешние ссылки. при добавлении в корзину <b>перенаправляем на сайт магазина.</b></span>",
                         "3"=>"<span class='adm_status_warn'><b>внешний:</b> данные на сервере. страница товара существует. локальные ссылки. товары добавляются в <b>отдельную корзину.</b> при оформлении <b>переход на сайт магазина.</b></span>",
                         "4"=>"<span class='adm_status_warn'><b>внутренний:</b> формируется <b>отдельный заказ</b> (в <b>отдельную корзину</b>)</span>",
                         "5"=>"<b>стандартный внутренний:</b> заказ формируется в общей корзине. ");
                $a_onoff=array("","0","0","0","1","1");
                if($b==""||$b=="0") {
                return array("descr"=>$a,"onoff"=>$a_onoff); } else {
                    return $a[$b];
                    }
                }

    function show_shops_tree($s,$parent=0,$level=4) { 
                Debug::log();
                $str="";
                if(count($s['parent_flip'][$parent])>0) {
                foreach($s['parent_flip'][$parent] as $k1=>$k2) { 
                if($parent!="0") {
                    if($level<=0) { $level_css=1; } else { $level_css=$level; }
                    $str.="<div class='shop_row_sub".$level_css."'>— "; } else { $str.="<div class='shop_row_parent0'>"; }
                $str.="<a href=".$s['remote_addr'][$k1]." class='adm_shop_nazv'>".$s['nazv'][$k1]."</a>
                    <span class='adm_shop_info'>".$s['nums_cats'][$k1]." разд., ".$s['nums_prds'][$k1]." тов.</span> <a href={ADM_SHOP_LINK}/edit".$k1.">{EDIT_ICON}</a> <br/><table class='adm_shop_descr'><tr><td>";
                if($s['descr'][$k1]!="") { $str.="".txt_cut($s['descr'][$k1])."<p></p>"; }
                $str.="коэфф./курс валюты — <b>".$s['currency'][$k1]."</b><p></p>способ отображения в родительских магазинах:<br/>&nbsp;&nbsp;— ";
                if($s['remote_always'][$k1]=="1") { $str.="в списках отображать как <span class='adm_status_warn'><u>внешнюю ссылку</u></span>"; } else { $str.="показывать также как раздел"; }
                $str.="<br>&nbsp;&nbsp;— <span class=adm_status_number>тип ".$s['status'][$k1]."</span>: ";
                $str.=$this->adm_shops_statuses($s['status'][$k1])."<p></p>";
                $str.="e-mail администратора — ".$s['admin_email'][$k1].""."</td></tr></table>";
                $str.=$this->show_shops_tree($s,$parent=$k1,($level-1));
                $str.="</div>";
                    }}
                    return $str;
                }

    function context_menu($url) {
               Debug::log();
               $u=array("shops"=>
                            "<a href={MAINURL_ADM}/shops/create>Создать или подключить новый магазин</a><br/><br/>                             
                             Для удаления магазина обратитесь к администратору
                             <br/><br/><br/><br/><b>Где добавить новые модули оплаты, доставки, чтобы впоследствиии их можно было подключить к магазинам?</b><p></p>— <a href=\"{MAINURL_ADM}/orientir\">Ориентир</a> (общие настройки движка, информ., статистика)",
                        "shops/edit"=>
                            "<div style='float:left;font-weight:bolder;font-size:2em;margin:-1px 5px 1px 0;'>!</div> Редактирование расширенных настроек требует особого внимания<div style='clear:both;'></div>
                            <br/><br/><br/><b>Быстрые ссылки</b><br/><br/><div class=adm_quick_lnks><a href=#conf_SHOP_KWORDS>Ключевые слова магазина</a><p></p>
                            <a href=#conf_SHOP_DESCR>Описание магазина</a><p></p><br/>
                            <a href=#conf_TEMPLATE>Дизайн магазина (шаблон)</a><p></p><br/>
                            <a href=#conf_EMAIL_TXT_REGISTER>Шаблон письма при успешной регистрации</a><p></p>
                            <a href=#conf_EMAIL_TXT_PASSW_RESTORE>Шаблон письма о восстановлении пароля</a><p></p><br/>
                            <a href=#conf_EMAIL_TXT_NEWORDER>Шаблон письма нового заказа</a><p></p>
                            <a href=#conf_PAGE_NEW_ORDER_TXT>Шаблон страницы нового заказа</a><p></p>
                            <a href=#conf_PAGE_OLD_ORDER_TXT>Шаблон страницы старого заказа</a><p></p><br/>
                            <a href=#conf_CUSTOMERS_DEFAULT_LISTS>Листы клиента</a><p></p><br/>
                            <a href=#conf_EXCLUDE_ATTRS>Исключенные свойства товара</a><p></p>
                            <a href=#conf_ATTR_RAND>Свойства для случайного товара</a><p></p><br/>
                            <a href=#conf_BANNERS>Баннеры (номера pages)</a><p></p><br/>
                            <a href=#conf_GROUP_MAIN_FLAG>Группировка товаров на главной</a><p></p><br/>
                            <a href=#conf_CONTACT_THEMES>Темы к письмам в отдел контактов</a><p></p><br/>
                            <a href=#conf_QTY_NAMES>Обозначение количества оставшегося товара</a><p></p><br/>
                            <a href=#conf_ORDER_DELIVERY>Включенные <b>модули доставки</b></a><p></p>
                            <a href=#conf_ORDER_PAYMENT>Включенные <b>модули оплаты</b></a><p></p>
                            Все доступные модули находятся здесь — <a href={MAINURL_ADM}/shops/orientir>ориентир</a><p></p><br/>
                            <a href=#conf_ORDER_STATUS_DB>Статусы заказов какого магазина (№) берутся за основу (или указать текущий)</a><p></p><br/>
                            <a href=#conf_ORDER_CLUSTER>Кластер заказов</a><p></p></div>
                            ");
               return $u[$url];
               }

    function collect_shop_configuration($nnn) {
              Debug::log();
               $a=mysql_kall("SELECT nnn, conf_key, conf_val, descr FROM ".DB_PREFIX."configuration WHERE shop_cat='".$nnn."' ORDER BY nnn ASC");
               if(mysql_num_rows($a)>0) {
               $a2=mysql_fetch_assoc($a);
               do { foreach($a2 as $a3=>$a4) { $a5[$a2['conf_key']][$a3]=$a4; }} while ($a2=mysql_fetch_assoc($a));
               return $a5;
               }
              }

    function collect_shop_o_b_statuses($nnn,$status_source) {
              Debug::log();
                if($status_source['conf_val']!=$nnn) { return 0; } else {
                $a=mysql_kall("SELECT * FROM ".DB_PREFIX."orders_status WHERE catshop='".$nnn."' ORDER BY sort ASC");

                if(mysql_num_rows($a)<=0) {
                    mysql_kall("INSERT INTO ".DB_PREFIX."orders_status (catshop) VALUES ('".$nnn."')");
                    $a=mysql_kall("SELECT * FROM ".DB_PREFIX."orders_status WHERE catshop='".$nnn."' ORDER BY sort ASC");
                    }

                $a2=mysql_fetch_assoc($a);
                do { foreach($a2 as $a3=>$a4) { $a5[$a2['status_id']][$a3]=$a4; 
                if(!isset($a5_new['new_status_id'][$a3])) { $a5_new['new_status_id'][$a3]=""; }}} while ($a2=mysql_fetch_assoc($a));
                $a=mysql_kall("SELECT * FROM ".DB_PREFIX."orders_status_bills WHERE catshop='".$nnn."' ORDER BY sort ASC");

                if(mysql_num_rows($a)<=0) {
                    mysql_kall("INSERT INTO ".DB_PREFIX."orders_status_bills (catshop) VALUES ('".$nnn."')");
                    $a=mysql_kall("SELECT * FROM ".DB_PREFIX."orders_status_bills WHERE catshop='".$nnn."' ORDER BY sort ASC");
                    }

                $a2=mysql_fetch_assoc($a);
                do { foreach($a2 as $a3=>$a4) { $a6[$a2['status_id']][$a3]=$a4; 
                if(!isset($a6_new['new_status_id'][$a3])) { $a6_new['new_status_id'][$a3]=""; }}} while ($a2=mysql_fetch_assoc($a));
                return array("o"=>$a5,"b"=>$a6,"o_new"=>$a5_new,"b_new"=>$a6_new);
              }}

    function adm_editshop_post($post) {
              Debug::log();
              foreach($post as $k=>$k2) { $post[$k]=iconv("UTF-8","Windows-1251",$post[$k]); }
              if(@$post['remote_always']=="on") { $rem=1; } else { $rem=0; }
              mysql_kall("UPDATE ".DB_PREFIX."catshop_config SET nazv='".$post['nazv']."', remote_addr='".$post['remote_addr']."', 
                  descr='".textprocess($post['descr'], 'sql')."', currency='".$post['currency']."', parent='".substr($post['parent'],1)."',
                      status='".substr($post['status'],1)."', remote_always='".$rem."', admin_email='".$post['admin_email']."'
                        WHERE nnn='".$post['shop2edit']."' AND type='shop'");
              }

    function adm_editshop_post_stats($post) {
               Debug::log();
              $db=array("b"=>"orders_status_bills", "o"=>"orders_status");
              $db_keys=array("b"=>"(status_name, sort, catshop, flag_first, flag_finish)",
                             "o"=>"(status_name, sort, catshop, status_color, flag_first, flag_quick, flag_error, flag_finish)");
                 
           foreach($post['status_name'] as $k=>$k2) { $post['status_name'][$k]=iconv("UTF-8","Windows-1251",$post['status_name'][$k]); }
           
           $c=mysql_kall("SELECT status_id FROM ".DB_PREFIX.$db[$post['a_editshop_stats_hid']]." WHERE catshop='".$post['shop2edit']."' LIMIT 1") or die(mysql_error());
           if(mysql_num_rows($c)>0) { // update
               $flag_upd="1"; } else { $flag_upd=""; }

           foreach($post['status_id'] as $k=>$v) {
               if(isset($post['flag_first'][$k])) { $ff=1; } else { $ff=0; }
               if(isset($post['flag_quick'][$k])) { $fq=1; } else { $fq=0; }
               if(isset($post['flag_error'][$k])) { $fe=1; } else { $fe=0; }
               if(isset($post['flag_finish'][$k])) { $fsh=1; } else { $fsh=0; }
               if($k==="new_status_id"||$flag_upd!="1") { if($post['status_name'][$k]!="") {
                 $sql="INSERT INTO ".DB_PREFIX.$db[$post['a_editshop_stats_hid']]." ".$db_keys[$post['a_editshop_stats_hid']]." VALUES
                       ('".textprocess($post['status_name'][$k],'sql')."','".$post['sort'][$k]."','".$post['shop2edit']."',";
                 if($post['a_editshop_stats_hid']=="o") { $sql.="'".$post['status_color'][$k]."',"; }
                 $sql.="'".$ff."',";
                 if($post['a_editshop_stats_hid']=="o") { $sql.="'".$fq."','".$fe."',"; }
                 $sql.="'".$fsh."')";
                 mysql_kall($sql) or die(mysql_error());                 
                       }} else {
               $sql="UPDATE ".DB_PREFIX.$db[$post['a_editshop_stats_hid']]." SET
                   status_name='".textprocess($post['status_name'][$k],'sql')."', sort='".$post['sort'][$k]."', ";
              if($post['a_editshop_stats_hid']=="o") {  $sql.="status_color='".$post['status_color'][$k]."', "; }
               $sql.="flag_first='".$ff."', ";
              if($post['a_editshop_stats_hid']=="o") {  $sql.="flag_quick='".$fq."', flag_error='".$fe."', "; }
               $sql.="flag_finish='".$fsh."' WHERE status_id='".$v."' AND catshop='".$post['shop2edit']."'";
               mysql_kall($sql) or die(mysql_error());
               }}

             if(count($post['del'])>0) {
           foreach($post['del'] as $k=>$v) { if($post['status_id'][$k]!="") {
               mysql_kall("DELETE FROM ".DB_PREFIX.$db[$post['a_editshop_stats_hid']]." WHERE status_id='".$post['status_id'][$k]."' AND catshop='".$post['shop2edit']."'"); }}}
             if($post['a_editshop_stats_hid']=="o") { return 2; } else { return 3; } 
              }
              
    function adm_editshop_post_conf ($post) { //a_editshop_conf_hid, conf_nnn_1
                  Debug::log();
                $id=$post['a_editshop_conf_hid'];
                $k="conf_nnn_".$id;
                 //$post[$k]=iconv("UTF-8","Windows-1251",$post[$k]); @reviewlate
                 $post[$k]=strtr($post[$k],array("[#>]"=>"{","[<#]"=>"}")); 
                 mysql_kall("UPDATE ".DB_PREFIX."configuration SET conf_val='".$post[$k]."' WHERE nnn='".$id."'") or die(mysql_error());
                 return $id;
              }
              
    }
?>