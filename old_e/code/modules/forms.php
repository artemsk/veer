<?php

class forms {

    //put your code here
    // REGISTER FORM
    function register_form() {
        Debug::log();
        $out_frm = "<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\" enctype=\"multipart/form-data\">";

        $gender = array('' => 'м', '0' => 'м', '1' => 'ж');
        // block 1 - info1
        $formnames = array("email", "pssw", "pssw2", "firstname", "lastname", "dob", "telephone", "gender");
        $formvalues = array(@$_SESSION['register_form_email'], "", "", @$_SESSION['register_form_firstname'], @$_SESSION['register_form_lastname'], @$_SESSION['register_form_dob'],
            @$_SESSION['register_form_telephone'], array(@$_SESSION['register_form_gender'] => $gender[@$_SESSION['register_form_gender']], '0' => 'м', '1' => 'ж'));
        $formdescr = array('e-mail', 'пароль', 'пароль еще раз', 'имя /отчество/', 'фамилия', 'дата рождения', 'телефон (код города!)', 'пол');
        $formtypes = array('text', 'password', 'password', 'text', 'text', 'text', 'text', 'select');
        $formbrline = array("<tr><td>", "</td><td>", "</td><td>", "</td></tr><tr><td>", "</td><td colspan=2>", "</td></tr><tr><td>", "</td><td>", "</td><td>");
        $crform = formcreate($formnames, $formtypes, $formvalues);
        $out_frm.="<br><b>Информация о новом клиенте</b><br><div class=\"register_page_tbls\"><table cellpadding=5>";
        foreach ($formdescr as $k => $v) {
            $out_frm.=$formbrline[$k] . $v . "<br>" . $crform[$k] . "";
        } $out_frm.="</td></tr></table></div><br>";


        $out_frm.="<b>Зарегистрировать как</b> <input type=\"radio\" name=yur_status value='0'";
        if (isset($_SESSION['register_form_yur_status']) && @$_SESSION['register_form_yur_status'] == "1") {
            
        } else {
            $out_frm.=" checked";
        }
        $out_frm.=" onclick=\"javascript:hide('form_yur');show('form_fiz');\"/> Физ. лицо &nbsp; <input type=\"radio\" name=yur_status value='1'
            onclick=\"javascript:show('form_yur');hide('form_fiz');\"";
        if (isset($_SESSION['register_form_yur_status']) && @$_SESSION['register_form_yur_status'] == "1") {
            $out_frm.=" checked";
        }
        $out_frm.="/> Юр. лицо<br />";

        if (isset($_SESSION['register_form_yur_status']) && @$_SESSION['register_form_yur_status'] == "1") {
            $fizform = "none;";
            $yurform = "block;";
        } else {
            $fizform = "block;";
            $yurform = "none;";
        }
        // block 2 - address
        $formnames = array("country", "region", "city", "postcode", "street_address_1", "street_address_2", "metro");
        $formvalues = array('Россия', @$_SESSION['register_form_region'], @$_SESSION['register_form_city'], @$_SESSION['register_form_postcode'], @$_SESSION['register_form_street_address_1'], @$_SESSION['register_form_street_address_2'], @$_SESSION['register_form_metro']);
        $formdescr = array('страна', "регион/область", "город", "индекс", "улица и номер дома", "подъезд, этаж, №квартиры", "метро (для Москвы)");
        $formtypes = array('text', 'text', 'text', 'text', 'textarea', 'textarea', 'text');
        $formbrline = array("<tr><td>", "</td><td>", "</td></tr><tr><td>", "</td><td>", "</td></tr><tr><td>", "</td><td>", "</td></tr><tr><td colspan=2>");
        $crform = formcreate($formnames, $formtypes, $formvalues);
        $out_frm.="<div class=\"register_page_tbls\" id=\"form_fiz\" style=\"display:" . $fizform . "\"><table cellpadding=5>";
        foreach ($formdescr as $k => $v) {
            $out_frm.=$formbrline[$k] . $v . "<br>" . $crform[$k] . "";
        } $out_frm.="</td></tr></table><br></div>";

        // block 2.5 - yur info + address
        $formnames = array("company", "inn", "schet", "bank", "korr", "bik", "postcode2", "city2", "region2", "address");
        $formvalues = array(@$_SESSION['register_form_company'], @$_SESSION['register_form_inn'], @$_SESSION['register_form_schet'], @$_SESSION['register_form_bank'], @$_SESSION['register_form_korr'],
            @$_SESSION['register_form_bik'], @$_SESSION['register_form_postcode2'], @$_SESSION['register_form_city2'], @$_SESSION['register_form_region2'], @$_SESSION['register_form_address']);
        $formdescr = array("Компания (ОАО, ЗАО, ООО, ПБОЮЛ)", "ИНН/КПП", "р/счет", "Банк", "корр/c", "БИК", "индекс", "город", "регион", "адрес");
        $formtypes = array('text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'textarea');
        $formbrline = array("<tr><td colspan=3>", "</td></tr><tr><td>", "</td><td colspan=2>", "</td></tr><tr><td>", "</td><td>", "</td><td>", "</td></tr><tr><td>",
            "</td><td>", "</td><td>", "</td></tr><tr><td colspan=3>");
        $crform = formcreate($formnames, $formtypes, $formvalues);
        $out_frm.="<div class=\"register_page_tbls\" id=\"form_yur\" style=\"display:" . $yurform . "\"><table cellpadding=5>";
        foreach ($formdescr as $k => $v) {
            $out_frm.=$formbrline[$k] . $v . "<br>" . $crform[$k] . "";
        } $out_frm.="</td></tr></table></div><br>";

        // block 3 - notifications, coupong, promokds etc.
        $formnames = array("promokod", "coupon", "note", "newsletter_sign' checked='true", "newsletter_prd_sign", "spcod' size='5", "shop_cat", 'addkli');
        $formvalues = array('', '', @$_SESSION['register_form_note'], '', '', '', SHOP_NNN, 'зарегистрироваться');
        $formdescr = array('Промокод для регистрации дистрибьютора или гостя дистрибьютора (если есть)', 'Купон на скидку (если есть)', 'Комментарий', 'подписка на новости', 'подписка на товары', 'код: <img style="border:1px #cccccc dotted;" src=' . MAINURL . '/code/image.php align="absbottom">', '', '');
        $formtypes = array('text', 'text', 'textarea', 'checkbox', 'checkbox', 'text', 'hidden', 'submit');
        $crform = formcreate($formnames, $formtypes, $formvalues);
        $out_frm.="<b>Дополнительно</b><br><div class=\"register_page_tbls\"><table cellpadding=5><tr><td>";
        foreach ($formdescr as $k => $v) {
            if ($formtypes[$k] == "hidden") {
                $out_frm.=$crform[$k] . "";
            } else {
                if ($formnames[$k] == "spcod' size='5") {
                    $out_frm.=$v . " " . $crform[$k] . "<br><br>";
                } else {
                    $out_frm.=$v . "<br>" . $crform[$k] . "<br><br>";
                }
            }
        } $out_frm.="</td></tr></table></div><br>";

        // customers_type, yur_status -> hidden
        // array('user'=>'клиент','distr'=>'дистрибьютор','guest'=>'гость','optovik'=>'оптовик') - оптовиком только чз админку

        $out_frm.="</form>";
        return $out_frm;
    }

    //////////////////////////
    // LOGIN ////////////////////////////
    function login_form() {
        Debug::log();
        $out_frm = "<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";

        $formnames = array("login' id='login_top' size='15", "passwrd' id='pssw_top' size='10", "remember_me", "referer_url", "referer_host", "logdone");
        $formvalues = array("", "", "", $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "войти");
        $formdescr = array("E-mail", "Пароль", "Запомнить", "[hid]", "[hid]", "");
        $formtypes = array('text', 'password', 'checkbox', 'hidden', 'hidden', 'submit');
        $formattrs = array("class='loginform'", "class='loginform'", "", "", "");
        $cr_loginform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
        $out_frm.="<div id='login_div' style='display:none;'><span id='login_div_p1'>";

        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_loginform[$k];
                continue;
            }
            if ($v != "") {
                $out_frm.=$v . " &rarr; ";
            }
            $out_frm.=$cr_loginform[$k] . "&nbsp; &nbsp; ";
        }

        //   if($o!="") { $out_frm.="</div> <a href=javascript:show('login_again_form');hide('compare_lists_top');>Войти</a> | "; }

        $out_frm.="</span><a href=" . MAINURL . "/user/register>Регистрация</a> | ";
        $out_frm.="<a href=javascript:show('remember_div');hide('login_div');>Забыл пароль</a>";
        $out_frm.="</div><div id='remember_div' style='display:none;'>"; // remember pass form &darr;
        $formnames = array("email", "referer_url", "referer_host", "forgot");
        $formvalues = array("", $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "отправить");
        $formdescr = array("E-mail, который вы указали при регистрации", "[hid]", "[hid]", "");
        $formtypes = array('text', 'hidden', 'hidden', 'submit');
        $formattrs = array("class='loginform'", "", "", "");
        $cr_forgetform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_forgetform[$k];
                continue;
            }
            if ($v != "") {
                $out_frm.=$v . " &rarr; ";
            }
            $out_frm.=$cr_forgetform[$k] . "&nbsp; &nbsp; ";
        }
        $out_frm.="<a href=" . MAINURL . "/user/register>Регистрация</a> | ";
        $out_frm.="<a href=javascript:hide('remember_div');show('login_div');>Вход</a>";
        $out_frm.="</div>";
        $out_frm.="</form>";
        return $out_frm;
    }

    ///////////////////////////////
    // LOGOUT ///////////////////////////////
    function logout_form($in_form_data = "") {
        Debug::log();
        $out_frm = "<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">" . @$in_form_data;
        $formnames = array("referer_url", "referer_host", "logout");
        $formvalues = array($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "выйти");
        $formdescr = array("[hid]", "[hid]", "");
        $formtypes = array('hidden', 'hidden', 'submit');
        $cr_logoutform = formcreate($formnames, $formtypes, $formvalues);

        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_logoutform[$k];
                continue;
            }
            if ($v != "") {
                $out_frm.=$v . " &rarr; ";
            }
            $out_frm.=$cr_logoutform[$k] . " ";
        }
        $out_frm.="</form>";
        return $out_frm;
    }

    function add2list_form($prd_id) { // наличие с_id обязательно
        Debug::log();
        lists2session();
        $c_lists4 = @unserialize(@$_SESSION['customers_lists']);
        $lists = explode("}{", CUSTOMERS_DEFAULT_LISTS);
        foreach ($lists as $k => $v) {
            if (isset($c_lists4[$v])) {
                $lists_new[trim($v)] = trim($v) . " (" . $c_lists4[$v] . ")";
                if ($v == COMPARE_LIST_NAME && @$c_lists4[$v] >= 2) {
                    $flag_compare = $c_lists4[$v];
                } // ссылка на страницу сравнений
                unset($c_lists4[$v]);
            } else {
                $lists_new[trim($v)] = trim($v);
            }
        }

        if (isset($c_lists4) && @$c_lists4 != "" && count(@$c_lists4) > 0) {
            foreach ($c_lists4 as $k => $v) {
                $lists_new[trim($k)] = trim($k) . " (" . $v . ")";
            }
        }

        $out_frm = "<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
        $formnames = array("list_name", "new_list_name", "prd_id", "referer_url", "referer_host", "add2list");
        $formvalues = array($lists_new, '', $prd_id, $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "добавить");
        $formdescr = array("добавить в", "", "[hid]", "[hid]", "[hid]", "");
        $formtypes = array('select', 'text', 'hidden', 'hidden', 'hidden', 'submit');
        $formattrs = array("style='font-size:1em;font-family:Verdana;overflow:hidden;width:180px;'", "style='font-size:1em;font-family:Verdana;'", "", "", "", "style='font-size:1em;font-family:Verdana;'");
        $cr_logoutform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_logoutform[$k];
                continue;
            }
            if ($v != "") {
                $out_frm.="" . $v . "<p></p>";
            }
            if ($formnames[$k] == "list_name") {
                $out_frm.="<div id='all_addlist' style='display:block;'>";
            }
            if ($formnames[$k] == "new_list_name") {
                $out_frm.="<div id='new_addlist' style='display:none;'>";
            }
            $out_frm.=$cr_logoutform[$k] . " ";
            if ($formnames[$k] == "list_name" || $formnames[$k] == "new_list_name") {
                $out_frm.="</div><p></p>";
            }
        }
        if (@$flag_compare >= 2) {
            $out_frm.="<a href='" . MAINURL . "/user/compare' class='product_compare'>Сравнить (" . $flag_compare . ")</a><p></p>";
        }
        $out_frm.="<div id='all_addlist2' style='display:block;'><a href=javascript:show('new_addlist');hide('all_addlist');show('new_addlist2');hide('all_addlist2');>новый список</a></div>";
        $out_frm.="<div id='new_addlist2' style='display:none;'><a href=javascript:hide('new_addlist');show('all_addlist');hide('new_addlist2');show('all_addlist2');>выбрать список</a></div>";
        $out_frm.="</form>";
        return $out_frm;
    }

    function add2basket_form($prd_id, $bsk_link, $attrs = "", $attrs_descr = "", $grps = "", $type = "full", $st = "buy") { // full для страницы товара
        //// id товара для добавления, ссылка, массив с выбраными свойствами, отдельно для группы, кол-во
        Debug::log();
        $form_out = "";
        if ($st == "buy" || $st == "predzakaz") { // status=buy
            if (substr($bsk_link, 0, 6) == "<input" || substr($bsk_link, 0, 7) == "<button" ) { // локальная ссылка
                $form_out.="<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
                $formnames = array("prd_id", "referer_url", "referer_host", "attrs_descr");
                $formvalues = array($prd_id, $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], @serialize(@$attrs_descr));
                $formdescr = array("[hid]", "[hid]", "[hid]", "[hid]");
                $formtypes = array('hidden', 'hidden', 'hidden', 'hidden');
                $cr_bskform = formcreate($formnames, $formtypes, $formvalues);
                $form_out.=implode("", $cr_bskform);
                if ($type == "full") {
                    if (count($grps) > 0) { // группа
                        $form_out_tmp = "<table class='product_attr_choose_tbl'>";
                        foreach ($grps as $k => $v) {
                            if (isset($attrs[$k])) {
                                foreach ($v as $kk => $vv) {
                                    $form_out_tmp.="<tr><td class='product_attr_choose_tbl_grp'>" . $vv . "</td></tr>";
                                    foreach ($attrs[$k][$kk] as $kkk => $vvv) {
                                        $form_out_tmp.="<tr><td valign=top style='padding-left:15px;'>" . $kkk . "</td></tr><tr>
                       <td style='padding-left:30px;' valing=top>" . $vvv . "</td></tr>";
                                    }
                                }
                            }
                        }
                        $form_out_tmp.="</table>";
                        if ($form_out_tmp != "<table class='product_attr_choose_tbl'></table>") {
                            $form_out.=$form_out_tmp;
                        }
                    } else { // одиночный товар:
                        if (count($attrs) > 0) {
                            $form_out.="<table class='product_attr_choose_tbl'>";
                            foreach ($attrs[0][$prd_id] as $k => $v) {
                                $form_out.="<tr><td valign=top>" . $k . "</td></tr><tr>
                     <td style='padding-left:15px;' valing=top>" . $v . "</td></tr>";
                            }
                            $form_out.="</table>";
                        }
                    }
                } // full

                if ($bsk_link != "") {
                    $form_out.=$bsk_link;
                }  // submit link

                $form_out.="</form>";
            }
        } // status=buy
        // TODO: cделать предзаказ
        return $form_out;
    }
    // add2basket

    function review_form($prd_id) { // review rate
        Debug::log();
        $out_frm = "<table class='comments_form'><form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
        $formnames = array("avtor", "txt' cols=50 rows='5", "rate_star", "vercode", "referer_url", "referer_host", "review_send", "prdid");
        $formvalues = array("" . @$_COOKIE['firstname'] . " " . @$_COOKIE['lastname'] . "", "", array("" => "", "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5"), "", $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "добавить", $prd_id);
        $formdescr = array("автор", "отзыв", "оценка (1-5)", "код: <img style=\"border:1px #cccccc dotted;\" src=" . MAINURL . "/code/image.php align=\"absbottom\">", "[hid]", "[hid]", "", "[hid]");
        $formtypes = array('text', 'textarea', 'select', 'text', 'hidden', 'hidden', 'submit', 'hidden');
        $cr_reviewform = formcreate($formnames, $formtypes, $formvalues);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_reviewform[$k];
                continue;
            }
            $out_frm.="<tr><td valign=top>" . $v . "</td><td>" . $cr_reviewform[$k] . "</td></tr>";
        }
        $out_frm.="</form></table>";
        return $out_frm;
    }

    function rate_form($prd_id, $votes_y = " ", $votes_n = " ") { // rate prd
        Debug::log();
        $out_frm = "<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
        $formnames = array("vote_y", "vote_n", "referer_url", "referer_host", "prdid");
        $formvalues = array(MAINURL . "/template/" . TEMPLATE . "/images/rate_yes.png", MAINURL . "/template/" . TEMPLATE . "/images/rate_no.png", $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], $prd_id);
        $formdescr = array($votes_y, $votes_n, "[hid]", "[hid]", "[hid]");
        $formtypes = array('submit_img', 'submit_img', 'hidden', 'hidden', 'hidden');
        $cr_reviewform = formcreate($formnames, $formtypes, $formvalues);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_reviewform[$k];
                continue;
            }
            $out_frm.=$cr_reviewform[$k] . " " . $v . "";
            if ($k == "0") {
                $out_frm.="&nbsp;";
            }
        }
        $out_frm.="</form>";
        return $out_frm;
    }

    function comment_form($page_id, $allow = "1") { // page comments: pages_nnn, avtor, registered, txt, dat, hid, customers_id
        Debug::log();
        if ($allow == "1") {
            $out_frm = "<table class='comments_form'><form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
            $formnames = array("pages_nnn", "avtor", "registered", "txt' cols=50 rows='5", "vercode", "dat", "referer_url", "referer_host", "comment_send");
            if (isset($_SESSION['customers_id'])) {
                $avtor = @$_COOKIE['firstname'] . " " . @$_COOKIE['lastname'];
                $registered = "1";
                $texttype = "text off";
            } else {
                $texttype = "text";
                $avtor = ANONIM_NAME;
                $registered = "0";
            }
            $formvalues = array($page_id, $avtor, $registered, "", "", time(), $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "добавить");
            $formdescr = array("[hid]", "Имя", "[hid]", "Комментарий", "код: <img style=\"border:1px #cccccc dotted;\" src=" . MAINURL . "/code/image.php align=\"absbottom\">", "[hid]", "[hid]", "[hid]", "");
            $formtypes = array('hidden', $texttype, 'hidden', 'textarea', 'text', 'hidden', 'hidden', 'hidden', 'submit');
            $cr_comform = formcreate($formnames, $formtypes, $formvalues);
            foreach ($formdescr as $k => $v) {
                if ($v === "[hid]") {
                    $out_frm.=$cr_comform[$k];
                    continue;
                }
                $out_frm.="<tr><td align=right valign=top>" . $v . "</td><td>" . $cr_comform[$k] . "</td></tr>";
            }
            $out_frm.="</form></table>";
            return $out_frm;
        }
    }

    function search_form($q = "Поиск") {
        Debug::log();
        $form_out = "";
        $form_out.="<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
        $formnames = array("search_q' style='width:150px;padding:2px 2px 2px 2px;' id='searchf' onClick='clearText(searchf)", "referer_url",
            "referer_host", "search_send");
        $formvalues = array($q, $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "&nbsp;&rarr;&nbsp;");
        $formdescr = array("", "[hid]", "[hid]", "");
        $formtypes = array('text', 'hidden', 'hidden', 'submit');
        $cr_comform = formcreate($formnames, $formtypes, $formvalues);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $form_out.=$cr_comform[$k] . " ";
                continue;
            }
            $form_out.=$v . $cr_comform[$k] . " ";
        }
        $form_out.="</form>";
        return $form_out;
    }

    function callme_form($fn = "", $ln = "", $ph = "") {
        Debug::log();
        $form_out = "";
        $form_out.="<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\"><table>";
        $formnames = array("call_to", "phone_to", "time_to", "msg", "vercode", "referer_url", "referer_host", "callme_send");

        if ($fn == "") {
            $fn = @$_COOKIE['firstname'];
        }
        if ($ln == "") {
            $ln = @$_COOKIE['lastname'];
        }
        if ($ph == "") {
            $ph = @$_SESSION['telephone'];
        }

        $formvalues = array(@$fn . " " . @$ln, @$ph, '', '', '', $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "отправить");
        $formdescr = array("Ваше имя: ", "Телефон для связи:<br/><font size=1 style=\"color:#999999;\">формат 8(903)574-5658</font>", "Удобное время звонка: ",
            "Дополнительная<br/>информация:<br/><font size=1>(по теме звонка)</font>",
            "код: <img style=\"border:1px #cccccc dotted;\" src=" . MAINURL . "/code/image.php?n=2 align=\"absbottom\">", "[hid]", "[hid]", "<font size=1><a href=\"javascript:hide('callme_content');show('head_content');\">закрыть</a></font>");
        $formtypes = array('text', 'text', 'text', 'textarea', 'text', 'hidden', 'hidden', 'submit');
        $cr_comform = formcreate($formnames, $formtypes, $formvalues);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $form_out.="<tr><td></td><td valign=\"top\">" . $cr_comform[$k] . "</td></tr>";
                continue;
            }
            $form_out.="<tr><td valign=\"top\" align=\"right\">" . $v . "</td><td valign=top>" . $cr_comform[$k] . "</td></tr>";
        }
        $form_out.="</table></form>";
        return $form_out;
    }

    function contact_us_form() {
        Debug::log();
        $form_out = "";
        $form_out.="<hr class=\"divider\"><form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
        $formnames = array("avtor", "email", "whom_to", "msg", "vercode", "referer_url", "referer_host", "guestbook_send");
        $formvalues = array(@$_COOKIE['firstname'] . " " . @$_COOKIE['lastname'], @$_COOKIE['email'], explode("\n", CONTACT_THEMES), '', '', $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "отправить");
        $formdescr = array("Ваше имя: ", "Ваш e-mail: ", "Тема: ",
            "Сообщение: ",
            "код: <img style=\"border:1px #cccccc dotted;\" src=" . MAINURL . "/code/image.php align=\"absbottom\">", "[hid]", "[hid]", "");
        $formtypes = array('text', 'text', 'select', 'textarea', 'text', 'hidden', 'hidden', 'submit');
        $formattrs = array('', '', '', 'rows="10" cols="60"', '', '', '', '');
        $cr_comform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $form_out.="" . $cr_comform[$k] . "<p></p>";
                continue;
            }
            $form_out.=$v . "<p></p>" . $cr_comform[$k] . "<p></p>";
        }
        $form_out.="</form>";

        return $form_out;
    }

    function basket_form($main = "", $grpbybasket_send = "") {
        Debug::log();
        $form_out = "";
        if ($main != "") {
            $form_out.="<form id=\"basket_total_form\" action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
            $form_out.=$main;
            $formnames = array("grpbybasket", "referer_url", "referer_host", "update_cart_fin' disabled='disabled' id='update_cart_fin");
            $formvalues = array(serialize($grpbybasket_send), $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "подтвердить заказ");
            $formdescr = array("[hid]", "[hid]", "[hid]",
                "<div id='update_cart_fin_txt'>Для оформления заказа, пожалуйста, укажите адрес доставки, выберите подходящие вам способ доставки и форму оплаты</div>");
            $formtypes = array('hidden', 'hidden', 'hidden', 'submit');
            $cr_comform = formcreate($formnames, $formtypes, $formvalues);
            foreach ($formdescr as $k => $v) {
                if ($v === "[hid]") {
                    $form_out.="" . $cr_comform[$k] . "<p></p>";
                    continue;
                }
                $form_out.=$v . "<div class='update_cart_fin_button'><span id='update_cart_fin_summ'></span>" . $cr_comform[$k] . "</div>";
            }
            $form_out.="<hr class=\"divider4\"><div class=\"update_cart_fin_error\">Если у вас не получается оформить заказ, <input type=submit name='update_cart_error_fix' value='нажмите сюда' style='font-size:1em;'> и наши менеджеры свяжутся с вами
            по указанным в информации, адресе доставки или комментариях контактным данным (пожалуйста, укажите телефон или электронный адрес, если они нигде не указаны)</div></form>";
        }
        return $form_out;
    }

    function basket_form_address_book($main = "", $bask_id = "", $w = "", $p = "", $savedselects = "", $flag4upd_newaddr = 0) {
        Debug::log();
        $form_out = "";
        $donecheck = 0;
        if (is_array($main)) { // есть массив из адресной книги
            foreach ($main['full'] as $k => $v) {
                $formnames[] = "deliver_addr_book[id_" . $bask_id . "]";
                $formdescr[] = $v;
                $formtypes[] = "radio";
                $formvalues[] = $k;
                $a = "id='deliver_addr" . $bask_id . "_" . $main['postcode'][$k] . "_" . $w . "_" . strtr($p, array("." => "-")) . "_" .
                        strtr($main['city'][$k], array(" " => "-", "javascript:" => "", "." => "", "'" => "", "," => "", "_" => "", ":" => "", ";" => "", "/" => "", "http://" => "", "?" => "", "=" => "")) . "_" .
                        strtr($main['country'][$k], array(" " => "-", "javascript:" => "", "." => "", "'" => "", "," => "", "_" => "", ":" => "", ";" => "", "/" => "", "http://" => "", "?" => "", "=" => "")) . "'";
                if ($savedselects['deliver_addr_book']['id_' . $bask_id] == $k && $flag4upd_newaddr != "1") {
                    $a.=" checked";
                    $donecheck = 1;
                }
                if (count($savedselects['deliver_addr_book']['id_' . $bask_id]) <= 0 && $k == $main['default_shipping'] && $flag4upd_newaddr != "1" && $donecheck != "1") {
                    $a.=" checked";
                }
                $formattrs[] = $a;
            }
            $cr_comform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
            $donecheck = 0;
            foreach ($formdescr as $k => $v) {
                $form_out.="<tr class=\"tblrow\" onmouseover=\"rowOverEffect(this)\" onmouseout=\"rowOutEffect(this)\" ";
                if ($savedselects['deliver_addr_book']['id_' . $bask_id] == $formvalues[$k] && $flag4upd_newaddr != "1") {
                    $form_out.="id=\"defaultSelected_addr" . $bask_id . "\"";
                    $donecheck = 1;
                }
                if (count($savedselects['deliver_addr_book']['id_' . $bask_id]) <= 0 && $formvalues[$k] == $main['default_shipping'] && $flag4upd_newaddr != "1" && $donecheck != "1") {
                    $form_out.="id=\"defaultSelected_addr" . $bask_id . "\"";
                }
                $form_out.="onclick=\"selectRowEffect_addr" . $bask_id . "(this)\"";
                $form_out.="><td><div style=\"float:left;\">" . $cr_comform[$k] . "</div><div class=\"cust_addr_book_lst\">" . $v . "</div></td></tr>";
            }

            $form_out = "<script>var selected_addr" . $bask_id . ";function selectRowEffect_addr" . $bask_id . "(object) { if (!selected_addr" . $bask_id . ") { if (document.getElementById) {
             selected_addr" . $bask_id . " = document.getElementById('defaultSelected_addr" . $bask_id . "'); } else { selected_addr" . $bask_id . " = document.all['defaultSelected_addr" . $bask_id . "']; }}
             if (selected_addr" . $bask_id . ") selected_addr" . $bask_id . ".className = 'tblrow'; object.className = 'tblrow_selected'; selected_addr" . $bask_id . " = object; }</script>
                <table cellpadding=0>" . $form_out . "</table>";
            if ($flag4upd_newaddr == "1") {
                $form_out = "<div id=\"addressbook_2_choose" . $bask_id . "\" style=\"display:none;\">" . $form_out . "";
            } else {
                $form_out = "<div id=\"addressbook_2_choose" . $bask_id . "\">" . $form_out . "";
            }

            $form_out.="<p></p><a id=\"newaddressbook_lnk" . $bask_id . "_" . $w . "_" . strtr($p, array("." => "-")) . "\" href=\"javascript:show('newaddressbook" . $bask_id . "');hide('addressbook_2_choose" . $bask_id . "')\" class=\"addrbook_newlink\">&rarr; новый адрес</a></div>";
        }
        // массив из адресной книги ///
        /// новый адрес
        $form_out.="<div id=\"newaddressbook" . $bask_id . "\"";
        if (is_array($main) && @$flag4upd_newaddr != "1") {
            $form_out.=" style=\"display:none;\">";
        } else {
            $form_out.=">";
        }
        $form_out.="<table>";
        $formnames = array("deliver_new_addr_country[" . $bask_id . "]' id='country_addr" . $bask_id . "", "deliver_new_addr_region[" . $bask_id . "]", "deliver_new_addr_city[" . $bask_id . "]' id='city_addr" . $bask_id . "_" . $w . "_" . strtr($p, array("." => "-")) . "",
            "deliver_new_addr_metro[" . $bask_id . "]", "deliver_new_addr_postcode[" . $bask_id . "]' id='pscd_addr" . $bask_id . "_" . $w . "_" . strtr($p, array("." => "-")) . "",
            "deliver_new_addr_street_address_1[" . $bask_id . "]", "deliver_new_addr_street_address_2[" . $bask_id . "]", "deliver_new_addr_fio[" . $bask_id . "]", "deliver_new_addr_company[" . $bask_id . "]");
        $formdescr = array("Страна: ", "Регион, область: ", "Город: ", "Метро (для Москвы): ", "Индекс: ", "Улица, дом: ", "Подъезд, этаж, квартира, домофон: ", "ФИО: ", "Организация: ");
        $formtypes = array("text", "text", "text", "text", "text", "text", "text", "text", "text");
        if (@$savedselects['deliver_new_addr_country'][$bask_id] != "") {
            $cv = @$savedselects['deliver_new_addr_country'][$bask_id];
        } else {
            $cv = "Россия";
        }
        if (@$savedselects['deliver_new_addr_fio'][$bask_id] != "") {
            $fiov = @$savedselects['deliver_new_addr_fio'][$bask_id];
        } else {
            $fiov = @$_SESSION['firstname'] . " " . @$_SESSION['lastname'];
        }

        $formvalues = array($cv, @$savedselects['deliver_new_addr_region'][$bask_id], @$savedselects['deliver_new_addr_city'][$bask_id],
            @$savedselects['deliver_new_addr_metro'][$bask_id], @$savedselects['deliver_new_addr_postcode'][$bask_id], @$savedselects['deliver_new_addr_street_address_1'][$bask_id],
            @$savedselects['deliver_new_addr_street_address_2'][$bask_id], $fiov,
            @$savedselects['deliver_new_addr_company'][$bask_id]);
        $cr_comform = formcreate($formnames, $formtypes, $formvalues);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $form_out.="" . $cr_comform[$k] . "";
                continue;
            }
            $form_out.="<tr><td valign=top align=right><strong>" . $v . "</strong></td><td valign=top>" . $cr_comform[$k] . "</td></tr>";
        }
        if (is_array($main)) {
            $form_out.="<tr><td colspan=2><a href=\"javascript:hide('newaddressbook" . $bask_id . "');show('addressbook_2_choose" . $bask_id . "')\" class=\"addrbook_newlink\">&larr; выбрать адрес</a></td></tr>";
        }
        $form_out.="</table></div>";
        // новый адрес

        return $form_out;
    }

    function basket_form_delivery($main = "", $bask_id = "", $p = "", $saveddelivery = "") {
        Debug::log();
        $form_out = "";
        if (is_array($main)) {
            $kaunt2 = 0;
            foreach ($main as $k => $v) {
                // if($v['flag']>0) {
                $formnames[] = "deliver_bask[id_" . $bask_id . "]";

                $a = "id='delivert" . $bask_id . "_" . $kaunt2 . "_" . $k . "_" . ceil($v['summ']) . "_" . ceil($p) . "'";
                if ($v['flag'] <= 0) {
                    $a.=" disabled";
                    $formdescr2[] = "disabled";
                } else {
                    $formdescr2[] = "";
                }

                $formdescr[] = $v[0] . " " . $v[1] . "</td><td class=\"deliverytypes_loading" . $bask_id . "\">" . $v['txt'] . "" . @$v['txt_add'];
                unset($main[$k]['txt_add']);
                $formtypes[] = "radio";
                $formvalues[] = $k . "_" . $v['summ'];
                if (($k . "_" . $v['summ']) == @$saveddelivery) {
                    $a.=" checked";
                }
                $formattrs[] = $a;
                $kaunt2++;
                //    }
            }
            $v2 = serialize($main);

            $formnames[] = "deliver_arr[id_" . $bask_id . "]";
            $formdescr[] = "[hid]";
            $formdescr2[] = "";
            $formtypes[] = "hidden";
            $formvalues[] = $v2;
            $formattrs[] = '';

            $cr_comform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
            $kaunt = 0;
            foreach ($formdescr as $k => $v) {
                if ($v === "[hid]") {
                    $form_out.="" . $cr_comform[$k] . "";
                    continue;
                }
                $form_out.="<tr class=\"tblrow\" onmouseover=\"rowOverEffect(this)\" onmouseout=\"rowOutEffect(this)\" ";
                if ($formvalues[$k] == @$saveddelivery) {
                    $form_out.="id=\"defaultSelected_delivery" . $bask_id . "\" ";
                }
                if ($formdescr2[$k] != "disabled") {
                    $form_out.="onclick=\"selectRowEffect" . $bask_id . "(this)\"";
                }
                $form_out.="><td>" . $v . "</td><td>" . $cr_comform[$k] . "</td></tr>";
                $kaunt++;
            }

            $form_out = "<script>var selected" . $bask_id . ";function selectRowEffect" . $bask_id . "(object) { if (!selected" . $bask_id . ") { if (document.getElementById) {
             selected" . $bask_id . " = document.getElementById('defaultSelected_delivery" . $bask_id . "'); } else { selected" . $bask_id . " = document.all['defaultSelected_delivery" . $bask_id . "']; }}
             if (selected" . $bask_id . ") selected" . $bask_id . ".className = 'tblrow'; object.className = 'tblrow_selected'; selected" . $bask_id . " = object; }</script>
                 <table cellpadding=5>" . $form_out . "</table>";
        } else {
            $form_out = $main;
        }
        return $form_out;
    }

    // форма оплаты
    function basket_form_payment($main = "", $bask_id = "", $savedpayment = "") {
        Debug::log();
        $form_out = "";
        if (is_array($main)) {
            $kaunt2 = 0;
            foreach ($main as $k => $v) {
                $formnames[] = "payment_bask[id_" . $bask_id . "]";
                $a = "id='payment" . $bask_id . "_" . $kaunt2 . "'";
                if ($v['flag'] <= 0) {
                    $a.=" disabled";
                    $formdescr2[] = "disabled";
                } else {
                    $formdescr2[] = "";
                }
                if (trim($v[1]) != "") {
                    $v[1] = " (" . $v[1] . ")";
                }
                $formdescr[] = $v[0] . "" . $v[1] . " <strong>" . $v['txt'] . "</strong>";
                $formtypes[] = "radio";
                $formvalues[] = $k . "_" . $v['skidka'] . "_" . @$v['after_flag'];
                if (($k . "_" . $v['skidka'] . "_" . @$v['after_flag']) == @$savedpayment) {
                    $a.=" checked";
                }
                $formattrs[] = $a;
                $kaunt2++;
            }
            $formnames[] = "payment_arr[id_" . $bask_id . "]";
            $formdescr[] = "[hid]";
            $formdescr2[] = "";
            $formtypes[] = "hidden";
            $formvalues[] = serialize($main);
            $formattrs[] = '';

            $cr_comform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
            $kaunt = 0;
            foreach ($formdescr as $k => $v) {
                if ($v === "[hid]") {
                    $form_out.="" . $cr_comform[$k] . "";
                    continue;
                }
                $form_out.="<tr class=\"tblrow\" onmouseover=\"rowOverEffect(this)\" onmouseout=\"rowOutEffect(this)\" ";
                if ($formvalues[$k] == @$savedpayment) {
                    $form_out.="id=\"defaultSelected_payment" . $bask_id . "\" ";
                }
                if ($formdescr2[$k] != "disabled") {
                    $form_out.="onclick=\"selectRowEffect_payment" . $bask_id . "(this)\"";
                }
                $form_out.="><td>" . $v . "</td><td>" . $cr_comform[$k] . "</td></tr>";
                $kaunt++;
            }

            $form_out = "<script>var selected_payment" . $bask_id . ";function selectRowEffect_payment" . $bask_id . "(object) { if (!selected_payment" . $bask_id . ") { if (document.getElementById) {
             selected_payment" . $bask_id . " = document.getElementById('defaultSelected_payment" . $bask_id . "'); } else { selected_payment" . $bask_id . " = document.all['defaultSelected_payment" . $bask_id . "']; }}
             if (selected_payment" . $bask_id . ") selected_payment" . $bask_id . ".className = 'tblrow'; object.className = 'tblrow_selected'; selected_payment" . $bask_id . " = object; }</script>
                 <table cellpadding=5>" . $form_out . "</table>";
        }
        return $form_out;
    }

    function basket_form_comments($bask_id = "", $savedselects = "") {
        Debug::log();
        $form_out = "";
        $formnames[] = "bask_comments[id_" . $bask_id . "]";
        $formdescr[] = "Телефон для связи, комментарии (по доставке, оплате, пожелания, уточнения)";
        $formtypes[] = "textarea";
        if (@$savedselects['bask_comments']['id_' . $bask_id] != "") {
            $cv = @$savedselects['bask_comments']['id_' . $bask_id];
        } else {
            $cv = @$_SESSION['telephone'];
        }
        $formvalues[] = @$cv;
        $formattrs[] = "rows=3 style='width:100%;'";
        $cr_comform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
        foreach ($formdescr as $k => $v) {
            $form_out.="<div class=\"basket_steps\">" . $v . "</div><hr class=\"divider\"><div class=\"basket_tbl_comments\">" . $cr_comform[$k] . "<p></p>";
        }
        return $form_out;
    }

    function basket_form_quickie($savedselects = "", $error_style = "") {
        Debug::log();
        $form_out = "";
        $formnames = array("order_quick_fio' id='order_quick_fio", "order_quick_email' id='order_quick_email", "order_quick_phone' id='order_quick_phone");
        $formtypes = array("text", "text", "text");
        $formvalues = array(@$savedselects['order_quick_fio'], @$savedselects['order_quick_email'], @$savedselects['order_quick_phone']);
        $formattrs = array("size=20", "size=20", "size=20");
        $formdescr = array("<div style=\"float:left;\">фио", "<div style=\"margin-left:10%;float:left;\">e-mail", "<div style=\"float:right;\">телефон");
        $cr_comform = formcreate($formnames, $formtypes, $formvalues, $formattrs);
        foreach ($formdescr as $k => $v) {
            $form_out.="" . $v . ": " . $cr_comform[$k] . "</div>";
        }
        return "<div class=\"basket_form_quickie\" " . $error_style . ">" . $form_out . "</div><div class=\"basket_form_quickie_txt\">
              Уважаемый покупатель! Используя эту форму, Вы можете оформить заказ без регистрации. Однако, если Вы планируете стать нашим постоянным покупателем,
              хотите отслеживать статус Вашего заказа, хотите в будущем получать <u>скидки</u>, мы рекомендуем Вам зарегистрироваться - это просто! Если же вы являетесь
              представителем юридического лица, зарегистрировавшись вы сможете указать свои реквизиты и ваыписать соответствующие счета.</div>";
    }

    function adm_form_login() {
        Debug::log();
        $out_frm = "<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
        $formnames = array("login' class='adm_input' size='10", "passwrd' class='adm_input' size='10", "remember_me", "referer_url", "referer_host", "a_logdone");
        $formvalues = array("", "", "", $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], "войти");
        $formdescr = array("логин", "пароль", "запомнить", "[hid]", "[hid]", "");
        $formtypes = array('text', 'password', 'checkbox', 'hidden', 'hidden', 'submit');
        $cr_loginform = formcreate($formnames, $formtypes, $formvalues);
        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_loginform[$k];
                continue;
            }
            if ($v != "") {
                $out_frm.="<span class='a_login_descr'>" . $v . ":<br/></span>";
            } else {
                $out_frm.="";
            }
            $out_frm.="" . $cr_loginform[$k] . "<br/><br/>";
        }
        $out_frm.="</form>";
        return $out_frm;
    }

    function adm_form_message($list_users = "") {
        Debug::log();
        $out_frm = "<form id=\"adm_msg_post\" class=\"adm_msg_form\" action=\"" . MAINURL_2 . "/user/done/\" method=\"post\">";
        if ($list_users != "") {
            foreach ($list_users as $k1 => $k2) {
                if (trim($k2) == "" || trim($k2) == @$_COOKIE['alog']) {
                    continue;
                }
                $out_frm.="<a class=\"replyname\" href=\"#\">@" . trim($k2) . "</a> ";
                $fl = 1;
            }
            if (@$fl == "1") {
                $out_frm.="<p></p>";
            }
        }
        $formnames = array("msgtxt' id='msgtxtarea' rows=4 style='width:85%;", "em_notify' id='emnot", "a_msgsend", "act");
        $formvalues = array("", "", " &nbsp;&rarr;&nbsp; ", MAINURL_2 . "/user/done/");
        $formdescr = array("", "на почту ", "", "[hid]");
        $formtypes = array('textarea', 'checkbox', 'submit', 'hidden');
        $cr_loginform = formcreate($formnames, $formtypes, $formvalues);
        foreach ($formdescr as $k => $v) {
            if ($v == "[hid]") {
                $out_frm.=$cr_loginform[$k];
                continue;
            }
            if ($formtypes[$k] == "checkbox") {
                $out_frm.="<font size=1>" . $v . "</font>" . $cr_loginform[$k] . " ";
            } else {
                $out_frm.="" . $cr_loginform[$k] . "<p></p>";
            }
        }
        $out_frm.="</form>";
        return $out_frm;
    }

    function adm_form_shopedit_catshop($nnn, $params, $statuses, $flagsubm = "1") {
        Debug::log();
        krsort($statuses['onoff']);
        foreach ($statuses['onoff'] as $k => $k2) {
            if ($k2 == "1") {
                $k3["s" . $k] = $k . ": " . strip_tags($statuses['descr'][$k]);
            }
        }
        $params['nazv']['0'] = "— несвязанный —";
        foreach ($params['nazv'] as $k => $k2) {
            unset($params['nazv'][$k]);
            $params['nazv']['p' . $k] = $k2;
        }
        $out_frm = "";

        if ($flagsubm == "1") {
            $out_frm.="<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\" id=\"adm_form_editshop\"><table id='edit1' cellpadding=5>";
        }

        unset($params['nnn'], $params['type'], $params['views'], $params['sort_cat'], $params['nums_cats'], $params['nums_prds'], $params['parent_flip']);
        $descr = array("nazv" => "Название магазина", "remote_addr" => "URL", "descr" => "Описание (небольшое)", "currency" => "Коэффициент или курс валюты (0 - откл.)",
            "parent" => "Привязка (при подключении внешнего магазина обязательно; 0 - самостоятельный)",
            "status" => "Способ взаимодействия с привязанным магазином", "remote_always" => "Включить переход на свой сайт", "admin_email" => "E-mail администратора");
        $types = array("nazv" => "text", "remote_addr" => "text", "descr" => "textarea", "currency" => "text",
            "parent" => "select", "status" => "select", "remote_always" => "checkbox", "admin_email" => "text");
        $attrs = array("nazv" => "size='30'", "remote_addr" => "size='30'", "descr" => "cols=30", "currency" => "",
            "parent" => "", "status" => "style='font-size:0.7em;'", "remote_always" => "", "admin_email" => "size=30");
        foreach ($params as $k => $k2) {
            $formnames[] = $k;
            if ($k == "status") {
                $formvalues[] = array_merge(array("s" . $k2[$nnn] => $k3["s" . $k2[$nnn]]), $k3);
            } else {
                if ($k == "parent") {
                    $formvalues[] = array_merge(array("p" . $k2[$nnn] => $params['nazv']["p" . $k2[$nnn]]), $params['nazv']);
                } else {
                    if ($k == "nazv") {
                        $formvalues[] = $k2["p" . $nnn];
                    } else {
                        $formvalues[] = $k2[$nnn];
                    }
                }
            }
            $formdescr[] = $descr[$k];
            $formtypes[] = $types[$k];
            $formattrs[] = $attrs[$k];
        }

        $formnames[] = "shop2edit";
        $formvalues[] = $nnn;
        $formdescr[] = "[hid]";
        $formtypes[] = "hidden";
        $formattrs[] = "";
        $cr_loginform = formcreate($formnames, $formtypes, $formvalues, $formattrs);

        foreach ($formdescr as $k => $v) {
            if ($v === "[hid]") {
                $out_frm.=$cr_loginform[$k];
                continue;
            }
            if ($v != "") {
                $out_frm.="<tr><td class='adm_shop_edit_descr' valign=top>" . $v . ":</td>";
            } else {
                $out_frm.="<td></td>";
            }
            $out_frm.="<td valign=top style='padding-left:15px;'>" . $cr_loginform[$k] . "</td></tr>";
        }
        $out_frm.="<input type=hidden name=a_editshop_hid value='go'>";
        if ($flagsubm == "1") {
            $out_frm.="</table><br/><input type='submit' name='a_editshop'  value='сохранить изменения'></form>";
        }
        return $out_frm;
    }

    function adm_form_shopedit_configuration($nnn, $params, $flagsubm = "1", $confnnn = "") {
        Debug::log();
        $out_frm = "";
        if (count($params) > 0) {
            $kkk = 0;
            foreach ($params as $k => $k2) {
                $formnames[] = "conf_nnn_" . $k2['nnn'];
                $formvalues[] = strtr($k2['conf_val'], array("{" => "[#>]", "}" => "[<#]"));
                $formdescr[] = "<a name=conf_" . $k . "></a><span class='adm_shopedit_conf_number'>" . $k2['nnn'] . "</span>&nbsp;<span class='adm_shopedit_conf_key'>" . txt_cut($k, 15, 'abbr') . "</span><p></p>" . $k2['descr'];
                $formtypes[] = "textarea";
                $formattrs[] = "cols=35 rows=5";
                $formdescr2[] = $k2['nnn'];
                $formdescr3[] = $k;
                $kkk++;
            }

            $cr_loginform = formcreate($formnames, $formtypes, $formvalues, $formattrs);

            if ($flagsubm == "1") {
                $out_frm = "<table cellpadding=5>";
            }
            foreach ($formdescr as $k => $v) {
                if ($confnnn != "" & @$flagsubm != "1") {
                    if ($confnnn == $formdescr2[$k]) {
                        
                    } else {
                        continue;
                    }
                }
                if ($v === "[hid]") {
                    $out_frm.=$cr_loginform[$k];
                    continue;
                }
                if ($flagsubm == "1") {
                    if ($v != "") {
                        $out_frm.="<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\" class=\"adm_form_post_editshop_conf\">
                    <tr><td class='adm_shop_edit_descr' valign=top>" . $v . "</td>";
                    } else {
                        $out_frm.="<td></td>";
                    }
                    $out_frm.="<td valign=top id='edit4_" . @$formdescr2[$k] . "' style='padding-left:15px;'>";
                }
                $out_frm.=$cr_loginform[$k];
                $out_frm.="<input type=hidden name='a_editshop_conf_hid_key' value='" . @$formdescr3[$k] . "'><input type=hidden class='aeditshopconfhid' name='a_editshop_conf_hid' value='" . @$formdescr2[$k] . "'><input type=hidden name='shop2edit' value='" . @$nnn . "'>";
                if ($flagsubm == "1") {
                    $out_frm.="</td><td valign=top><input type=submit name='a_editshop_conf' value='сохр " . @$formdescr2[$k] . "'></td>
                    </tr></form>";
                }
            }
            if ($flagsubm == "1") {
                $out_frm.="<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\" class=\"adm_form_post_editshop_conf\">
                    <tr><td class='adm_shop_edit_descr' valign=top>1</td>
                        <td valign=top id='edit4_' style='padding-left:15px;'>
                            <textarea></textarea>
                            <input type=hidden name='a_editshop_conf_hid_key' value=''>
                            <input type=hidden class='aeditshopconfhid' name='a_editshop_conf_hid' value=''>
                            <input type=hidden name='shop2edit' value='" . @$nnn . "'></td>
                        <td valign=top>
                            <input type=submit name='a_editshop_conf' value='сохр ???'></td>
                    </tr></form></table>";
            }
            return $out_frm;
        }
    }

    function adm_form_shopedit_statuses_ob($nnn, $params, $what = "b", $ext_shop = "", $flagsubm = "1") {
        Debug::log();

        if ($params == "0") {
            $out_frm = "У магазина нет индивидуальных статусов. Его статусы определяются магазином <a href={MAINURL_ADM}/shops/edit" . $ext_shop[0]['conf_val'] . ">" . $ext_shop[1] . "</a>";
            return $out_frm;
        }

        $out_frm = "";
        if ($flagsubm == "1") {
            $out_frm.="<form action=\"" . MAINURL_2 . "/user/done/\" method=\"post\" class=\"adm_form_post_editshop_stats\">";
        }
        $types = array("status_id" => "hidden", "status_name" => "text", "sort" => "text", "status_color" => "text", "flag_first" => "checkbox",
            "flag_quick" => "checkbox", "flag_error" => "checkbox", "flag_finish" => "checkbox");
        $attrs = array("sort" => "size=3", "status_name" => "size=40", "status_color" => "size=7");
        if ($params != "0") {
            $params[@$what] = @array_merge(@$params[$what], @$params[@$what . "_new"]);
        }
        if (count($params[$what]) > 0) {
            foreach ($params[$what] as $k => $k2) {
                $formnames[] = "del[" . $k . "]";
                $formvalues[] = "0";
                $formdescr = "";
                $formtypes[] = "checkbox";
                $formattrs[] = "";
                foreach ($k2 as $k3 => $k4) {
                    if (!isset($types[$k3])) {
                        continue;
                    }
                    if ($k3 == "status_color") {
                        if ($k4 == "") {
                            $k4 = "#eeeeee";
                        }
                        $formnames[] = $k3 . "[" . $k . "]' class='adm_status_color' style='border-width:0px; background-color:" . $k4 . ";";
                    } else {
                        $formnames[] = $k3 . "[" . $k . "]";
                    }

                    $formvalues[] = $k4;
                    $formdescr[] = "";
                    $formtypes[] = $types[$k3];
                    $formattrs[] = @$attrs[$k3];
                }
            }
        }

        if ($what == "b") {
            $f2 = 5;
            $f = "a_editshop_bills";
        } else {
            $f2 = 8;
            $f = "a_editshop_statuses";
        }

        $formnames[] = "shop2edit";
        $formvalues[] = $nnn;
        $formdescr[] = "[hid]";
        $formtypes[] = "hidden";
        $formattrs[] = "";

        $cr_loginform = formcreate($formnames, $formtypes, $formvalues, $formattrs);

        $tr_count = 0;
        if ($what == "b") {
            $out_frm.="<table id='edit3' cellspacing=0 cellpadding=5><tr><td class=tbl_zag_del>del</td><td class=tbl_zag>статус</td><td class=tbl_zag>&uarr;&darr;</td>
                         <td class=tbl_zag>нов<br/>ый</td><td class=tbl_zag>после<br/>дний</td></tr><tr>";
        } else {
            $out_frm.="<table id='edit2' cellspacing=0 cellpadding=5><tr><td class=tbl_zag_del>del</td><td class=tbl_zag>статус</td><td class=tbl_zag>&uarr;&darr;</td><td class=tbl_zag>цвет</td>
                         <td class=tbl_zag>нов<br/>ый</td><td class=tbl_zag>быст<br/>рый</td><td class=tbl_zag>оши<br/>бка</td><td class=tbl_zag>после<br/>дний</td></tr><tr>";
        }
        foreach ($cr_loginform as $k2 => $v2) {
            $tr_count++;
            if ($formnames[$k2] == $f) {
                $out_frm.="</table><br>";
                $flag_skip_table = 1;
            }
            if ($formtypes[$k2] == "hidden") {
                $out_frm.=$v2;
            } else {
                if (@$flag_skip_table == "1") {
                    $out_frm.=$v2;
                } else {
                    $out_frm.="<td align=center class=tbl_stat_td>";
                    if ($formtypes[$k2] == "checkbox") {
                        if ($formvalues[$k2] == "1") {
                            $out_frm.="<span class=tbl_chkbox_on>";
                        } else {
                            $out_frm.="<span class=tbl_chkbox_off>";
                        }
                    }
                    $out_frm.=$v2;
                    if ($formtypes[$k2] == "checkbox") {
                        $out_frm.="</span>";
                    }
                    $out_frm.="</td>";
                    $last = "td";
                }
            }

            if ($tr_count > $f2 && $flag_skip_table != "1") {
                $tr_count = 0;
                $out_frm.="</tr><tr>";
                $last = "tr";
            }
        }
        $out_frm.="<input type=hidden name=a_editshop_stats_hid value='" . $what . "'>";
        if ($flag_skip_table != "1") {
            $out_frm.="</tr></table>";
        }
        if ($flagsubm == "1") {
            $out_frm.="<br/><input type=submit name='" . $f . "' value='сохранить'></form>";
        }
        return $out_frm;
    }

}

?>