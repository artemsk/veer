<?php

class categories {

    function show_tree($i = SHOP_NNN, $curr = "", $search = "") { // дерево для левой колонки; $ret=full, simple
        Debug::log();
        // используем кэш, если не прошло заданного времени
        if ((time() - (60 * CACHE_TIMEOUT)) <
                @$_SESSION['cache']['c_tree'][SHOP_NNN][@$curr['nnn']]['time']) {
            return $this->generate_ajax(@$_SESSION['cache']['c_tree'][SHOP_NNN][@$curr['nnn']]['html'], @$curr['nnn'], $search);
        }

        $tree = "";
        if (@$curr['nadrazdel'] != "" && is_array($curr['nadrazdel'])) {
            $curr['nadrazdel'] = array_reverse($curr['nadrazdel'], true);
            unset($curr['nadrazdel'][SHOP_NNN]);
        }
        $look = $this->gather($i, "full");
        $curr_level = 1;
        $div_k = 0;
        $div_e_k = 0;
        $div_k2 = 0;
        $div_e_k2 = 0;
        $js_arr = "";

        if (count($look['podrazdel']) > 0) {
            foreach ($look['podrazdel'] as $k => $v) {
                $test[$v['level']] = $k;

                if ($v['level'] > $curr_level) {
                    $backup_curr_level2[$v['level']] = $backup_curr_level;
                    if (@$test[$v['level'] - 2] <= 0) {
                        $test2 = "0";
                    } else {
                        $test2 = $test[$v['level'] - 2];
                    }
                    $tree_plus[$backup_curr_level] = "<div class='cat_level_plus' style='float:left;'><a class=\"nav\" href=\"" .
                            @$curr['nnn'] . "_" . $backup_curr_level . ".html\" onClick=\"jumpToAnchor()\">+</a></div>";
                    $tree_plus_alt[$backup_curr_level] = "<div class='cat_level_minus' style='float:left;'><a class=\"nav\" href=\"" .
                            @$curr['nnn'] . "_" . $test2 . ".html\" onClick=\"jumpToAnchor()\">&uarr;</a></div>";
                }
                $curr_level = $v['level'];
                $backup_curr_level = $k;
                if ($v['remote_addr'] != "" && $v['remote_always'] == "1") {
                    $lnk = $v['remote_addr'];
                } else {
                    $lnk = MAINURL . "/catalog/" . $k;
                }
                $tree_k["{" . $k . "}"] = "<a href=\"" . $lnk . "\">" . $v['nazv'] . "</a><br>";
                if ($k == @$curr['nnn'] && @$curr != "") {
                    $tree_k["{" . $k . "}"] = "<span class='cat_current'>" . $tree_k["{" . $k . "}"] . "</span>";
                    $sty = "cat_level_curr";
                } else {
                    $sty = "cat_level";
                }
                $tree_plus[$k] = "<div class='" . $sty . "' style='float:left;'>&rarr;</div>";
                $tree_k_dva[$backup_curr_level2[$v['level']]] = @$tree_k_dva[$backup_curr_level2[$v['level']]] . "{" . $k . "}";
                $connections[$k] = $backup_curr_level2[$v['level']];
                $js_arr_0[$backup_curr_level2[$v['level']]] = "hide('sublevel_" . $backup_curr_level2[$v['level']] . "');";
                $level_remember[$k] = $v['level'];
            }
        }
        $js_arr = implode("", $js_arr_0);
        foreach ($tree_plus as $k => $v) {
            $tree_k_alt["{" . $k . "}"] = "<div class='cat_level_yes'>" . $tree_plus_alt[$k] . $tree_k["{" . $k . "}"] . "</div>";
            $tree_k["{" . $k . "}"] = "<div class='cat_level_yes'>" . $v . $tree_k["{" . $k . "}"] . "</div>";
        }

        foreach ($tree_k_dva as $k => $v) {
            if (isset($curr['nadrazdel'])) {
                $openit = "none"; //if(array_key_exists($k,$curr['nadrazdel'])) { $openit="block"; }
                if ($k == $curr['nnn']) {
                    $openit = "block";
                }
                if ($k == $connections[$curr['nnn']] && !isset($tree_k_dva[$curr['nnn']])) {
                    $openit = "block";
                }
            } else {
                if ($k == "") {
                    $openit = "block";
                } else {
                    $openit = "none";
                }
            }
            $tree_k_dva[$k] = "<div id=\"sublevel_" . $k . "\" style=\"display:" . $openit . ";\">" .
                    $tree_k_alt["{" . $connections[$k] . "}"] . $tree_k_alt["{" . $k . "}"] . "<div class=\"div_cat_level_2\">" .
                    strtr($v, $tree_k) . "</div></div>";
            if ($openit != "none") {
                $smart[$k] = $tree_k_dva[$k];
            }
        }
        $tree_k_dva2 = implode("", $tree_k_dva);

       // $_SESSION['cache']['c_tree'][SHOP_NNN][@$curr['nnn']]['html'] = $tree_k_dva2;
       // $_SESSION['cache']['c_tree'][SHOP_NNN][@$curr['nnn']]['time'] = time();

        return $this->generate_ajax($tree_k_dva2, @$curr['nnn'], $search);
    }

    function generate_ajax($str, $currnnn, $search = "") {
        Debug::log();
        if (is_array($search)) {
            $lookout = $search[2];
            $lookout_openit = "none;";
        } else {
            $lookout = $currnnn;
            $lookout_openit = "block;";
        }
        if ($lookout <= 0) {
            $lookout = "";
        }
        if ($lookout == SHOP_NNN) {
            $lookout = "";
        }
        // "_".$lookout."_ -> ".$lookout_openit."<textarea>".$str."</textarea><br>";
        $str_test = explode("<div id=\"sublevel_" . $lookout . "\" style=\"display:" . $lookout_openit . "\">", $str);
        if (count($str_test) > 1) {
            $str_test2 = explode("<div id=\"sublevel_", $str_test[1]);
            return substr(@$str_test2[0], 0, -6);
        } return $str;
    }

    function gather($i = SHOP_NNN, $gather_type = "brief") { // gather_type - brief(все только для над разделов), full(все дерево)
        Debug::log();
        if (isset($_SESSION['cache']['cats_full_tree'][$gather_type][SHOP_NNN][$i])) {
            return $_SESSION['cache']['cats_full_tree'][$gather_type][SHOP_NNN][$i];
        }

        $exclude_sql = cats();
        $exclude_sql = strtr($exclude_sql, array("products_2_cats.shop_cat" => "catshop_config.nnn"));

        $cats = mysql_kall("SELECT * FROM " . DB_PREFIX . "catshop_config WHERE " . DB_PREFIX . "catshop_config.nnn='" . $i . "' " . @$exclude_sql . "");

        if (mysql_num_rows($cats) > 0) {
            $gcl = cats_tree();
            $cats2 = mysql_fetch_assoc($cats);

            $zz[] = $i;
            $level = 0;
            for ($j = 0; $j <= 1000; $j++) {
                $ii = $zz[$j];
                $level++;
                $last_ii = $zz[$j];

                $cats4 = @$gcl['parent'][$ii];
                if (count($cats4) > 0) {

                    foreach ($cats4 as $k => $v) {
                        $zz[] = $k;
                        $podrazdel[$k]['nazv'] = $gcl[$k]['nazv'];
                        $podrazdel[$k]['remote_addr'] = $gcl[$k]['remote_addr'];
                        $podrazdel[$k]['type'] = $gcl[$k]['type'];
                        $podrazdel[$k]['level'] = @$podrazdel[$gcl[$k]['parent']]['level'] + 1;
                        $podrazdel[$k]['remote_always'] = $gcl[$k]['remote_always'];
                        $sort_cat_lvl[$podrazdel[$k]['level']][$k] = $gcl[$k]['sort_cat'];
                        $tree[$k] = $gcl[$k]['parent'];
                    }
                } else {
                    if ($j == (count($zz) - 1)) {
                        break;
                    }
                }

                if ($gather_type == "brief") {
                    break;
                }
            }

            if (isset($tree)) {
                $cats2['tree'] = @$tree;
            }
            if (isset($sort_cat_lvl)) {
                $cats2['sort_cat_lvl'] = @$sort_cat_lvl;
            }

            $jj[] = $i;
            $flag_br = 0;
            for ($j = 0; $j <= 1000; $j++) {
                $ii = $jj[$j];
                $cats4 = @$gcl[$ii];

                if (count($cats4) > 0) {
                    if ($ii != $i) { // тек раздел не записываем
                        $nadrazdel[$ii]['nazv'] = $cats4['nazv'];
                        $nadrazdel[$ii]['remote_addr'] = $cats4['remote_addr'];
                        $nadrazdel[$ii]['remote_always'] = $cats4['remote_always'];
                        $nadrazdel[$ii]['type'] = $cats4['type'];
                    }
                    $jj[] = $cats4['parent'];
                    if ($ii == SHOP_NNN) {
                        $flag_br = 1;
                    }  // доходим только до тек. магазина
                } else {
                    if ($j == (count($jj) - 1)) {
                        break;
                    }
                }
                if (@$flag_br == "1") {
                    break;
                }
            }
            if (isset($nadrazdel)) {
                $cats2['nadrazdel'] = @$nadrazdel;
            }

            // resort
            if (count(@$sort_cat_lvl) > 0) {
                krsort($sort_cat_lvl);
                unset($maxlevel);
                foreach ($sort_cat_lvl as $k => $v) {
                    asort($v);
                    if (!isset($maxlevel)) {
                        $maxlevel = $k + 1;
                    }
                    foreach ($v as $kk => $vv) {
                        $search_top[0] = $kk;
                        $nulls = $kk;
                        $str = "";

                        if (strlen($nulls) < 5) {
                            $v3 = $nulls;
                            $v2 = strlen($nulls);
                            for ($o = 1; $o <= (5 - $v2); $o++) {
                                $v3 = "0" . $v3;
                            } $str = $v3;
                        }

                        for ($j = 0; $j < $k; $j++) {

                            $nulls = $sort_cat_lvl[$k - $j][$search_top[$j]];
                            if (strlen($nulls) < 5) {
                                $v3 = $nulls;
                                $v2 = strlen($nulls);
                                for ($o = 1; $o <= (5 - $v2); $o++) {
                                    $v3 = "0" . $v3;
                                }
                                $v4 = $v3;
                            }

                            $search = $search_top[$j];
                            if (isset($tree[$search])) {
                                $search_top[] = $tree[$search];
                                $nulls = $tree[$search];
                                if (strlen($nulls) < 5) {
                                    $v3 = $nulls;
                                    $v2 = strlen($nulls);
                                    for ($o = 1; $o <= (5 - $v2); $o++) {
                                        $v3 = "0" . $v3;
                                    }
                                    $str = $v3 . $v4 . $str;
                                }
                            }
                        }

                        unset($search_top);
                        if (strlen($str) < (5 * (($maxlevel * 2) - 1))) {
                            $str2 = $str;
                            $v2 = strlen($str);
                            for ($o = 1; $o <= (((($maxlevel * 2) - 1) * 5) - $v2); $o++) {
                                $str2 = $str2 . "0";
                            }
                            $str = $str2;
                        }

                        $real_sort[$kk] = $str;
                    }
                }
            }

            if (isset($real_sort)) {
                $cats2['real_sort'] = $real_sort;
                asort($real_sort, SORT_STRING);
                foreach ($real_sort as $k => $v) {
                    $cats2['podrazdel'][$k] = $podrazdel[$k];
                }
            }
            unset($cats2['tree'], $cats2['sort_cat_lvl'], $cats2['real_sort']);

            //$_SESSION['cache']['cats_full_tree'][$gather_type][SHOP_NNN][$i] = $cats2;

            return $cats2;
        }
    }

}

// attributes
class attribs {

    // TODO: при выборе свойства показывать сколько товаров у каждого из значений
    function gather($what = "all", $shop_cat = SHOP_NNN, $limit = "") { // all - все существующие свойства или все значения указанного свойства
        Debug::log();
        $what2 = strtr($what, array("\"" => "", "/" => "_", "'" => "", "." => "", "," => "", "?" => "", "!" => "", "^" => "", "`" => "", "*" => "", ";" => "", ":" => "", " " => ""));
        if ($limit == "") {
            $limit2 = "";
        } else {
            $limit2 = "LIMIT " . $limit;
        }
        $add_sql = cats("sql", $shop_cat);
        if ($what == "all") {
            $excl = "";
            if (EXCLUDE_ATTRS != "") {
                $excl = explode(",", EXCLUDE_ATTRS);
                array_walk($excl, 'trim_blank');
                $excl = implode("' AND attr_name!='", $excl);
            }
            $sel_sql = mysql_kall("SELECT attr_name, " . DB_PREFIX . "products_attr.nnn FROM " . DB_PREFIX . "products_attr, " . DB_PREFIX . "products_2_cats WHERE attr_name!='" . $excl . "' AND attr_val!='' AND " . DB_PREFIX . "products_attr.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " GROUP BY attr_name ORDER BY attr_name ASC " . $limit2 . "") or die(mysql_error()); // тормозная или нет?
            $sel_var = "attr_name";
        } else {
            $sel_sql = mysql_kall("SELECT attr_val, " . DB_PREFIX . "products_attr.nnn FROM " . DB_PREFIX . "products_attr, " . DB_PREFIX . "products_2_cats WHERE attr_name='" . $what . "' AND attr_val!='' AND " . DB_PREFIX . "products_attr.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " GROUP BY attr_val ORDER BY attr_val ASC " . $limit2 . "") or die(mysql_error());
            $sel_var = "attr_val";
        }
        $sel = mysql_fetch_assoc($sel_sql);
        if (mysql_num_rows($sel_sql) > 0) {
            do {
                $main[$what][$sel['nnn']] = $sel[$sel_var];
            } while ($sel = mysql_fetch_assoc($sel_sql));
            return $main;
        }
    }

}

// keyws
class keyws {

    function gather($shop_cat = SHOP_NNN, $limit = "10") { // all - все существующие ключ слова
        Debug::log();
        $add_sql = cats("sql", $shop_cat);
        $limit_s = $limit;
        if ($limit <= 0) {
            $order_type = "keyword ASC";
        } else {
            $order_type = "SUM(4sum) DESC";
        }
        $sel_sql = mysql_kall("SELECT " . DB_PREFIX . "products_keywords.nnn, keyword, SUM(4sum) FROM " . DB_PREFIX . "products_keywords,
            " . DB_PREFIX . "products_2_cats WHERE keyword!='' AND " . DB_PREFIX . "products_keywords.products_nnn=" .
                DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " GROUP BY keyword ORDER BY " . $order_type . "") or die(mysql_error());
        // TODO: очень тормозная
        $sel = mysql_fetch_assoc($sel_sql);
        $str = "";
        if (mysql_num_rows($sel_sql) > 0) {
            $flag_max = 0;
            $maxnum = 500;
            do {
                if ($flag_max != "1" && $limit > 0) {
                    $maxnum = $sel['SUM(4sum)'];
                    $flag_max = 1;
                }
                $sz = round(1 * $sel['SUM(4sum)'] / $maxnum, 2);
                if ($sz < 0.6) {
                    $sz = 0.6;
                }
                $str = $str . " &nbsp;<a href=" . MAINURL . "/keyword/" . $sel['nnn'] . "><span style='font-size:" . $sz . "em;'>" .
                        $sel['keyword'] . "</span></a>";
                $main2[$sel['keyword']] = "<a href=" . MAINURL . "/keyword/" . $sel['nnn'] . "><span style='font-size:" . $sz . "em;'>" .
                        wordwrap($sel['keyword'], 30) . "</span></a>";
                //$main[$sel['keyword']]=$sel['SUM(4sum)'];
                if ($limit > 0) {
                    $limit_s--;
                    if ($limit_s <= 0) {
                        break;
                    }
                } // если limit=0 то все
            } while ($sel = mysql_fetch_assoc($sel_sql));

            if ($limit <= 0) {
                $str = "<div class='all_keywords_column'>";
                $main3 = ceil(count($main2) / 3);
                $limit_s = 0;
                $column_count = 0;
                foreach ($main2 as $k => $v) {
                    $limit_s++;
                    $str = $str . $v . "<p></p>";
                    if ($limit_s >= $main3) {
                        $str.="</div><div class='all_keywords_column'>";
                        $column_count++;
                        $limit_s = 0;
                    }
                }
                if ($column_count < 3) {
                    $str.="</div>";
                }
            }
        }
        return $str;
    }

}

class ratings {

    function recomend($c_id = "0", $type = "visits", $limit = "10", $prd_one_flag = "") { // visits!, keywords! orders, search, random?
        //// рекомендуемые товары, type-> visits, orders, search (все скомбинировано должно быть или нет?)
        //// если с_id=0 то "сейчас смотрят на сайте")
        Debug::log();
        $limit = LIMIT_PRD_RECOMENDATIONS + $limit;
        if ($c_id <= 0) {
            $c_id = "0";
        }

        // РЕКОМЕНДАЦИИ: VISITS
        if ($prd_one_flag == "" && $type == "visits") { // TODO: делаем !visits чтобы не было лишнего запроса к бд
            if (@$_SESSION['cache']['recomend']['visits'][SHOP_NNN][$c_id] != "") {
                return array("lst" => unserialize($_SESSION['cache']['recomend']['visits'][SHOP_NNN][$c_id]),
                    "zag" => RECOMEND_NAME_SIMILAR_VISITS);
                //break;
            }
        }

        $add_sql = cats();

        // ИСТОЧНИКИ ТОВАРОВ ///////////////////////////////////////////////////
        if ($prd_one_flag == "" && $type != "visits") {
            // ORDER BY SUM(nums) DESC / by lastmodified DESC
            $sql_whr = "SELECT " . DB_PREFIX . "customers_lastvisits.* FROM " . DB_PREFIX . "customers_lastvisits, " . DB_PREFIX . "products_2_cats
                        WHERE " . DB_PREFIX . "customers_lastvisits.catshop_prd_id=" . DB_PREFIX . "products_2_cats.products_nnn
                            AND type='/product/' AND customers_id='" . $c_id . "' AND catshop_prd_id!='0' " . $add_sql . " 
                                GROUP BY catshop_prd_id ORDER BY SUM(nums) DESC, lastmodified DESC LIMIT " . $limit . " ";

            $s1 = mysql_kall($sql_whr) or die(mysql_error());
            $s2 = mysql_fetch_assoc($s1);
            do {
                $s3[$s2['type']][$s2['catshop_prd_id']] = $s2['catshop_prd_id'];
            } while ($s2 = mysql_fetch_assoc($s1));

            if ($c_id <= 0) {
                return array("lst" => $s3['/product/'], "zag" => RECOMEND_NAME_LAST_VISITS);
                //break;
            } // если нет клиента, то просто показываем последние посещения
        } else {
            $s3['/product/'][$prd_one_flag] = $prd_one_flag;
        }

        // TODO: рекомендации по orders, search
        ////////////////////////////////////////////////////////

        $s4 = DB_PREFIX . "customers_lastvisits.catshop_prd_id IN ('" . @implode("', '", $s3['/product/']) . "')";
        $s4_not = "AND " . DB_PREFIX . "customers_lastvisits.catshop_prd_id NOT IN ('" . @implode("', '", $s3['/product/']) . "')";

        // РЕКОМЕНДАЦИИ: КЛЮЧЕВЫЕ СЛОВА /KEYWORDS
        if ($type == "keywords") { // рекомендации по ключ словам
            $s4 = strtr($s4, array("customers_lastvisits.catshop_prd_id" => "products_keywords.products_nnn"));
            $s4_not = strtr($s4_not, array("customers_lastvisits.catshop_prd_id" => "products_keywords.products_nnn"));
            $sql_whr2 = "SELECT keyword FROM " . DB_PREFIX . "products_keywords WHERE " . $s4 . " GROUP BY keyword ORDER BY SUM(4sum) DESC LIMIT " . $limit . "";
            $s5 = mysql_kall($sql_whr2) or die(mysql_error());
            $s66 = "";
            if (mysql_num_rows($s5) > 0) {
                $s6 = mysql_fetch_assoc($s5);
                do {
                    $s66.="'" . $s6['keyword'] . "', ";
                } while ($s6 = mysql_fetch_assoc($s5));
                $s66 = substr($s66, 0, -2);
            }
            if ($s66 != "") {
                $sql_whr2 = "SELECT products_nnn, keyword FROM " . DB_PREFIX . "products_keywords WHERE keyword IN (" . $s66 . ") " . $s4_not . "";
                $s5 = mysql_kall($sql_whr2) or die(mysql_error());
                $s77 = "";
                if (mysql_num_rows($s5) > 0) {
                    $s6 = mysql_fetch_assoc($s5);
                    do {
                        $s77[$s6['products_nnn']] = @$s77[$s6['products_nnn']] + 1;
                    } while ($s6 = mysql_fetch_assoc($s5));
                    arsort($s77);
                    $limit_kaunt = $limit;
                    foreach ($s77 as $k => $v) {
                        $s7[$k] = $k;
                        $limit_kaunt--;
                        if ($limit_kaunt <= 0) {
                            break;
                        }
                    }

                    return array("lst" => $s7, "zag" => RECOMEND_NAME_BASE_ON_KEYWORDS);
                    //break;
                }
            }
        }
        /////////////////////////////////////////
        // РЕКОМЕНДАЦИИ: VISITS
        if ($type == "visits") {

            // TODO: полный перегруз БАЗЫ ДАННЫХ, поэтому отрубаем до лучших времен
            $sql_whr2 = "SELECT catshop_prd_id, SUM(nums), customers_id FROM " . DB_PREFIX . "customers_lastvisits WHERE type='/product/'
                    " . @$s4_not . " AND customers_id IN (SELECT customers_id FROM " . DB_PREFIX . "customers_lastvisits WHERE customers_id!='" . $c_id . "' AND customers_id!='0' AND " . $s4 . " GROUP BY customers_id) GROUP BY catshop_prd_id ORDER BY SUM(nums) DESC LIMIT " . $limit . "";
            $s5 = mysql_kall($sql_whr2) or die(mysql_error());
            if (mysql_num_rows($s5) > 0) {
                $s6 = mysql_fetch_assoc($s5);
                do {
                    $s7[$s6['catshop_prd_id']] = $s6['catshop_prd_id'];
                } while ($s6 = mysql_fetch_assoc($s5));

                if (count($s7) < $limit) {
                    $s7 = @array_merge(@$s7, @$s3['/product/']);
                    $s7 = @array_combine(@$s7, @$s7);
                } // добиваем посещенными

                if ($prd_one_flag == "") {
                   // $_SESSION['cache']['recomend']['visits'][SHOP_NNN][$c_id] = serialize($s7);
                }
                return array("lst" => $s7, "zag" => RECOMEND_NAME_SIMILAR_VISITS);
                //break;
            }
        }
        //////////////////////////////////////////
        ///////////////////////////
    }

    function pop($type = "views", $sort = "month", $limit = "10") {
        Debug::log();
        //// популярные товары: просмотры views, заказы orders, обсуждения reviews или наугад random
        // sort - month, day
        $limit = LIMIT_PRD_POP + $limit;
        $months = array("01" => "января", "02" => "февраля", "03" => "марта", "04" => "апреля", "05" => "мая", "06" => "июня", "07" => "июля", "08" => "августа",
            "09" => "сентября", "10" => "октября", "11" => "ноября", "12" => "декабря");

        $txt = array("viewed_month" => "Популярные товары месяца", "viewed_day" => "Популярные товары сегодня",
            "ordered_month" => "Бестселлеры", "ordered_day" => "Бестселлеры сегодня");

        $sort_arr2 = array("views" => "viewed", "orders" => "ordered");

        if ($type == "random") {
            $type_arr = array("0" => "views", "1" => "orders");
            $type = $type_arr[array_rand($type_arr)];
            $sort_arr = array("month", "day");
            $sort = $sort_arr[array_rand($sort_arr)];
        }

        $viewed = new products();

        if ($type == "views") {
            $prds = $viewed->collect_products($limit, "pop_vwd", "1");
            $sort2 = "viewed_" . $sort;
        }
        if ($type == "orders") {
            $prds = $viewed->collect_products($limit, "pop_ord", "1");
            $sort2 = "ordered_" . $sort;
        }
        // TODO: самые обсуждаемые товары

        $prds = $viewed->sort_products($prds, $sort2, 'desc');
        return array("prds" => $prds, "zag" => $txt[$sort2]);
    }

    function pop_rate($limit = "10") {
        Debug::log();
        $add_sql = cats();
        $sql = "SELECT " . DB_PREFIX . "products_reviews.products_nnn, SUM(rate), COUNT(" . DB_PREFIX . "products_reviews.nnn) as countnnn, SUM(vote_y), SUM(vote_n) FROM " . DB_PREFIX . "products_reviews, " . DB_PREFIX . "products_2_cats
                    WHERE " . DB_PREFIX . "products_reviews.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . "
                        GROUP BY " . DB_PREFIX . "products_reviews.products_nnn ORDER BY COUNT(" . DB_PREFIX . "products_reviews.nnn) DESC LIMIT " . ($limit + 10) . "";
        $poparr = mysql_kall($sql) or die(mysql_error());
        $poparr2 = mysql_fetch_assoc($poparr);
        do {
            $rate_avr = round($poparr2['SUM(rate)'] / $poparr2['countnnn'], 2);
            if (($poparr2['SUM(vote_y)'] + $poparr2['SUM(vote_n)']) > 0) {
                $rate_yn_avr = (($poparr2['SUM(vote_y)'] * 5) + ($poparr2['SUM(vote_n)'] * 1)) / ($poparr2['SUM(vote_y)'] + $poparr2['SUM(vote_n)']);
                if ($rate_avr <= 0) {
                    $d = 1;
                } else {
                    $d = 2;
                }
                $rate_avr_all = round(($rate_avr + $rate_yn_avr) / $d, 2);
            } else {
                $rate_avr_all = $rate_avr;
            }
            $poparr3[$poparr2['products_nnn']] = $rate_avr_all;
        } while ($poparr2 = mysql_fetch_assoc($poparr));
        if (count($poparr3) > 0) {
            arsort($poparr3);
            foreach ($poparr3 as $k => $v) {
                $poparr3[$k] = $k;
            }
            return $poparr3;
        }
    }

    function pop_keywords($prds = array(), $limit = 10) {
        Debug::log();
        if (count($prds) > 0) {
            $prds2 = implode("', '", $prds);
            $kws = mysql_kall("SELECT nnn, keyword, SUM(4sum) FROM " . DB_PREFIX . "products_keywords WHERE products_nnn IN ('" . $prds2 . "') GROUP BY keyword ORDER BY SUM(4sum) DESC LIMIT " . $limit . "") or die(mysql_error());
            $kws2 = mysql_fetch_assoc($kws);
            do {
                $main[$kws2['nnn']] = $kws2['keyword'];
            } while ($kws2 = mysql_fetch_assoc($kws));
            return($main);
        }
    }

    function random($type = "random", $limit = "1", $nnn = "") { // товары наугад
        // attr, keyword, manuf, random
        Debug::log();
        $type_arr = array("attr", "keyword", "manuf");

        $add_sql = cats();

        if ($type == "random") {
            $type = $type_arr[array_rand($type_arr)];
        }

        if ($type == "attr" || $type == "manuf") {
            $excl = "";
            $manuf = "";
            if ($nnn == "") {
                if (EXCLUDE_ATTRS != "" && $type != "manuf") {
                    $excl = explode(",", EXCLUDE_ATTRS);
                    array_walk($excl, 'trim_blank');
                    $excl = implode("' AND attr_name!='", $excl);
                }
                if ($type == "manuf") {
                    $manuf = "AND attr_name='производитель'";
                }
                $attrs4 = mysql_kall("SELECT " . DB_PREFIX . "products_attr.products_nnn, attr_name, attr_val FROM " . DB_PREFIX . "products_attr,
                                    " . DB_PREFIX . "products_2_cats WHERE " . DB_PREFIX . "products_attr.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn
                                        " . $add_sql . " AND " . DB_PREFIX . "products_attr.attr_name!='" . $excl . "' " . $manuf);
                $attrs4_1 = mysql_num_rows($attrs4);
                if ($attrs4_1 > 0) {
                    $attrs5 = mt_rand(0, ($attrs4_1 - 1));
                    $attrs6_name = mysql_result($attrs4, $attrs5, 'attr_name');
                    $attrs6_val = mysql_result($attrs4, $attrs5, 'attr_val');
                    $attrs6_nnn = mysql_result($attrs4, $attrs5, 'products_nnn');
                }
            } else {
                $attrs6 = mysql_kall("SELECT attr_name, attr_val FROM " . DB_PREFIX . "products_attr
                                    WHERE nnn='" . $nnn . "' ORDER BY nnn ASC LIMIT 1");
                $attrs6_name = mysql_result($attrs6, '0', 'attr_name');
                $attrs6_val = mysql_result($attrs6, '0', 'attr_val');
            }

            if (@$attrs6_name != "") {
                $zag = $attrs6_name;
                if ($attrs6_val != "") {
                    $zag = "<strong>" . $zag . "</strong> &rarr; " . $attrs6_val . "";
                }
                if ($attrs6_nnn != "") {
                    $main[$attrs6_nnn] = $attrs6_nnn;
                } else {

                    $attrs8 = mysql_kall("SELECT " . DB_PREFIX . "products_attr.products_nnn
                                        FROM " . DB_PREFIX . "products_attr, " . DB_PREFIX . "products_2_cats WHERE
                                            " . DB_PREFIX . "products_attr.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn
                                        " . $add_sql . " AND attr_name!='" . $excl . "' AND (attr_name='" . $attrs6_name . "'
                                            AND attr_val='" . $attrs6_val . "') GROUP BY " . DB_PREFIX . "products_attr.products_nnn ORDER BY rand() LIMIT " . $limit . "");

                    if (mysql_num_rows($attrs8) > 0) {
                        $attrs9 = mysql_fetch_assoc($attrs8);
                        do {
                            $main[$attrs9['products_nnn']] = $attrs9['products_nnn'];
                        } while ($attrs9 = mysql_fetch_assoc($attrs8));
                    }
                }
            }
        }

        if ($type == "keyword") {
            $keyws1 = mysql_kall("SELECT keyword, " . DB_PREFIX . "products_keywords.products_nnn
                             FROM " . DB_PREFIX . "products_keywords, " . DB_PREFIX . "products_2_cats WHERE
                                " . DB_PREFIX . "products_keywords.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn
                                        " . $add_sql . "");
            $keyws2 = mysql_num_rows($keyws1);
            if ($keyws2 > 0) {
                $keyws3 = mt_rand(0, ($keyws2 - 1));
                $keyws4_kwrd = mysql_result($keyws1, $keyws3, 'keyword');
                $keyws4_nnn = mysql_result($keyws1, $keyws3, 'products_nnn');
            }
            if ($keyws4_kwrd != "") {
                $zag = "ключевое слово &rarr; <strong>" . $keyws4_kwrd . "</strong>";
                if ($limit <= 1 && $keyws4_nnn != "") {
                    $main[$keyws4_nnn] = $keyws4_nnn;
                } else {
                    $keyws5 = mysql_kall("SELECT " . DB_PREFIX . "products_keywords.products_nnn
                             FROM " . DB_PREFIX . "products_keywords, " . DB_PREFIX . "products_2_cats WHERE
                                " . DB_PREFIX . "products_keywords.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn
                                        " . $add_sql . " AND keyword='" . $keyws4_kwrd . "' GROUP BY " . DB_PREFIX . "products_keywords.products_nnn
                                            ORDER BY rand() LIMIT " . $limit . "");
                    if (mysql_num_rows($keyws5) > 0) {
                        while ($keyws6 = mysql_fetch_assoc($keyws5)) {
                            $main[$keyws6['products_nnn']] = $keyws6['products_nnn'];
                        }
                    }
                }
            }
        }

        return array("prds" => @$main, "zag" => @$zag);
    }

    function recomend_cats($c_id = "0", $limit = LIMIT_POP_CAT, $every_customer = "") { // everycustomer - популярные разделы у клиентов
        Debug::log();
        // TODO: полный перегруз БАЗЫ ДАННЫХ
        /*
          //// рекомендуемые или популярные разделы

          if($every_customer!="1") {
          $recs=readfromfile(SHOP_NNN."_cid".$c_id."_recomend_cats","3"); if($recs!="") { return array("lst"=>unserialize($recs),"zag"=>RECOMEND_CATS_NAME); break; } }

          $add_sql=cats();

          if($c_id<=0) { $c_id="0"; }

          if($every_customer!="1") {
          $sql_whr="SELECT ".DB_PREFIX."customers_lastvisits.* FROM ".DB_PREFIX."customers_lastvisits, ".DB_PREFIX."products_2_cats
          WHERE ".DB_PREFIX."customers_lastvisits.catshop_prd_id=".DB_PREFIX."products_2_cats.products_nnn
          AND type='/catalog/' AND (customers_id='".$c_id."' OR customers_id='0') AND catshop_prd_id!='0' ".$add_sql." ORDER BY customers_id DESC, nums DESC, lastmodified DESC LIMIT ".$limit."";
          } else {
          $sql_whr="SELECT ".DB_PREFIX."customers_lastvisits.*, ".DB_PREFIX."products_2_cats.shop_cat FROM ".DB_PREFIX."customers_lastvisits, ".DB_PREFIX."products_2_cats
          WHERE ".DB_PREFIX."customers_lastvisits.catshop_prd_id=".DB_PREFIX."products_2_cats.products_nnn
          AND (customers_id!='0') AND catshop_prd_id!='0' ".$add_sql." ORDER BY nums DESC, lastmodified DESC LIMIT ".$limit."";
          }
          $s1=mysql_kall($sql_whr);
          $s2=mysql_fetch_assoc($s1);
          do {
          if($s2['type']=="/catalog/") {
          $s3[$s2['type']][$s2['catshop_prd_id']]=$s2['catshop_prd_id'];  }

          if($every_customer=="1"&&$s2['type']=="/product/") { $s3['/catalog/'][$s2['shop_cat']]=$s2['shop_cat']; }

          } while($s2=mysql_fetch_assoc($s1));

          if($c_id<=0) { return array("lst"=>$s3['/catalog/'],"zag"=>POPULAR_CATS_NAME); break; } // если нет клиента, то просто показываем популярные разделы
          $s4=DB_PREFIX."customers_lastvisits.catshop_prd_id IN ('".@implode("', '",$s3['/catalog/'])."')";
          $s4_not=DB_PREFIX."customers_lastvisits.catshop_prd_id NOT IN ('".@implode("', '",$s3['/catalog/'])."')";

          // TODO: полный перегруз БАЗЫ ДАННЫХ
          $sql_whr2="SELECT catshop_prd_id, SUM(nums), customers_id FROM ".DB_PREFIX."customers_lastvisits WHERE type='/catalog/'
          AND ".$s4_not." AND catshop_prd_id!='0' AND customers_id IN (SELECT customers_id FROM ".DB_PREFIX."customers_lastvisits WHERE customers_id!='".$c_id."' AND customers_id!='0' AND ".$s4." GROUP BY customers_id) GROUP BY catshop_prd_id ORDER BY SUM(nums) DESC LIMIT ".$limit."";
          $s5=mysql_kall($sql_whr2); if(mysql_num_rows($s5)>0) { $s6=mysql_fetch_assoc($s5);
          do { $s7[$s6['catshop_prd_id']]=$s6['catshop_prd_id']; } while ($s6=mysql_fetch_assoc($s5));
          if(count($s7)<$limit) { $s7=@array_merge(@$s7,@$s3['/catalog/']); $s7=@array_combine(@$s7, @$s7); } // добиваем посещенными

          if($every_customer!="1") { write2file(serialize($s7),SHOP_NNN."_cid".$c_id."_recomend_cats"); }
          return array("lst"=>$s7,"zag"=>RECOMEND_CATS_NAME); break;
          }


         */
    }

    function cats2cats($catshop, $limit = LIMIT_POP_CAT) {
        Debug::log();
        //// рекомендуемые или популярные разделы
        /*
          $recs=readfromfile(SHOP_NNN."_".$catshop."_cats2cats"); if($recs!="") { return unserialize($recs); break; }

          $add_sql=cats();

          $s4=DB_PREFIX."customers_lastvisits.catshop_prd_id IN ('".@$catshop."')";
          $s4_not=DB_PREFIX."customers_lastvisits.catshop_prd_id NOT IN ('".@$catshop."')";

          // TODO: полный перегруз БАЗЫ ДАННЫХ
          $sql_whr2="SELECT catshop_prd_id, SUM(nums), customers_id FROM ".DB_PREFIX."customers_lastvisits WHERE type='/catalog/'
          AND ".$s4_not." AND catshop_prd_id!='0' AND customers_id IN (SELECT customers_id FROM ".DB_PREFIX."customers_lastvisits
          WHERE ".$s4." GROUP BY customers_id) GROUP BY catshop_prd_id ORDER BY SUM(nums) DESC LIMIT ".$limit."";

          $s5=mysql_kall($sql_whr2); if(mysql_num_rows($s5)>0) {
          $s6=mysql_fetch_assoc($s5);
          do { $s7[$s6['catshop_prd_id']]=$s6['catshop_prd_id']; } while ($s6=mysql_fetch_assoc($s5));
          write2file(serialize($s7),SHOP_NNN."_".$catshop."_cats2cats");
          return $s7; break;
          }
         */
    }

}


class filters {
    
    function gather($catalog = SHOP_NNN, $attributes = array()) {
                
        $return_arr = array();
        
        $add_sql = cats("sql", $catalog);
               
        $what = ""; $what2 = "";
        
        if($catalog == SHOP_NNN && count($attributes)<=0 ) { return; }
        
        if(count($attributes)>0) {
        $sel_sql = "SELECT attr_name, attr_val FROM ".DB_PREFIX."products_attr WHERE nnn = '".implode("' OR nnn='",$attributes)."'";
        $sql = mysql_kall($sel_sql);

        while($sql2 = mysql_fetch_assoc($sql)) {
            $what.= "(attr_name = '".$sql2['attr_name']."' AND attr_val = '".$sql2['attr_val']."') OR ";
            $what_arr[$sql2['attr_name']][$sql2['attr_val']] = $sql2['attr_val'];
        }
        if($what != "") {
        $what2 = DB_PREFIX."products_attr.products_nnn IN (SELECT products_nnn FROM ".DB_PREFIX."products_attr WHERE ".substr($what,0,-3)." GROUP BY products_nnn) AND ";
        }}
        
        $what3 = explode(",", FILTER_ATTRS);
        $what3[] = "производитель";
        $what4 = implode("' OR attr_name='",$what3);
        $sel_sql = "SELECT attr_name, attr_val, ".DB_PREFIX."products_attr.nnn, ".DB_PREFIX."products_attr.products_nnn FROM " . DB_PREFIX . "products_attr, " . DB_PREFIX . "products_2_cats WHERE ". $what2 . " (attr_name = '".$what4."') AND attr_val!='' AND " . DB_PREFIX . "products_attr.products_nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY attr_val ASC";
        
        $sql = mysql_kall($sel_sql) or die(mysql_error());                
        while($sql3 = mysql_fetch_assoc($sql)) {
            if(isset($what_arr[$sql3['attr_name']][$sql3['attr_val']])) { 
                $what_what[$sql3['products_nnn']] = $what_what[$sql3['products_nnn']] + 1; }            
            $return_arr['attrs'][$sql3['attr_name']][$sql3['attr_val']] = $sql3['nnn'];  
            $products['attrs'][$sql3['attr_name']][$sql3['attr_val']][$sql3['products_nnn']] = $sql3['products_nnn'];  
        }
        
        
        if(is_array($what_what)) {
        foreach($what_what as $k => $v) {       
            if($v < count($what_arr)) { unset($what_what[$k]); } else { $what_what2[$k] = $k; }} 
            $return_arr['products']=$what_what2;                        
 
        foreach($products['attrs'] as $k => $v) { if(isset($what_arr[$k])) { continue; }
            foreach($v as $kk=>$vv) { 
                foreach($vv as $kkk=>$vvv) {
                if(isset($what_what2[$kkk])) { $return_arr['allowed'][$k][$kk] = $kk; }
                }   
                if(!isset($return_arr['allowed'][$k][$kk])) { unset($return_arr['attrs'][$k][$kk]); } 
                }}    
        } else {
            if(is_array($products)) {
                $products2 = simplifyarr($products['attrs']);                
                $products2[1] = array_count_values($products2[1]);
                $what_what2 = array_combine(array_keys($products2[1]), array_keys($products2[1]));
                $return_arr['products'] = $what_what2;
            }
        }
        
        
        if(count($attributes)>0 && is_array($what_what2)) {
            $sel_sql = "SELECT shop_cat FROM ".DB_PREFIX."products_2_cats WHERE products_nnn IN ('".implode("', '",$what_what2)."') GROUP BY shop_cat";
            
            $sql = mysql_kall($sel_sql) or die(mysql_error());                
            while($sql4 = mysql_fetch_assoc($sql)) {
                $return_arr['catalog'][$sql4['shop_cat']] = $sql4['shop_cat'];            
            }
        }
        
        return $return_arr;
    }
    
}