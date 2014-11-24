<?php 

/* user classes & functions */
/* always begin with __userfunc_ */

class __userfunc_klb {
    
    function requestpost($post) {
        Debug::log();
        
        if($post['email']==""&&$post['phone']=="") {
            $_SESSION['send_login_message']="Чтобы мы могли с вами связаться, укажите, пожалуйста, ваш телефон или электронный адрес.";
            return;
        }
                 
        $sql_ins = "INSERT
            INTO " . DB_PREFIX . "guestbook (mails_type, callme_type, to_whom, link_from, prd_from, order_from, customer_id_from, catshop_from,
                avtor, txt, dat, phone, phone_dat, email, hid, inside)
                        VALUES ('1','0','all','".$_SERVER['HTTP_REFERER']."','','','".@$_SESSION['customers_id']."',
                            '".SHOP_NNN."','".textprocess($post['avtor'],'sql')."','".textprocess($post['txt'],'sql')."',
                                '".time()."','".textprocess(@$post['phone'],'sql')."','','".textprocess(@$post['email'],'sql')."',
                                    '0','0')";       
        mysql_kall($sql_ins);
        
        $_SESSION['send_login_message']="Заявка отправлена. Мы свяжемся с вами в ближайшее время. Спасибо!";
        return;        
    }
    
    
}

?>