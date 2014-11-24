<?php

class pages {
    
    function update_views($page) { //
        Debug::log();
        mysql_call("UPDATE ".DB_PREFIX."pages SET views=views+1 WHERE nnn='".$page."'") or die(mysql_error());
        } // update views

    // add_page -> dat, shop_cat, nazv, txt, pic, comm_allow, show_nazv, show_in_list, show_in_last, show_in_news, show_in_parent, hid
    function add_page($dat, $postvars, $filevars) { Debug::log();
        $sql_ins_keys = "";
        $sql_ins_vals = "";
        foreach ($filevars as $k => $v) {
            if (count($filevars[$k]) > 0) {
                if ($filevars[$k]['error'] <> 0) {
                    
                } else {
                    $str_upl = file_upload($filevars[$k], MAINURL_5 . "/upload/", "jpg"); // глобальная функция upload
                    $postvars['pic'] = @$str_upl;
                }
            }
        }
        unset($postvars['addpage']);
        $postvars['dat'] = $dat;
        $txt = textprocess($txt);
        foreach ($postvars as $k => $v) {
            if ($v == "on") {
                $v = "1";
            } $sql_ins_keys = $sql_ins_keys . $k . ", ";
            $sql_ins_vals = $sql_ins_vals . "'" . $v . "', ";
        }

        $sql_ins_keys = substr($sql_ins_keys, 0, -2);
        $sql_ins_vals = substr($sql_ins_vals, 0, -2);
        $sql_ins = "INSERT INTO " . DB_PREFIX . "pages (" . $sql_ins_keys . ") VALUES (" . $sql_ins_vals . ")";

        mysql_kall($sql_ins) or die(mysql_error());
    }

    function del_page($nnn, $shop_cat) { Debug::log();
        mysql_kall("DELETE FROM " . DB_PREFIX . "pages WHERE nnn='" . $nnn . "' AND shop_cat='" . $shop_cat . "'");
    }

    function hid_page($nnn, $shop_cat) { Debug::log();
        mysql_kall("UPDATE " . DB_PREFIX . "pages SET hid='1' WHERE nnn='" . $nnn . "' AND shop_cat='" . $shop_cat . "'");
    }

    function unhid_page($nnn, $shop_cat) { Debug::log();
        mysql_kall("UPDATE " . DB_PREFIX . "pages SET hid='0' WHERE nnn='" . $nnn . "' AND shop_cat='" . $shop_cat . "'");
    }

    function edit_page($postvars, $filevars) { Debug::log();
        $sql_ins = "";
        $nnn = $postvars['nnn'];
        $shop_cat = $postvars['shop_cat'];
        foreach ($filevars as $k => $v) {
            if (count($filevars[$k]) > 0) {
                if ($filevars[$k]['error'] <> 0) {
                    
                } else {
                    $str_upl = file_upload($filevars[$k], MAINURL_5 . "/upload/", "jpg"); // глобальная функция upload
                    $postvars['pic'] = @$str_upl;
                }
            }
        }
        unset($postvars['editpage'], $postvars['nnn'], $postvars['shop_cat']);
        $checkbox = array("comm_allow" => "", "show_nazv" => "", "show_in_list" => "", "show_in_last" => "", "show_in_news" => "", "show_in_parent" => "", "hid" => "");
        if (isset($postvars['delpic']) && @$str_upl == "") {
            $postvars['pic'] = "";
            unset($postvars['delpic']);
        }
        foreach ($checkbox as $k => $v) {
            if (!isset($postvars[$k])) {
                $postvars[$k] = "0";
            }
        }
        foreach ($postvars as $k => $v) {
            if ($v == "on") {
                $v = "1";
            } $sql_ins = $sql_ins . $k . "='" . $v . "', ";
        }

        $sql_ins = substr($sql_ins, 0, -2);
        $sql_ins = "UPDATE " . DB_PREFIX . "pages SET " . $sql_ins . " WHERE nnn='" . $nnn . "' AND shop_cat='" . $shop_cat . "'";

        mysql_kall($sql_ins) or die(mysql_error());
    }

    //////////////////////////////////
    
    //////////////////////////////////
    
    //////////////////////////////////
    
    // страница: отображается только для сайта или для подрубрик/зависимых сайтов с разрешением
    function show_page($nnn, $shop_cat = SHOP_NNN) { Debug::log();

        $sql_whr = "AND (shop_cat='" . $shop_cat . "'";
        $temp_lst = cats("arr", $shop_cat);
        unset($temp_lst[$shop_cat]);
        if (count($temp_lst) > 0) {
            $sql_whr.=" OR (shop_cat IN ('" . implode("', '", array_keys($temp_lst)) . "') AND show_in_parent='1')";
        }
        $sql_whr .=")";

        $showpage = mysql_kall("SELECT nnn, dat,nazv,txt,pic,comm_allow,show_nazv FROM " . DB_PREFIX . "pages
            WHERE (nnn IN ('" . $nnn . "')) " . $sql_whr . " AND hid!='1'");
        $showpage_numrows = mysql_num_rows($showpage);
        if ($showpage_numrows > 0) {
            $page_content = mysql_fetch_assoc($showpage);
            if ($showpage_numrows <= 1) {
                if ($page_content['show_nazv'] != "1") {
                    unset($page_content['nazv']);
                }
                unset($page_content['show_nazv']);
                $v2 = explode("+++", $page_content['txt']);
                $page_content['txt'] = $v2[0];
                $page_content["txt_full"] = @$v2[1];
                return array($page_content['nnn']=>$page_content);
            } else {
                do {
                    if ($page_content['show_nazv'] != "1") {
                        unset($page_content['nazv']);
                    }
                    unset($page_content['show_nazv']);
                    $v2 = explode("+++", $page_content['txt']);
                    $page_content['txt'] = $v2[0];
                    $page_content["txt_full"] = @$v2[1];
                    $page_content2[$page_content['nnn']] = $page_content;
                } while ($page_content = mysql_fetch_assoc($showpage));
                return $page_content2;
            }
        }
    }

    // СПИСОК СТРАНИЦ: типы: list, last, news. limit только для last. main - смесь last и news для главной страницы!
    function show_pages($shop_cat = SHOP_NNN, $type = "list", $limit = "10") { Debug::log();

        $sql_whr = "AND (shop_cat='" . $shop_cat . "'";
        $temp_lst = cats("arr", $shop_cat);
        unset($temp_lst[$shop_cat]);
        if (count($temp_lst) > 0) {
            $sql_whr.=" OR (shop_cat IN ('" . implode("', '", array_keys($temp_lst)) . "') AND show_in_parent='1')";
        }
        $sql_whr .=")";

        // список страниц
        if ($type == "list") {
            $sql_whr = "SELECT " . DB_PREFIX . "pages.nnn,shop_cat," . DB_PREFIX . "pages.dat,nazv,pic,show_in_news,show_nazv, COUNT(c.nnn) as num_comms
            FROM " . DB_PREFIX . "pages LEFT OUTER JOIN " . DB_PREFIX . "pages_comments as c ON " . DB_PREFIX . "pages.nnn=" . DB_PREFIX . "c.pages_nnn
                 WHERE show_in_list='1' AND " . DB_PREFIX . "pages.hid!='1' " . $sql_whr . " GROUP BY " . DB_PREFIX . "pages.nnn
                     ORDER BY " . DB_PREFIX . "pages.dat DESC LIMIT 100";
        }

        // список новостей
        if ($type == "news") {
            $sql_whr = "SELECT nnn,shop_cat,dat,nazv,txt,pic,show_in_news FROM " . DB_PREFIX . "pages
            WHERE show_in_news='1' AND hid!='1' " . $sql_whr . " ORDER BY sort_order ASC, dat DESC";
        }

        // список последних
        if ($type == "last") {
            $sql_whr = "SELECT nnn,shop_cat,dat,nazv,pic,show_in_news FROM " . DB_PREFIX . "pages
            WHERE show_nazv='1' AND show_in_last='1' AND hid!='1' " . $sql_whr . " ORDER BY dat DESC LIMIT " . $limit . "";
        }

        if ($type == "main") {
            $sql_whr = "(SELECT sort_order,nnn,shop_cat,dat,nazv,txt,pic,show_in_news FROM " . DB_PREFIX . "pages
            WHERE show_nazv='1' AND show_in_last='1' AND hid!='1' " . $sql_whr . " ORDER BY dat DESC LIMIT " . $limit . ") UNION
                  (SELECT sort_order,nnn,shop_cat,dat,nazv,txt,pic,show_in_news FROM " . DB_PREFIX . "pages
                      WHERE show_in_news='1' AND hid!='1' " . $sql_whr . " ORDER BY sort_order ASC, dat DESC LIMIT " . NEWS_LIMIT_ON_MAIN . ")";
        }

        if ($sql_whr != "") {
            $showpages = mysql_kall($sql_whr) or die(mysql_error());
            if (mysql_num_rows($showpages) > 0) {
                $showpages2 = mysql_fetch_assoc($showpages);
                do {
                    $imp_key = $showpages2['show_in_news'];
                    if ($type == "last") {
                        $imp_key = 0;
                    }
                    if ($type == "list") {
                        $imp_key = 0;
                    }
                    foreach ($showpages2 as $k => $v) {
                        if ($k == "nnn") {
                            continue;
                        }
                        if ($k == "txt") {
                            $v2 = explode("+++", $v);
                            $v = $v2[0];
                            $pages_content[$imp_key][$showpages2['nnn']]["txt_full"] = @$v2[1];
                        }
                        $pages_content[$imp_key][$showpages2['nnn']][$k] = $v;
                    }
                } while ($showpages2 = mysql_fetch_assoc($showpages));
            }
            return $pages_content;
        }
    }

    // комментарии
    function comments($nnn = "all", $allow = "1") { Debug::log();
        if ($allow == "1") {
            if ($nnn == "all") {
                $showpage = mysql_kall("SELECT * FROM " . DB_PREFIX . "pages_comments WHERE hid!='1' ORDER BY dat DESC");
            } else {
                $showpage = mysql_kall("SELECT * FROM " . DB_PREFIX . "pages_comments WHERE pages_nnn='" . $nnn . "' AND hid!='1' ORDER BY dat ASC");
            }
            if (mysql_num_rows($showpage) > 0) {
                $page_content = mysql_fetch_assoc($showpage);
                do {
                    $page_content2[] = $page_content;
                } while ($page_content = mysql_fetch_assoc($showpage));
                return $page_content2;
            }
        }
    }

    // добавление комментария
    function add_comment($post) { // pages_nnn, avtor, registered, txt, dat,
        Debug::log();
        if (trim($_SESSION['number1']) != "" && trim($post['vercode']) != "") {
            if ($_SESSION['number1'] == mb_strtolower($post['vercode'])) {
                if ($post['txt'] != "") {
                    if (isset($_COOKIE[SHOP_NNN . '_timeout']) && (time() < @$_COOKIE[SHOP_NNN . '_timeout'])) {
                        $_SESSION['send_login_message'] = "Перед написанием следующего комментария, подождите, пожалуйста еще <b>" . ($_COOKIE[SHOP_NNN . '_timeout'] - time()) . "</b> сек.";
                    } else {
                        $timeout0 = time() + (60 * COMMENTS_TIMEBREAK);
                        setcookie(SHOP_NNN . "_timeout", $timeout0, "0", "/", MAINURL_4);

                        if (isset($_SESSION['customers_id'])) {
                            $avtorhid = @$_COOKIE['firstname'] . " " . @$_COOKIE['lastname'];
                        } else {
                            $avtorhid = "";
                        }
                        if (@$post['avtor'] == "" && $avtorhid == "") {
                            $post_avtor = ANONIM_NAME;
                        }
                        if (@$post['avtor'] == "" && $avtorhid != "") {
                            $post_avtor = $avtorhid;
                        }
                        if (@$post['avtor'] != "") {
                            $post_avtor = $post['avtor'];
                        }

                        mysql_call("INSERT INTO " . DB_PREFIX . "pages_comments (pages_nnn, avtor, registered, txt, dat, hid, customers_id)
                         VALUES ('" . $post['pages_nnn'] . "','" . textprocess($post_avtor, 'sql') . "','" . @$post['registered'] . "','" . textprocess($post['txt'], 'sql') . "','" . $post['dat'] . "','" . COMMENT_APPEAR_DEF . "','" . @$_SESSION['customers_id'] . "')");
                        $_SESSION['send_login_message'] = "Комментарий добавлен.";
                    }
                } else {
                    $_SESSION['send_login_message'] = "Комментарий не добавлен, т.к. его нет.";
                }
            } else {
                $_SESSION['send_login_message'] = "Неверный проверочный код.";
            }
        } else {
            $_SESSION['send_login_message'] = "Проверочный код не указан.";
        }
    }

    function prepare_news($news_list, $autocollapse = NEWS_AUTOCOLLAPSE, $newsimp = NEWS_IMPORTANT, 
            $css_arr = array("news_dat", "news_pic", "news_txt", "news", "news_imp")) {
        Debug::log();
        $n4 = 0;
        if(count($news_list)>0) {
        foreach ($news_list as $n1 => $n2) {
            $n4++;
            $n3 = "<span class=\"" . $css_arr[0] . "\">" . date("d.m.Y", $news_list[$n1]['dat']) . "<p></p></span>";
            if ($news_list[$n1]['pic'] != "" && $n4 <= $autocollapse) {
                $n3.="<div class=\"" . $css_arr[1] . "\"><img src=\"" . imgprocess(MAINURL . "/upload/pages/" . $news_list[$n1]['pic'], "0", "100", "1", "upload/pages/thumb/") . "\"></div>";
            }
            $x = explode("<p style=\"margin:7 0 0 0;padding:0;\">", $news_list[$n1]['txt']);
            for ($j = 0; $j < count($x); $j++) {
                if (substr($x[$j], 0, 7) == "http://") {
                    $x2 = explode("</p>", $x[$j]);
                    $x2[0] = "<a href=" . $x2[0] . ">$x2[0]</a>" . @$x2[1];
                    unset($x2[1]);
                    $x[$j] = implode("</p>", $x2);
                }
            } $news_list[$n1]['txt'] = implode(" ", $x);

            if ($n4 > $autocollapse) {
                $news_list[$n1]['txt'] = "<a href=" . MAINURL . "/page/" . $n1 . ">" . substr(strip_tags($news_list[$n1]['txt']), 0, 45) . "...</a>";
            }

            $n3.="<span class=\"" . $css_arr[2] . "\"><p></p>" . $news_list[$n1]['txt'] . "</span>";
            if (trim($news_list[$n1]['txt_full']) != "" && $n4 <= $autocollapse) {
                $n3.="<span class=\"" . $css_arr[2] . "\"><a href=" . MAINURL . "/page/" . $n1 . ">подробнее</a></span>";
            }
            if ($n4 > $newsimp) {
                $news_arr['all'] = @$news_arr['all'] . "<div class=\"" . $css_arr[3] . "\">" . $n3 . "</div>";
            } else {
                $news_arr['imp'] = @$news_arr['imp'] . "<div class=\"" . $css_arr[4] . "\">" . $n3 . "</div>";
            }
        }}
        return $news_arr;
    }
}
?>