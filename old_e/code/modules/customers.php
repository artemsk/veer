<?php

class customers {

    //put your code here

    function add_customer($post) {
        Debug::log();
        // TODO: ошибки при регистрации
        $flag_stop = 0;
        if ($post['pssw'] != $post['pssw2'] || $post['pssw'] == "") {
            $regerror[] = "Неверный пароль";
            $flag_stop = 1;
        } // ошибки
        if (trim($post['email']) == "") {
            $regerror[] = "Не указан e-mail";
            $flag_stop = 1;
        }
        if (trim($post['firstname']) == "" || trim($post['lastname']) == "") {
            $regerror[] = "Необходимо указать имя и фамилию";
            $flag_stop = 1;
        }
        $already_registered = mysql_call("SELECT customers_id FROM " . DB_PREFIX . "customers WHERE email='" . textprocess($post['email'], 'sql') . "'") or die(mysql_error());
        if (mysql_num_rows($already_registered) > 0) {
            $regerror[] = "Указанный e-mail уже зарегистрирован";
            $flag_stop = 1;
        }

        if (trim($_SESSION['number1']) == "" || trim($post['spcod']) == "") {
            $regerror[] = "Нет проверочного кода";
            $flag_stop = 1;
        }
        if (trim($_SESSION['number1']) != "" && trim($post['spcod']) != "") {
            if ($_SESSION['number1'] != mb_strtolower($post['spcod'])) {
                $regerror[] = "Проверочный код указан неверно";
                $flag_stop = 1;
            }
        }

        if ($flag_stop != "1") {

            if ($post['yur_status'] == "on") {
                $post['yur_status'] = "1";
            } else {
                $post['yur_status'] = "0";
            }
            if ($post['newsletter_sign'] == "on") {
                $post['newsletter_sign'] = "1";
            } else {
                $post['newsletter_sign'] = "0";
            }
            if ($post['newsletter_prd_sign'] == "on") {
                $post['newsletter_prd_sign'] = "1";
            } else {
                $post['newsletter_prd_sign'] = "0";
            }
            if ($post['gender'] == "") {
                $post['gender'] = "0";
            }

            $post['customers_type'] = "user"; // оптовиком можно стать только чз админку!
            $post['distr_promo_id'] = 0;
            $post['guest_from_id'] = 0;
            if ($post['promokod'] != "") { // promokod
                $checkpromo = mysql_call("SELECT nnn,connected_id FROM " . DB_PREFIX . "customers_promo WHERE promokod='" . $post['promokod'] . "' AND catshop='" . $post['shop_cat'] . "' AND status='1'") or die(mysql_error());
                if (mysql_num_rows($checkpromo) > 0) {
                    $checkpromo2 = mysql_fetch_assoc($checkpromo);
                    if ($checkpromo2['connected_id'] > 0) {
                        $post['guest_from_id'] = $checkpromo2['connected_id'];
                        $post['customers_type'] = "guest";
                    } else {
                        $post['guest_from_id'] = "0";
                        $post['distr_promo_id'] = $checkpromo2['nnn'];
                        $post['customers_type'] = "distr";
                    }
                    mysql_kall("UPDATE " . DB_PREFIX . "customers_promo SET status='0' WHERE nnn='" . $checkpromo2['nnn'] . "'");
                }
            }

            $sql_ins = "INSERT INTO " . DB_PREFIX . "customers (orig_catshop, allow_everywhere, email,
        pssw, customers_type, yur_status, gender, firstname, lastname, dob, telephone, regdate, lastlogon, num_logons, num_orders,
        newsletter_sign, newsletter_prd_sign, distr_promo_id, guest_from_id, personal_discount_on, note,
        allow_orders, hid) VALUES ('" . $post['shop_cat'] . "','" . REGUSER_ALLOW_EVERYWHERE . "','" . $post['email'] . "','" . $post['pssw'] . "','" . $post['customers_type'] . "',
            '" . $post['yur_status'] . "','" . $post['gender'] . "','" . $post['firstname'] . "','" . $post['lastname'] . "','" . $post['dob'] . "',
                '" . $post['telephone'] . "','" . time() . "', '" . time() . "', '0', '0',
        '" . $post['newsletter_sign'] . "', '" . $post['newsletter_prd_sign'] . "', '" . $post['distr_promo_id'] . "', '" . $post['guest_from_id'] . "', 
            '0', '" . $post['note'] . "', '" . REGUSER_ALLOW_ORDERS . "', '" . REGUSER_HID . "')";

            mysql_call($sql_ins) or die(mysql_error());
            $ins_id = mysql_insert_id();

            if ($post['yur_status'] != "1") {
                $sql_ins2 = "INSERT INTO " . DB_PREFIX . "customers_address_book (company, fio, street_address_1, street_address_2,
        city, postcode, country, region, metro, default_shipping, default_billing, customers_id)
        VALUES ('" . $post['company'] . "','" . $post['firstname'] . " " . $post['lastname'] . "','" . $post['street_address_1'] . "',
            '" . $post['street_address_2'] . "','" . $post['city'] . "','" . $post['postcode'] . "','" . $post['country'] . "',
                '" . $post['region'] . "','" . $post['metro'] . "','1','1','" . $ins_id . "')";
                mysql_call($sql_ins2) or die(mysql_error());
            }

            if ($post['yur_status'] == "1") { // yur_status
                $sql_ins3 = "INSERT INTO " . DB_PREFIX . "customers_yur_book (company, inn, schet, bank, korr, bik,
        postcode, city, address, region, customers_id) VALUES ('" . $post['company'] . "','" . $post['inn'] . "',
            '" . $post['schet'] . "','" . $post['bank'] . "','" . $post['korr'] . "','" . $post['bik'] . "','" . $post['postcode2'] . "',
                '" . $post['city2'] . "','" . $post['address'] . "','" . $post['region2'] . "','" . $ins_id . "')";
                mysql_call($sql_ins3) or die(mysql_error());
            }

            if ($post['coupon'] != "") { // coupon
                $checkcoupon = mysql_call("SELECT * FROM " . DB_PREFIX . "customers_specials WHERE catshop='" . $post['shop_cat'] . "' AND coupon='" . $post['coupon'] . "' AND status='1' AND expires>" . time() . " AND used_by_customer_id='0'") or die(mysql_error());
                if (mysql_num_rows($checkcoupon) > 0) {
                    mysql_call("UPDATE " . DB_PREFIX . "customers_specials SET used_by_customer_id='" . $ins_id . "' WHERE catshop='" . $post['shop_cat'] . "' AND coupon='" . $post['coupon'] . "' AND status='1' AND expires>'" . time() . "' AND used_by_customer_id='0'");
                }
            }
            //$sql_ins4="INSERT INTO ".DB_PREFIX."customers_specials 'catshop', 'coupon', 'discount', 'status', 'expires', 'used_by_customer_id'";
// success email
            $txt = "From: " . EMAIL_ADMIN . "
To: " . textprocess($post['email'], 'sql') . "
Subject: " . SHOP_NAME . ": Регистрация клиента
Content-type: text/html; charset=windows-1251

" . nl2br(EMAIL_TXT_REGISTER);
            $mail = mailenc($txt);
            mailx($mail);
//
        } else {
            foreach ($post as $pt => $pt2) {
                $_SESSION['register_form_' . $pt] = $pt2;
            }
        }

        if (is_array($regerror)) {
            $mess = "Не удалось зарегистрироваться: ";
            foreach ($regerror as $re => $re2) {
                $mess.=$re2 . "; ";
            }
            $_SESSION['send_login_message'] = $mess;
        } else {
            $_SESSION['send_login_message'] = "Регистрация прошла успешно.";
        }

        return @$ins_id;
    }

    //////////////////// ADD_CUSTOMER
    //// CБОР ДАННЫХ КЛИЕНТА
    function gather_data($c_id, $c_dbs = array("customers", "customers_address_book", "customers_basket_lists", "customers_yur_book"), $c_dbs_k = array("customers_id", "customers_id", "customers_id", "customers_id")) {  /// gather: id клиента, какие бд и какие идентификаторы
        Debug::log();
        foreach ($c_dbs as $k => $v) {
            $customers_data = mysql_kall("SELECT * FROM " . DB_PREFIX . $v . " WHERE " . $c_dbs_k[$k] . "='" . $c_id . "'");
            $c_data = mysql_fetch_assoc($customers_data);
            $kkk = 0;
            if (mysql_num_rows($customers_data) > 0) {
                do {
                    if (count($c_data) > 0) {
                        foreach ($c_data as $kk => $vv) {
                            $customers_edit[$v][$kkk][$kk] = $vv;
                        }
                    }
                    $kkk++;
                } while ($c_data = mysql_fetch_assoc($customers_data));
            }
        }
        return $customers_edit;
    }

    /////////////////////

    function edit_customer($post, $sent_checkboxes = array("customers#yur_status" => "", "customers#newsletter_sign" => "", "customers#newsletter_prd_sign" => "")) {
        //// dbname#dbfield - формат key; зафиксировать какие чекбоксы были посланы
        Debug::log();
        if (@$post['c_id'] > 0) {

            // checkbox array!!! (сюда добавлять поля из бд, которые 1/0 (checkbox они же)
            $checkbox_fields = array("customers#allow_everywhere" => "", "customers#yur_status" => "", "customers#newsletter_sign" => "", "customers#newsletter_prd_sign" => "",
                "customers#allow_orders" => "", "customers#hid" => "", "customers_promo#status" => "", "customers_specials#status" => ""); // все чекбоксы
            ////

            foreach ($post as $k => $v) {
                $k2 = explode("#", $k);
                if (count($k2) > 1) {

                    // доп проверки
                    if ($v == "on") {
                        $v = "1";
                    }
                    if ($k2[0] == "customers_promo" && $k2[1] == "promokod" && $v == "") {
                        continue;
                    } // временная заплатка
                    if ($k2[0] == "customers_specials" && $k2[1] == "coupon" && $v == "") {
                        continue;
                    } // временная заплатка
                    if ($k2[0] == "customers" && $k2[1] == "pssw") {
                        if ($post['customers#pssw'] != $post['customers#pssw2']) {
                            continue;
                        }
                    }
                    if ($k2[0] == "customers" && $k2[1] == "pssw2") {
                        continue;
                    }
                    if (isset($checkbox_fields[$k])) {
                        unset($checkbox_fields[$k]);
                        unset($sent_checkboxes[$k]);
                    }
                    //

                    $k3[$k2[0]] = @$k3[$k2[0]] . $k2[1] . "='" . $v . "', ";
                } else {
                    continue;
                }
            } // пропускаем ненужные поля

            if (count($sent_checkboxes) > 0) {
                foreach ($sent_checkboxes as $k => $v) {
                    $k2 = explode("#", $k);
                    mysql_call("UPDATE " . DB_PREFIX . $k2[0] . " SET " . $k2[1] . "='0'"); // обновляем только те чекбоксы, которые были посланы и не unset
                }
            }

            if (count(@$k3) > 0) {
                foreach ($k3 as $k => $v) {
                    $k4 = "UPDATE " . DB_PREFIX . $k . " SET " . substr($v, 0, -2) . " WHERE customers_id='" . $post['c_id'] . "'";
                    //// TODO: сделать вариации для некоторых баз
                    mysql_call($k4) or die(mysql_error());
                }
            }
        }
    }

    //////// EDIT CUSTOMER

    function del_customer($c_id, $type = "client") {
        Debug::log();
        //// type=client,order,mail,all; сначала скрываем. удаляем только тех клиентов, которые скрыты (т.е. чтобы удалить, надо дважды нажать)
        //// из search_history не будет удалять
        $check = mysql_kall("SELECT hid FROM " . DB_PREFIX . "customers WHERE customers_id='" . $c_id . "' AND hid='1'");
        if (mysql_num_rows($check) > 0) {

            if ($type == "client" || $type == "all") {
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_address_book WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_basket_attr WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_basket_lists WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_lastvisits WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_promo WHERE connected_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_share WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_specials WHERE used_by_customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_yur_book WHERE customers_id='" . $c_id . "'");
            }

            if ($type == "mail" || $type == "all") {
                mysql_kall("DELETE FROM " . DB_PREFIX . "guestbook WHERE customer_id_from='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "pages_comments WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "products_reviews WHERE customers_id='" . $c_id . "'");
            }

            if ($type == "order" || $type == "all") {
                mysql_kall("DELETE FROM " . DB_PREFIX . "customers_scores WHERE customers_id='" . $c_id . "'");
                mysql_kall("DELETE FROM " . DB_PREFIX . "orders, " . DB_PREFIX . "orders_bills, " . DB_PREFIX . "orders_products, " . DB_PREFIX . "orders_products_attr,
               " . DB_PREFIX . "orders_status_history WHERE " . DB_PREFIX . "orders.customers_id='" . $c_id . "' AND " . DB_PREFIX . "orders.nnn=" . DB_PREFIX . "orders_bills.orders_nnn AND
                   " . DB_PREFIX . "orders.nnn=" . DB_PREFIX . "orders_products.orders_nnn AND " . DB_PREFIX . "orders.nnn=" . DB_PREFIX . "orders_products_attr.orders_nnn AND
                       " . DB_PREFIX . "orders.nnn=" . DB_PREFIX . "orders_status_history.orders_nnn"); // TODO: проверить работоспособность
            }
        } else {
            mysql_kall("UPDATE " . DB_PREFIX . "customers SET hid='1' WHERE customers_id='" . $c_id . "'");
        }
    }

    ////// DEL CUSTOMER

    function login_temporary($l, $p) { // TODO: временная заглушка пока мы не избавимся от всех md5
        Debug::log();
        $data_arr = "";
        $login_success = 0;
        $checkuser = mysql_call("SELECT * FROM " . DB_PREFIX . "customers WHERE email='" . textprocess($l, 'sql') . "'");
        if (mysql_num_rows($checkuser) > 0) {
            $checkuser2 = mysql_fetch_assoc($checkuser);
            if ($checkuser2['md5pssw'] != "") {
                $stack = explode(':', $checkuser2['md5pssw']);
                if (md5($stack[1] . textprocess($p, 'sql')) == $stack[0]) {
                    $login_success = 1;
                    $data_arr = $checkuser2;
                }
            } else {
                if ($checkuser2['pssw'] == textprocess($p, 'sql')) {
                    $login_success = 2;
                    $data_arr = $checkuser2;
                }
            }
        }
        return array("ls" => $login_success, "d" => $data_arr);
    }

    function login($post = "") { // LOGIN ACTION // 0,1,2 - логстат есть
        Debug::log();
        $logstat = login_check(); // проверям статус логина        
        if ($logstat != "1") {
            if ($logstat == "2" && !isset($_COOKIE['email']) && !isset($_COOKIE['lastlogon'])) {
                $logstat = "0";
            }

            $login_continue = 0;
            if ($logstat == "2") { // перелогин
                $checkuser = mysql_call("SELECT * FROM " . DB_PREFIX . "customers WHERE email='" . $_COOKIE['email'] . "' AND lastlogon='" . $_COOKIE['lastlogon'] . "'");
                $post['login'] = $_COOKIE['email'];
                $checkuser2 = mysql_fetch_assoc($checkuser);
                $login_continue = 1;
            } else {
                $ls = $this->login_temporary($post['login'], $post['passwrd']); // TODO: временная заглушка для паролей
                if ($ls['ls'] > 0) {
                    $checkuser2 = $ls['d'];
                    $login_continue = 1;
                }
            }

            if ($login_continue == "1") {
                if ($checkuser2['hid'] == "1") {
                    $_SESSION['send_login_message'] = "Ваш профайл отключен.";
                } else { // hid=1
                    if ($checkuser2['allow_everywhere'] != "1" && SHOP_NNN != $checkuser2['orig_catshop']) {
                        $_SESSION['wrong_shop'] = 1;
                        $this->logout();
                        $_SESSION['send_login_message'] = "Вы не зарегистрированы в магазине. Будем рады, если вы зарегистрируетесь.";
                        $_SESSION['wrong_shop'] = 1;
                    } else { // allow_everywhere
                        if (isset($post['remember_me'])) {
                            $rememberme = time() + (60 * 60 * 24 * 14);
                        } else {
                            $rememberme = 0;
                        }

                        setcookie("email", $post['login'], $rememberme, "/", MAINURL_4);
                        setcookie("gender", $checkuser2['gender'], $rememberme, "/", MAINURL_4);
                        setcookie("firstname", $checkuser2['firstname'], $rememberme, "/", MAINURL_4);
                        setcookie("lastname", $checkuser2['lastname'], $rememberme, "/", MAINURL_4);
                        clearfile(array("callme_" . session_id()), "0", "txts");

                        $customers_types = unserialize(CUSTOMERS_TYPES); // типы клиентов по-человечески

                        $lastlogon = time();
                        setcookie("lastlogon", time(), $rememberme, "/", MAINURL_4);
                        $_SESSION['lastlogon'] = $lastlogon; // на время сессии пользуемся этим значением (дублируем)

                        if ($logstat != "1" && $logstat != "2" && $checkuser2['md5pssw'] != "" && @$post['passwrd'] != "") {
                            mysql_kall("UPDATE " . DB_PREFIX . "customers SET md5pssw='', pssw='" . textprocess($post['passwrd'], 'sql') . "' WHERE customers_id='" . $checkuser2['customers_id'] . "'");
                        }
                        // TODO: временная заглушка для паролей

                        mysql_kall("UPDATE " . DB_PREFIX . "customers SET lastlogon='" . $lastlogon . "', num_logons=num_logons+1 WHERE customers_id='" . $checkuser2['customers_id'] . "'") or die(mysql_error());

                        $_SESSION['shop_logged'] = SHOP_NNN;
                        $_SESSION['wrong_shop'] = "0";
                        $_SESSION['customers_type'] = $checkuser2['customers_type'];
                        $_SESSION['customers_type_nazv'] = $customers_types[$checkuser2['customers_type']];
                        $_SESSION['yur_status'] = $checkuser2['yur_status'];
                        $_SESSION['customers_id'] = $checkuser2['customers_id'];
                        $_SESSION['allow_orders'] = $checkuser2['allow_orders'];
                        $_SESSION['allow_everywhere'] = $checkuser2['allow_everywhere'];
                        $_SESSION['orig_catshop'] = $checkuser2['orig_catshop'];
                        $_SESSION['telephone'] = $checkuser2['telephone'];

                        $_SESSION['customers_discount'] = $checkuser2['personal_discount_on']; // скидка в профайле по умолчанию
                        $_SESSION['customers_discount_expire'] = time() + (60 * 60 * 24 * 31 * 12);

                        $_SESSION['temp_firstname'] = $checkuser2['firstname'];
                        $_SESSION['temp_lastname'] = $checkuser2['lastname'];

                        callme($checkuser2['firstname'], $checkuser2['lastname'], $checkuser2['telephone']);

                        $check_discount = mysql_kall("SELECT discount, expires FROM " . DB_PREFIX . "customers_specials WHERE used_by_customer_id='" . $checkuser2['customers_id'] . "' AND catshop='" . SHOP_NNN . "' AND status='1' AND expires>='" . time() . "' ORDER BY discount DESC");
                        if (mysql_num_rows($check_discount) > 0) { // скидка по купону
                            $discount2 = mysql_fetch_assoc($check_discount);
                            if ($discount2['discount'] >= $checkuser2['personal_discount_on']) { // действует, только если больше постоянной скидки и 1 раз
                                $_SESSION['customers_discount'] = $discount2['discount'];
                                $_SESSION['customers_old_discount'] = $checkuser2['personal_discount_on'];
                                $_SESSION['customers_discount_expire'] = $discount2['expires'];
                                $_SESSION['customers_coupon_active'] = 1;
                            }
                        }

                        // сохраняем корзину если есть
                        $sessid = session_id();
                        if (@$sessid != "") {
                            mysql_kall("UPDATE " . DB_PREFIX . "customers_basket_lists SET customers_id='" . $checkuser2['customers_id'] . "', temp_session='' WHERE customers_id='0' AND temp_session='" . $sessid . "' AND list_name='[basket]'");
                            mysql_kall("UPDATE " . DB_PREFIX . "customers_basket_attr SET customers_id='" . $checkuser2['customers_id'] . "', temp_session='' WHERE customers_id='0' AND temp_session='" . $sessid . "'");
                        } // сохр. корзины

                        $add_sql = cats();
                        $test = mysql_kall("SELECT SUM(" . DB_PREFIX . "customers_basket_lists.quantity) FROM " . DB_PREFIX . "customers_basket_lists, " . DB_PREFIX . "products_2_cats WHERE customers_id='" . $checkuser2['customers_id'] . "' AND list_name='[basket]'
                AND " . DB_PREFIX . "customers_basket_lists.prd_id=" . DB_PREFIX . "products_2_cats.products_nnn " . $add_sql . " GROUP BY " . DB_PREFIX . "customers_basket_lists.nnn") or die(mysql_error());
                        $_SESSION['customers_basket_num'] = @mysql_num_rows($test);
                        if ($_SESSION['customers_basket_num'] == "") {
                            $_SESSION['customers_basket_num'] = "0";
                        }

                        $_SESSION['send_login_message'] = "Вход выполнен.";
                    }
                } // hid, allow_everywhere
            } else {
                $_SESSION['send_login_message'] = "Неверные логин и пароль.";
            } // checkuser
        } // logstat=1
        $refer = "http://" . @$post['referer_host'] . @$post['referer_url'];
        if (@$_SERVER['HTTP_REFERER'] != "") {
            $refer = $_SERVER['HTTP_REFERER'];
        }

        return $refer;
    }

// LOGIN
    /////////////////////////

    function logout($post = "") { // logout
        Debug::log();
        setcookie("email", "", time() - 36000, "/", MAINURL_4);
        setcookie("gender", "", time() - 36000, "/", MAINURL_4);
        setcookie("firstname", "", time() - 36000, "/", MAINURL_4);
        setcookie("lastname", "", time() - 36000, "/", MAINURL_4);
        setcookie("lastlogon", "", time() - 36000, "/", MAINURL_4);
        clearfile(array("callme_" . session_id()), "0", "txts");
        if (@$_SESSION['wrong_shop'] != "1") {
            unset($_SESSION['customers_basket_num']);
        } // удаляем корзину во всех случае кроме wrong_shop

        clearfile("", "0", "txts", "1"); // удаляем клиентские файлы

        unset($_SESSION['send_login_message'], $_SESSION['customers_discount'], $_SESSION['customers_old_discount'], $_SESSION['basket_log_changed_a'], $_SESSION['basket_log_changed_q'], $_SESSION['customers_discount_expire'], $_SESSION['orig_catshop'], $_SESSION['allow_everywhere'], $_SESSION['allow_orders'], $_SESSION['customers_id'], $_SESSION['yur_status'], $_SESSION['customers_type'], $_SESSION['customers_lists'], $_SESSION['telephone'], $_SESSION['shop_logged'], $_SESSION['wrong_shop']);

        $refer = "http://" . @$post['referer_host'] . @$post['referer_url'];
        if (@$_SERVER['HTTP_REFERER'] != "") {
            $refer = $_SERVER['HTTP_REFERER'];
        }
        return $refer;
    }

// LOGOUT
    ////////////////////////////

    function remember($post) { // remember password
        Debug::log();
        $check_pssw = mysql_call("SELECT pssw, email FROM " . DB_PREFIX . "customers WHERE email='" . $post['email'] . "' AND hid!='1' AND pssw!=''");
        if (mysql_num_rows($check_pssw) > 0) {
            $check_pssw2 = mysql_fetch_assoc($check_pssw);

            $maintxt = strtr(EMAIL_TXT_PASSW_RESTORE, array(
                "{SHOP_NAME}" => SHOP_NAME,
                "{SHOP_URL}" => MAINURL,
                "{PASSW}" => $check_pssw2['pssw'],
            ));
            $txt = "From: " . EMAIL_ADMIN . "
To: " . $check_pssw2['email'] . "
Subject: " . SHOP_NAME . " : восстановление пароля
Content-type: text/html;
              charset=windows-1251

" . $maintxt;

            $mail = mailenc($txt);
            mailx($mail);
        }
        $refer = "http://" . @$post['referer_host'] . @$post['referer_url'];
        if (@$_SERVER['HTTP_REFERER'] != "") {
            $refer = $_SERVER['HTTP_REFERER'];
        }
        return $refer;
    }

    // REMEMBER //////////////////////////////////////////
    /////////////////////////////////////////////////////

    function guestbook($post, $ref, $type = "") { // type=callme,
        Debug::log();
        $n = 1;
        if ($type == "callme") {
            $n = 2;
        }

        if (trim($_SESSION['number' . $n]) != "" && trim($post['vercode']) != "") {
            if ($_SESSION['number' . $n] == mb_strtolower($post['vercode'])) {


                $ref2 = explode("/product/", $ref);
                if (count($ref2) > 1) {
                    $prd_from = trim($ref2[1]);
                }
                $ref2 = explode("/catalog/", $ref);
                if (count($ref2) > 1) {
                    $catshop_from = trim($ref2[1]);
                } else {
                    $catshop_from = SHOP_NNN;
                }

                $type_prepare['callme'] = array("mails_type" => "0",
                    "callme_type" => "1",
                    "to_whom" => "all",
                    "link_from" => $ref,
                    "prd_from" => @$prd_from,
                    "order_from" => "",
                    "customer_id_from" => @$_SESSION['customers_id'],
                    "catshop_from" => @$catshop_from,
                    "avtor" => textprocess(@$post['call_to'], 'sql'),
                    "txt" => textprocess(@$post['msg'], 'sql'),
                    "dat" => time(),
                    "phone" => @$post['phone_to'],
                    "phone_dat" => @$post['time_to'],
                    "email" => "",
                    "hid" => "0",
                    "inside" => "0"
                ); // TODO: order_from

                $type_prepare['contact'] = array("mails_type" => "1",
                    "callme_type" => "0",
                    "to_whom" => "contact_theme_" . $post['whom_to'],
                    "link_from" => $ref,
                    "prd_from" => "",
                    "order_from" => "",
                    "customer_id_from" => @$_SESSION['customers_id'],
                    "catshop_from" => @$catshop_from,
                    "avtor" => textprocess(@$post['avtor'], 'sql'),
                    "txt" => textprocess(@$post['msg'], 'sql'),
                    "dat" => time(),
                    "phone" => "",
                    "phone_dat" => "",
                    "email" => textprocess(@$post['email'], 'sql'),
                    "hid" => "0",
                    "inside" => "0"
                );

                $type_prepare2 = implode("', '", $type_prepare[$type]);

                $sql_ins = "INSERT
            INTO " . DB_PREFIX . "guestbook (mails_type, callme_type, to_whom, link_from, prd_from, order_from, customer_id_from, catshop_from,
                avtor, txt, dat, phone, phone_dat, email, hid, inside)
                        VALUES ('" . $type_prepare2 . "')";
                mysql_kall($sql_ins);
                $_SESSION['send_login_message'] = "Отправлено.";
                return;
            } else {
                $_SESSION['send_login_message'] = "Неверный проверочный код.";
            }
        } else {
            $_SESSION['send_login_message'] = "Проверочный код не указан.";
        }
    }

// GUESTBOOK
    // здесь будут собираться данные для страницы клиента
    function customers_page($id = "") { // TODO: переделать страницу клиента в нормальную
        Debug::log();
        if ($id == "") {
            $id = @$_SESSION['customers_id'];
        }
        if ($id <= 0) {
            
        } else {
            $order = new order;
            $status_find = $order->status_finder("all");

            // пока только заказы
            $all_o = mysql_kall("SELECT oidhash, catshop, orders_id_real, dat, status, content_price, delivery_price, payment_method, price FROM " . DB_PREFIX . "orders WHERE customers_id='" . $id . "' ORDER BY dat DESC");
            $ao = mysql_fetch_assoc($all_o);
            $olist = "";
            if (mysql_num_rows($all_o) > 0) {
                do {
                    $catsup = cats_up($ao['catshop']);
                    $olist.="<b><a href=" . $catsup['remote_addr'] . "/user/orders/" . $ao['oidhash'] . ">Заказ №" . $ao['orders_id_real'] . "</a> от " . date("d.m.Y", $ao['dat']) . ":</b> " . $status_find[$ao['status']]['nazv'] . "<br>";
                    $olist.="<font color=#999999>" . $ao['content_price'] . " <span class=\"rur\">p</span> + " . $ao['delivery_price'] . " <span class=\"rur\">p</span> + " . $ao['payment_method'] . " = " . $ao['price'] . " <span class=\"rur\">p</span></font><br>";
                    $olist.="<br>";
                } while ($ao = mysql_fetch_assoc($all_o));
            }

            return $olist;
        }
    }

}

?>