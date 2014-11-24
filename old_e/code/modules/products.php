<?php
class products {

    //
    //
    // LEVEL 1
    //          product_prices
    //

    function product_prices($prices, $priors, $attrs = "0", $client = "0", $type = "prd", $grp_all_nnn = '0', 
            $order_discount = "0", $admin_customers = "") { 
 Debug::log();
        // prices - массив цен, priors - массив приоритетов, attrs - attr_id /для цен выбранного свойства/, client - id клиента, type - prd, grp
        // grp_alll_nnn - только для групп - nnn для проверки свойства, ?order_discount - появл. только из админки для скидки на заказ в целом
        // ?admin_customers - массив customers_type, customers_discount, 
        // ?customers_discount_expire -- только из админки для оформл. заказа от имени клиента
        
        $client_status = "user";
        $current_discount = 0; // умолчания
        if ($client > 0) { // статус клиента: дистр (distr), гость (guest), оптовик (opt), индивидуальная скидка ()
            $client_status = $_SESSION['customers_type']; // client_status
            if (time() < $_SESSION['customers_discount_expire']) {
                $current_discount = $_SESSION['customers_discount'];
            }
            // сделать определение типа и скидки со стороны админки, если есть
            if (isset($admin_customers) && @$admin_customers != "") {
                if (@$admin_customers['customers_type'] != "") {
                    $client_status = $admin_customers['customers_type'];
                }
                if (@$admin_customers['customers_discount_expire'] != "") {
                    if (time() < $admin_customers['customers_discount_expire']) {
                        $current_discount = $admin_customers['customers_discount'];
                    }
                }
            }
        }
        // client_status

        if ($type == "prd") {
            $add2sql = "AND change_price='1'";
        }
        if ($type == "grp") {
            $add2sql = "AND change_price_grp='1' AND products_nnn='" . $grp_all_nnn . "'";
        }

        if ($attrs > 0) { // если рассчитывается цена товара с конкретным выбранным свойством
            $attrs2 = explode(",", $attrs);
            foreach ($attrs2 as $k => $v) {
                $v = trim($v);
                $v2 = mysql_kall("SELECT price, price_sales, price_distr, price_guest, price_opt 
                    FROM " . DB_PREFIX . "products_attr WHERE nnn='" . $v . "' AND attr_dominant='1' " . $add2sql . " AND price>0");
                if (mysql_num_rows($v2) > 0) {
                    $v3 = mysql_fetch_assoc($v2);
                    foreach ($v3 as $kk => $vv) {
                        $prices[$kk] = $vv;
                    }
                }
            }
        } // изм. цены из-за свойства, замена всего массива
        
        // PRICE_0 - начальная цена
        $result_price[0] = $prices['price'];
        $result_price[1] = "0"; // очередность: 0
        
        // PRICE_1 - измененная цена: указана цена со скидкой
        if ($priors['prior_sales'] == "1" && $prices['price_sales'] > 0) {
            if ($prices['price_sales'] != $prices['price']) {
                $result_price[1] = "1";
            }
            $result_price[0] = $prices['price_sales'];
        } // 1
        
        // фиксируем price_0 или price_1 - как первую ступень, на случай отмены
        $temp_result_price = $result_price[0]; 
        
        // фиксируем неизмененную цену
        $temp_result_price_nosales = $prices['price']; 
        
        // customers_type
        if ($client_status == "guest" && $priors['prior_guest'] == "1" && $prices['price_guest'] > 0) {
            $result_price[1] = "2";
            $result_price[0] = $prices['price_guest'];
        } // гость, 2
        if ($client_status == "distr" && $priors['prior_distr'] == "1" && $prices['price_distr'] > 0) {
            $result_price[1] = "3";
            $result_price[0] = $prices['price_distr'];
        } // дистрибьютор, 3
        if ($client_status == "opt" && $priors['prior_opt'] == "1" && $prices['price_opt'] > 0) {
            $result_price[1] = "4";
            $result_price[0] = $prices['price_opt'];
        } // оптовик, 4
        
        // PRICE_2 - скидки, индивидуальные или купон, если есть
        if ($client_status != "user" && CUSTOMERS_TYPE_OVERCOME_PRD_DISCOUNT == "1") {

        } else { // опр. сохраняется ли статус клиента
            if ($current_discount > 0) { // если есть скидка клиента или купон
                if ($temp_result_price != $prices['price']) {

                } else { // если нет скидочной цены
                    $result_price[1] = "5";
                    $result_price[0] = ((100 - $current_discount) * $prices['price']) / 100;
                }
            }
        } // 5
        
        // PRICE_3 - скидки на заказ (из админки)
        if ($client_status != "user" && CUSTOMERS_TYPE_OVERCOME_ORDER_DISCOUNT == "1") {

        } else { // опр. сохраняется ли статус клиента
            if ($order_discount > 0) { // если есть скидка на заказ
                if ($temp_result_price != $prices['price']) {

                } else { // если нет скидочной цены
                    $result_price[1] = "6";
                    $result_price[0] = ((100 - $order_discount) * $prices['price']) / 100;
                }
            }
        }

        // "0)".$prices['price']." 1)".$result_price[1].": ".$result_price[0]."<br>";
        //$result_price[2]=$prices['price'];
        if ($result_price[1] == "1") {
            $result_price[2] = $temp_result_price_nosales;
        } else {
            //// хитрый момент: если никаких изм цены не было, то показываем разницу с первоначальной ценой, иначе разницу со скидочной ценой
            $result_price[2] = $temp_result_price;
        }
        return $result_price; // out: 0 - цена, 1 - статус (0-6), 2 - первоначальная цена
    }

    //
    //
    // LEVEL 2
    //          product_group, product_attrs
    //
    
    function product_group($grp_nnn, $grp_price = '0', $attrs = '0', $client = '0') {
Debug::log();
        // определяем картинки и цены, названия и вес для группы товаров
        // grp_nnn - id товаров в группе, grp_price - принудительно указанная цена уже выбранная с помощью product_prices, 
        // attrs - выбранные свойства, client - id
        
        // используем кэш, если не прошло заданного времени
        if ((time() - (60 * CACHE_TIMEOUT)) < @$_SESSION['cache']['groups'][$grp_nnn][$grp_price][$attrs][$client]['time']) {
            return unserialize($_SESSION['cache']['groups'][$grp_nnn][$grp_price][$attrs][$client]['sql']);
         }

        $currencycheck=cats('arr');

        $v2 = explode(",", $grp_nnn);
        array_walk($v2, 'trim_blank');

        foreach ($v2 as $k3 => $v3) {
            $v2_flip[$v3][] = $k3;
        }

        $v2_full_test = DB_PREFIX . "products.nnn IN ('" . implode("', '", $v2) . "')";

        $v4 = mysql_kall("SELECT " . DB_PREFIX . "products.nnn, " . DB_PREFIX . "products.currency, " . DB_PREFIX . "products.nazv, 
            weight, price, price_sales, price_distr, price_guest, price_opt, prior_sales, prior_distr, prior_guest, prior_opt, shop_cat
            FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats  WHERE (" . $v2_full_test . ") AND 
                ".DB_PREFIX."products.nnn=".DB_PREFIX."products_2_cats.products_nnn AND " . DB_PREFIX . "products.status!='hid' 
                    GROUP BY ".DB_PREFIX."products.nnn ORDER BY ".DB_PREFIX."products_2_cats.prior DESC") or die(mysql_error()); // @reviewlate: slow

        $summ_grp_price = 0;
        $summ_grp_price_starter = 0;
        $summ_grp_price_currency = 0;
        $summ_grp_price_currency_starter = 0; // starter - начальная цена
        
        if (mysql_num_rows($v4) > 0) {
            $v5 = mysql_fetch_assoc($v4);
            $flag_prior_currency = 0;
            do {
                $v3 = $v5['nnn']; // $v2_flip[$v3] - порядковый номер в группе
                if (count($v2_flip[$v3]) <= 0) {
                    continue;
                }
                foreach ($v2_flip[$v3] as $v2_flip_k => $v2_flip_v) {
                    
                    if ($flag_prior_currency <= 0) {
                        $catshop_currency_fin = $currencycheck[$v5['shop_cat']]['currency'];;
                        $flag_prior_currency = 1;
                    } // только у самого первого
                    
                    // $v3." ---> prior ".$v5['catshop_prior']." - - - $catshop_currency_fin - ".$v2_flip_v."<br>";
                    array_shift($v2_flip[$v3]);

                    foreach ($v5 as $kk => $vv) {
                        if ($kk == "nazv") {
                            $return_arr['grp_nazv'][$v2_flip_v][trim($v3)] = $v5['nazv'];
                            continue;
                        }
                        if ($kk == "weight") {
                            $return_arr['grp_weight'] = @$return_arr['grp_weight'] + $vv;
                            if ($vv <= 0) {
                                $return_arr['grp_weight_flag'] = "1";
                            } continue;
                        }
                        if ($kk == "currency" || $kk == "shop_cat" || $kk == "nnn") {
                            continue;
                        }
                        if (substr($kk, 0, 5) == 'price') {
                            $prices[$kk] = $vv;
                        } else {
                            $priors[$kk] = $vv;
                        }
                    }

                    if (is_array($attrs)) {
                        $attrs_send = $attrs[$v2_flip_v];
                    } else {
                        $attrs_send = $attrs;
                    }
 
                    $return_price = $this->product_prices($prices, $priors, $attrs_send, $client, 'grp', trim($v3)); // --> LEVEL 2->LEVEL 1
                    // out -> return_price[0], return_price[1], return_price[2]

                    $summ_grp_price = $summ_grp_price + $return_price[0]; // общая стоимость группы
                    
                    $summ_grp_price_starter = $summ_grp_price_starter + $return_price[2]; // первоначальная стоимость
                    
                    $return_arr['priors'][trim($v3)] = $priors; // массив с проиоритетами, а он нужен?
                    
                    // общая и первоначальные стоимость группы, конвертированные согласно инд. или общему коэффициенту (u.e) валюте
                    $summ_grp_price_currency = $summ_grp_price_currency + 
                            currency_converter($return_price[0], $catshop_currency_fin, $v5['currency']);
                    
                    $summ_grp_price_currency_starter = $summ_grp_price_currency_starter + 
                            currency_converter($return_price[2], $catshop_currency_fin, $v5['currency']);

                    if ($return_price[1] > @$return_arr['price_star']) {
                        $return_arr['price_star'] = $return_price[1];
                    } // @reviewlate: тип цены, выбирается наибольший (выделять цену?)
                }
                
            } while ($v5 = mysql_fetch_assoc($v4));
        }       
        // "<pre>"; print_r($return_arr); "</pre>";
        ///////////////////////////////////////////////////////////////////////
        // если у группы фиксированная цена, то вывод только прайс без первоначальной цены и без конвертации (будет сделано в show_product)
        if ($grp_price <= 0) {
            $return_arr['price'] = $summ_grp_price;
            $return_arr['price_grp_currency'] = $summ_grp_price_currency;
            $return_arr['price_starter'] = $summ_grp_price_starter;
            $return_arr['price_grp_currency_starter'] = $summ_grp_price_currency_starter;
        } else { // 
            $return_arr['price'] = $grp_price;
            unset($return_arr['price_star']);
        }

        // собираем все картинки группы через ;
        $img_v = "AND (products_nnn IN ('" . implode("', '", $v2) . "'))";
        $img_sql = mysql_kall("SELECT img FROM " . DB_PREFIX . "products_image WHERE sort='0' " . @$img_v . "");
        $img_sql2 = mysql_fetch_assoc($img_sql);
        if (mysql_num_rows($img_sql) > 0) {
            do {
                $return_arr['img'] = $return_arr['img'] . $img_sql2['img'] . ";";
            } while ($img_sql2 = mysql_fetch_assoc($img_sql));
        }
        
     //   $_SESSION['cache']['groups'][$grp_nnn][$grp_price][$attrs][$client]['sql'] = serialize($return_arr);
     //   $_SESSION['cache']['groups'][$grp_nnn][$grp_price][$attrs][$client]['time'] = time();
            
        return $return_arr;
        // out -> grp_nazv, grp_weight, {grp_weight_flag}, priors[], price, {price_star, price_grp_currency, price_starter, 
        // price_grp_currency_starter}, img
    }

    //
    //

    function product_attrs($nnn, $type, $priors="") { // nnn, type=grp,prd, priors[prd_id] - приоритеты
   Debug::log();     
        $nnn_arr=explode(" ",$nnn); $nnn=implode("",$nnn_arr); $nnn_arr=explode(",",$nnn);
        if(count($nnn_arr)>1) { $nnn2="products_nnn='".implode("' OR products_nnn='",$nnn_arr)."'";  } else { $nnn2="products_nnn='".$nnn."'"; }
         $attrs=mysql_kall("SELECT * FROM ".DB_PREFIX."products_attr WHERE ".$nnn2."");
        $attrs2=mysql_fetch_assoc($attrs);
        if(mysql_num_rows($attrs)>0) { unset($flag_4_dominant, $flag_4_dominant_already, $flag_4_dominant_pnnn);
            do { $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['attr_nnn']=$attrs2['nnn'];
                 $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['attr_type']=$attrs2['attr_type'];
                 $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['attr_descr']=$attrs2['attr_descr'];

                 if($type=="prd") { // менять ли цену, определяем где это работает, а где нет. это не касается групп с фиксированной ценой
                 $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['change']=$attrs2['change_price'];
                 } else { $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['change']=$attrs2['change_price_grp']; }

                 if((isset($flag_4_dominant_pnnn[$attrs2['products_nnn']])&&isset($flag_4_dominant_already)&&!isset($flag_4_dominant[$attrs2['attr_name']]))&&$attrs2['attr_dominant']=="1") {
                     $attrs2['attr_dominant']="0"; $flag_4_dominant_already=1;
                     } // если доминантное свойство уже выбрано, то больше доминантных быть не может (фикс. nnn, prd_id, attr_name, attr_dominant)

                 // если доминантное свойства еще не выбрано, выбираем его
                 if($attrs2['attr_dominant']=="1") { $flag_4_dominant[$attrs2['attr_name']]=1; $flag_4_dominant_already=1; $flag_4_dominant_pnnn[$attrs2['products_nnn']]=1; }

                 // фиксируем доминантные и недоминантные свойства
                 $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['attr_dominant']=$attrs2['attr_dominant'];

                 // определяем стоимость товара с текущим свойством, учитывая так же статус клиента и его скидки
                 $return_price=$this->product_prices(array('price'=>$attrs2['price'],'price_sales'=>$attrs2['price_sales'],'price_distr'=>$attrs2['price_distr'],'price_guest'=>$attrs2['price_guest'],'price_opt'=>$attrs2['price_opt']),$priors[$attrs2['products_nnn']],0,@$_SESSION['customers_id'],'prd');
                 // LEVEL 2->LEVEL 1 / out -> return_price[0], return_price[1], return_price[2]

                 $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['price']=@$return_price[0]; // сохраняем стоимость товара со свойствами
                 $main['attrs'][$attrs2['products_nnn']][$attrs2['attr_name']][$attrs2['attr_val']]['price_starter']=@$return_price[2];

            } while($attrs2=mysql_fetch_assoc($attrs)); }

            foreach($nnn_arr as $k=>$v) { $main_rearr['attrs'][$k][$v]=@$main['attrs'][$v]; }

            return $main_rearr;
            // out -> [prd_id][attr_name][attr_val] attr_nnn, attr_type, attr_descr, change, attr_dominant, price, price_starter
        }

    //
    //
    // LEVEL 3
    //          collect_products, collect_products_full
    //

    function collect_products($i, $type = "cat", $group_img_flag = "0", $onlynums_flag = "0", $startfrom=0, $sorttype="dat", $sortdirection="DESC") {
        Debug::log();
        // кратко: type=nnn / cat / srch / main / new / attr / keyw / nnn_connects / nnn_array
        // отображение в разделах, поиске, главной, спец страницы: новинки, производители, по ключ словам, по любым аттрибутам
        // nnn_connects - взаимосвязанные товары могут быть из любых магазинов!

        if (is_array($i)) {
            $ii = implode("_", $i);
        } else {
            $ii = $i;
        }

        // используем кэш, если не прошло заданного времени
        if ((time() - (60 * CACHE_TIMEOUT)) < 
                @$_SESSION['cache']['products'][SHOP_NNN][$ii][$type][$group_img_flag][$startfrom][$_SESSION['customers_id']]['time']) {  
            return unserialize($_SESSION['cache']['products'][SHOP_NNN][$ii][$type][$group_img_flag][$startfrom][$_SESSION['customers_id']]['sql']);
        }

            if($startfrom<1) { $startfrom=1; }
            $startfrom=($startfrom-1)*PRDS_PER_PAGE;

           if($sorttype=="ordered_month" ) { $sorttype="ordered"; }
           if($sorttype=="viewed_month" ) { $sorttype="viewed";  }
           if($sorttype=="") { $sorttype="dat"; $sortdirection="desc"; }         
        
        if ($type == "cat") {
            $add_sql = cats('sql', $i); 
            $arr_status_i = cats('arr', $i);          
        } else {
            $add_sql = cats();
        }
        $arr_status = cats('arr');
        
        $vars = "type, status, dat, kogda, nazv, grp_nnn, currency, " . DB_PREFIX . "products.price, " . DB_PREFIX . "products.price_sales, 
            " . DB_PREFIX . "products.price_distr, " . DB_PREFIX . "products.price_guest, " . DB_PREFIX . "products.price_opt, 
                prior_sales, prior_distr, prior_guest, prior_opt, star, ordered, viewed, " . DB_PREFIX . "products.nnn";
        
        //////////////// nnn - конкретный товар:
        
        if ($type == "nnn") {
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn AND (" . DB_PREFIX . "products.nnn='" . $i . "') AND status!='hid' " . @$add_sql . " ORDER BY dat DESC";
        }
        
        //////////////// nnn_array - массив товаров
        
        if ($type == "nnn_array") {
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn AND (" . DB_PREFIX . "products.nnn IN ('" . implode("', '", $i) . "')) AND status!='hid' " . @$add_sql . " ORDER BY dat DESC";
        }
        
        //////////////// nnn_connects - связанные товары из разных магазинов (кроме старого движка):
             
        if ($type == "nnn_connects") {
            $add_sql = "";
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn AND (" . DB_PREFIX . "products.nnn IN ('" . $i . "')) AND status!='hid' ORDER BY dat DESC";
        }
        
        //////////////// cat - товары выбранной категории + подкатегории
             
        if ($type == "cat") {
            $sql = "SELECT " . $vars . ", " . DB_PREFIX . "products_2_cats.shop_cat FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn AND " . DB_PREFIX . "products.status!='hid' " . @$add_sql . " ORDER BY ".$sorttype." ".$sortdirection." LIMIT ".$startfrom.", ".(PRDS_PER_PAGE); 
        }
        
        //////////////// srch - поиск по названию
             
        if ($type == "srch") {
            $i2 = explode(" ", $i);
            $i5 = "";
            if (count($i2) > 1) {
                foreach ($i2 as $i3 => $i4) {
                    $i5.="(nazv='" . trim($i4) . "' OR nazv LIKE '" . trim($i4) . "%%%' OR nazv LIKE '%%%" . trim($i4) . "' OR nazv LIKE '%%%" . trim($i4) . "%%%') AND ";
                }
            } else {
                $i5.="nazv='" . trim($i) . "' OR nazv LIKE '" . trim($i) . "%%%' OR nazv LIKE '%%%" . trim($i) . "' OR nazv LIKE '%%%" . trim($i) . "%%%' OR ";
            }
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE (" . substr($i5, 0, -4) . ") AND status!='hid' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY ".$sorttype." ".$sortdirection." LIMIT ".$startfrom.", ".(PRDS_PER_PAGE);
        }
        
        //////////////// main - товары на главной
             
        if ($type == "main") { 
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE products_2_cats.on_main='1' AND status!='hid' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY products_2_cats.on_main_ord DESC, dat DESC";
        } // TODO: on_main_ord ASC -> DESC временно 
        
        //////////////// new - указанное количество новых товаров
             
        if ($type == "new") {
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE status!='hid' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY dat DESC LIMIT " . $i . "";
        }
        
        //////////////// attr - товары с определенными свойствами (+ производитель)
             
        if ($type == "attr") {
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_attr, " . DB_PREFIX . "products_2_cats WHERE attr_name='" . trim($i[0]) . "' AND attr_val='" . trim($i[1]) . "' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_attr.products_nnn AND " . DB_PREFIX . "products.status!='hid' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY ".$sorttype." ".$sortdirection." LIMIT ".$startfrom.", ".(PRDS_PER_PAGE);
        }
        
        //////////////// keyws - товары с определенными ключевыми словами
             
        if ($type == "keyw") {
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_keywords, " . DB_PREFIX . "products_2_cats WHERE keyword='" . $i . "' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_keywords.products_nnn AND " . DB_PREFIX . "products.status!='hid' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY ".$sorttype." ".$sortdirection." LIMIT ".$startfrom.", ".(PRDS_PER_PAGE);
        }
        
        //////////////// pop_ord - самые заказываемые товары
     
        if ($type == "pop_ord") {
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE status!='hid' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY ordered DESC LIMIT " . $i . "";
        }
        
        //////////////// pop_vwd - самые просматриваемые товары
             
        if ($type == "pop_vwd") {
            $sql = "SELECT " . $vars . " FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats WHERE status!='hid' AND " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql . " ORDER BY viewed DESC LIMIT " . $i . "";
        }
        
        ////////////////            

        $sel_sql = mysql_kall($sql) or die(mysql_error());

        if (@$onlynums_flag == "1") {
            return mysql_num_rows($sel_sql);
        } // только количество!

        if (mysql_num_rows($sel_sql) > 0) {

            $sel = mysql_fetch_assoc($sel_sql);
            do {

                if ($type == "cat") {
                    if ($sel['shop_cat'] == $i) {
                        $main[$sel['nnn']]['smart_sort'] = "0";
                    } else {
                        if (!isset($main[$sel['nnn']]['smart_sort'])) { // поменяв однажды не будем менять!
                            $main[$sel['nnn']]['smart_sort'] = "1";
                        }
                    }
                }

                if (isset($main[$sel['nnn']]['nazv'])) {
                    continue;
                }

                foreach ($sel as $k => $v) { // весь массив
                    if ($k == "nnn") {
                        continue;
                    }
                    $main[$sel['nnn']][$k] = $v;
                }

                if ($sel['type'] == "grp") {
                    $grp_collect[$sel['nnn']] = $sel['grp_nnn'];
                }

                $main[$sel['nnn']]['ordered_month'] = ceil($sel['ordered'] / ((time() - $sel['dat']) / 60 / 60 / 24 / 31));
                $main[$sel['nnn']]['ordered_day'] = ceil($sel['ordered'] / ((time() - $sel['dat']) / 60 / 60 / 24));
                $main[$sel['nnn']]['viewed_month'] = ceil($sel['viewed'] / ((time() - $sel['dat']) / 60 / 60 / 24 / 31));
                $main[$sel['nnn']]['viewed_day'] = ceil($sel['viewed'] / ((time() - $sel['dat']) / 60 / 60 / 24));

                //if($main[$sel['nnn']]['ordered_month']>$sel['ordered']) { $main[$sel['nnn']]['ordered_month']=$sel['ordered']; } 
                // если товар еще не продается месяц, то оставляем гипотетический результат
                if ($main[$sel['nnn']]['ordered_day'] > $sel['ordered']) {
                    $main[$sel['nnn']]['ordered_day'] = $sel['ordered'];
                }
                //if($main[$sel['nnn']]['viewed_month']>$sel['viewed']) { $main[$sel['nnn']]['viewed_month']=$sel['viewed']; }
                if ($main[$sel['nnn']]['viewed_day'] > $sel['viewed']) {
                    $main[$sel['nnn']]['viewed_day'] = $sel['viewed'];
                }

                // даже для групп определяем цену сначала как для обычного товара !
                $return_price = $this->product_prices(
                        array('price' => $sel['price'],
                    'price_sales' => $sel['price_sales'],
                    'price_distr' => $sel['price_distr'],
                    'price_guest' => $sel['price_guest'],
                    'price_opt' => $sel['price_opt']), array('prior_distr' => $sel['prior_distr'],
                    'prior_guest' => $sel['prior_guest'],
                    'prior_sales' => $sel['prior_sales'],
                    'prior_opt' => $sel['prior_opt']), 0, @$_SESSION['customers_id'], 'prd');
                // LEVEL 3 -> LEVEL 1 / out -> return_price[0], return_price[1], return_price[2]
                
                // pics
                $sql_img = mysql_kall("SELECT img FROM " . DB_PREFIX . "products_image 
                    WHERE products_nnn='" . $sel['nnn'] . "' ORDER BY sort ASC LIMIT 1");
                if (mysql_num_rows($sql_img) > 0) {
                    $sql_img2 = mysql_fetch_assoc($sql_img);
                    if (isset($main[$sel['nnn']]['img']) && @$group_img_flag != "1") {
                        $main[$sel['nnn']]['img'] = $sql_img2['img'] . ";" . $main[$sel['nnn']]['img'];
                    } else {
                        $main[$sel['nnn']]['img'] = $sql_img2['img'];
                    } // если есть офиц. картинка для группы, то она заменяется
                }

                // cats          
                $sql_cats = mysql_kall("SELECT shop_cat, products_nnn, basket_separate, prior, prior_bask 
                FROM " . DB_PREFIX . "products_2_cats WHERE products_nnn='" . $sel['nnn'] . "'");
                $sql_cats2 = mysql_fetch_assoc($sql_cats);
                do {                    
                    if ($type == "cat" & count($arr_status_i[$sql_cats2['shop_cat']]) > 0) { // для категорий убираем лишние разделы
                        if ($sql_cats2['shop_cat'] == $i) {
                            $main[$sql_cats2['products_nnn']]['smart_sort'] = "0";
                        } else {
                            if (!isset($main[$sql_cats2['products_nnn']]['smart_sort'])) {
                                $main[$sql_cats2['products_nnn']]['smart_sort'] = "1";
                            }
                        } unset($sel['shop_cat']);
                    }

                    $main[$sql_cats2['products_nnn']]['shop_cat'][$sql_cats2['shop_cat']] = @$arr_status[$sql_cats2['shop_cat']]['status'];
                    $main[$sql_cats2['products_nnn']]['shop_cat_currency'][$sql_cats2['shop_cat']] = @$arr_status[$sql_cats2['shop_cat']]['currency'];

                    if ($sql_cats2['prior'] == "1") {
                        $main[$sql_cats2['products_nnn']]['shop_cat_priority'] = $sql_cats2['shop_cat'];
                    } else {
                        if (!isset($main[$sql_cats2['products_nnn']]['shop_cat_priority'])&&
                                $arr_status[$sql_cats2['shop_cat']]['hostshop']==SHOP_NNN) { // @reviewlate: обрубаем все приоритеты не тек магазина
                            $main[$sql_cats2['products_nnn']]['shop_cat_priority'] = $sql_cats2['shop_cat'];
                        }
                    }

                    if ($sql_cats2['prior_bask'] == "1") {
                        $catsup = cats_up($sql_cats2['shop_cat']);
                        $catsup['connected_2_current'] = $arr_status[$catsup['nnn']]['status'];
                        $main[$sql_cats2['products_nnn']]['shop_cat_onlyshop_arr'][$sql_cats2['shop_cat']] = $catsup;
                        $main[$sql_cats2['products_nnn']]['shop_cat_onlyshop'] = $catsup;
                    } else {
                        $catsup = cats_up($sql_cats2['shop_cat']);
                        $catsup['connected_2_current'] = $arr_status[$catsup['nnn']]['status'];
                        $main[$sql_cats2['products_nnn']]['shop_cat_onlyshop_arr'][$sql_cats2['shop_cat']] = $catsup;
                        if (!isset($main[$sql_cats2['products_nnn']]['shop_cat_onlyshop'])) {
                            $main[$sql_cats2['products_nnn']]['shop_cat_onlyshop'] = $catsup;
                        }
                    } // @reviewlate: тут вместо кэтсапа можно использовать $arr_status[shopcat][hostshop]
                    
                    if ($type == "main" || $type == "cat" || $type == "keyw" || $type == "attr" || $type == "srch") { // сбор group_by
                        $main['group_by_cat'][$sql_cats2['shop_cat']][$sql_cats2['products_nnn']] = $sql_cats2['products_nnn'];
                        $main['group_by_shop'][$main[$sql_cats2['products_nnn']]['shop_cat_onlyshop']['nnn']][$sql_cats2['products_nnn']] = 
                                $sql_cats2['products_nnn'];
                    }
                } while ($sql_cats2 = mysql_fetch_assoc($sql_cats));

                // лишнее
                unset($main[$sel['nnn']]['price_sales'], $main[$sel['nnn']]['price_guest'], 
                        $main[$sel['nnn']]['price_distr'], $main[$sel['nnn']]['price_opt'], 
                        $main[$sel['nnn']]['prior_sales'], $main[$sel['nnn']]['prior_distr'], 
                        $main[$sel['nnn']]['prior_guest'], $main[$sel['nnn']]['prior_opt']);

                // есть цена
                if (@$return_price[0] > 0) {
                    $main[$sel['nnn']]['price'] = $return_price[0];
                    $main[$sel['nnn']]['price_star'] = @$return_price[1];
                    $main[$sel['nnn']]['price_starter'] = $return_price[2];
                } // цена после выбора
                
                // prd_in_grps но только если type=keyw, attr (т.к. будем искать группы, внутри кот. есть товары с искомым запросом)
                if ($sel['type'] != "grp" && ($type == "attr" || $type == "keyw")) {
                    $grp_check_in[$sel['nnn']] = $sel['nnn'];
                }
            } while ($sel = mysql_fetch_assoc($sel_sql));


            ///////// PRD_IN_GRPS - ищем группы
            if (count(@$grp_check_in) > 0) {
                $grp_check = mysql_kall("SELECT products.nnn, grp_nnn FROM " . DB_PREFIX . "products, " . DB_PREFIX . "products_2_cats
                  WHERE " . DB_PREFIX . "products.status!='hid' AND grp_nnn!='' AND 
                      " . DB_PREFIX . "products.nnn=" . DB_PREFIX . "products_2_cats.products_nnn " . @$add_sql." GROUP BY products.nnn");
                if (mysql_num_rows($grp_check) > 0) {
                    $grp_check2 = mysql_fetch_assoc($grp_check);
                    do { 
                        $grp_nnn2 = explode(",", $grp_check2['grp_nnn']);
                        foreach ($grp_nnn2 as $grp_nnn3) {
                            if (trim($grp_nnn3) == "") {
                                continue;
                            }
                            if (isset($grp_check_in[trim($grp_nnn3)])) {
                                $main[trim($grp_nnn3)]['prd_in_grps'][$grp_check2['nnn']] = $grp_check2['nnn'];                                
                            }
                        }
                    } while ($grp_check2 = mysql_fetch_assoc($grp_check));
                }
                unset($main[$sel['nnn']]['prd_in_grps']['']);
            }

            // ГРУППЫ
            if (count(@$grp_collect) > 0) {
                foreach ($grp_collect as $k => $v) {
                    $v = trim($v);
                    // меняем цену, если она не фиксирована
                    $return_arr = $this->product_group($v, $main[$k]['price'], 0, @$_SESSION['customers_id']); // LEVEL 3->LEVEL 2
                    // out -> grp_nazv, grp_weight, {grp_weight_flag}, priors[], price_star, price, 
                    // {price_grp_currency, price_starter, price_grp_currency_starter}, img

                    if (@$return_arr['price'] > 0) {
                        $main[$k]['price'] = $return_arr['price'];
                    }
                    
                    if (@$return_arr['price_grp_currency'] > 0) {
                        $main[$k]['price_grp_currency'] = $return_arr['price_grp_currency'];
                    } // false - если фикс. цена
                    
                    if (@$return_arr['price_starter'] > 0) {
                        $main[$k]['price_starter'] = $return_arr['price_starter'];
                    } // false - если фикс. цена
                    
                    if (@$return_arr['price_grp_currency_starter'] > 0) {
                        $main[$k]['price_grp_currency_starter'] = $return_arr['price_grp_currency_starter'];
                    } // false - если фикс. цена
                    
                    if (@$return_arr['img'] != "") {
                        $main[$k]['img'] = $return_arr['img'];
                    } // если есть, показываем официальную картинку вместо нескольких
                    
                    if (isset($return_arr['price_star'])) {
                        $main[$k]['price_star'] = $return_arr['price_star'];
                    }
                    if (@$return_arr['grp_weight'] > 0) {
                        $main[$k]['grp_weight'] = $return_arr['grp_weight'];
                    }
                    if (@$return_arr['grp_weight_flag'] > 0) {
                        $main[$k]['grp_weight_flag'] = $return_arr['grp_weight_flag'];
                    }
                    if (@$return_arr['grp_nazv'] != "") {
                        $main[$k]['grp_nazv'] = $return_arr['grp_nazv'];
                    }
                }
            }

            unset($main['']);

           // $_SESSION['cache']['products'][SHOP_NNN][$ii][$type][$group_img_flag][$startfrom][$_SESSION['customers_id']]['sql'] = serialize($main);
           // $_SESSION['cache']['products'][SHOP_NNN][$ii][$type][$group_img_flag][$startfrom][$_SESSION['customers_id']]['time'] = time();

            return $main;
        } // sel_sql>0
    }

    //
    //

    function collect_products_full($i) { // собираем все возможные данные по товару
        Debug::log();
        $add_sql=cats(); $arr_status=cats('arr');
        $sql="SELECT ".DB_PREFIX."products.*  FROM ".DB_PREFIX."products, ".DB_PREFIX."products_2_cats WHERE ".DB_PREFIX."products.nnn=".DB_PREFIX."products_2_cats.products_nnn AND ".DB_PREFIX."products.nnn='".$i."' AND status!='hid' ".@$add_sql;

         $sel_sql=mysql_kall($sql) or die(mysql_error());
        if(mysql_num_rows($sel_sql)>0) {

        $sel=mysql_fetch_assoc($sel_sql);

        foreach($sel as $k=>$v) { $main[$k]=$v; }

        $main['ordered_month']=ceil($sel['ordered']/((time()-$sel['dat'])/60/60/24/31));
        $main['ordered_day']=ceil($sel['ordered']/((time()-$sel['dat'])/60/60/24));
        $main['viewed_month']=ceil($sel['viewed']/((time()-$sel['dat'])/60/60/24/31));
        $main['viewed_day']=ceil($sel['viewed']/((time()-$sel['dat'])/60/60/24));
        if($main['ordered_month']>$sel['ordered']) { $main['ordered_month']=$sel['ordered']; }
        if($main['ordered_day']>$sel['ordered']) { $main['ordered_day']=$sel['ordered']; }
        if($main['viewed_month']>$sel['viewed']) { $main['viewed_month']=$sel['viewed']; }
        if($main['viewed_day']>$sel['viewed']) { $main['viewed_day']=$sel['viewed']; }

        // даже для групп определяем цену сначала как для обычного товара !
        $return_price=$this->product_prices(array('price'=>$sel['price'],'price_sales'=>$sel['price_sales'],'price_distr'=>$sel['price_distr'],'price_guest'=>$sel['price_guest'],'price_opt'=>$sel['price_opt']),array('prior_distr'=>$sel['prior_distr'],'prior_guest'=>$sel['prior_guest'],'prior_sales'=>$sel['prior_sales'],'prior_opt'=>$sel['prior_opt']),0,@$_SESSION['customers_id'],'prd');
        // LEVEL 3 -> LEVEL 1 // out -> return_price[0], return_price[1], return_price[2]

        unset($main['price_sales'],$main['price_guest'],$main['price_distr'],$main['price_opt'],$main['prior_sales'],$main['prior_distr'],$main['prior_guest'],$main['prior_opt']);

        if(@$return_price[0]>0) { $main['price']=$return_price[0]; $main['price_star']=@$return_price[1]; $main['price_starter']=@$return_price[2]; } // цена после выбора

        // grp_nnn
        if($sel['grp_nnn']!="") { // собираем данные по группе
        
        // находим новую цену группы, если только она не фиксирована
        $return_arr=$this->product_group($sel['grp_nnn'],$main['price'],0,@$_SESSION['customers_id']); // LEVEL 3 -> LEVEL 2
        // out -> grp_nazv, grp_weight, {grp_weight_flag}, priors[], price_star, price, {price_grp_currency, price_starter, price_grp_currency_starter}, img

        if(@$return_arr['price']>0) { $main['price']=$return_arr['price']; }
        if(@$return_arr['price_grp_currency']>0) { $main['price_grp_currency']=$return_arr['price_grp_currency']; } // false - если фикс. цена
        if(@$return_arr['price_starter']>0) { $main['price_starter']=$return_arr['price_starter']; } // false - если фикс. цена
        if(@$return_arr['price_grp_currency_starter']>0) { $main['price_grp_currency_starter']=$return_arr['price_grp_currency_starter']; } // false - если фикс. цена
        if(@$return_arr['img']!="") { $main['img'][]=$return_arr['img']; }
        if(isset($return_arr['price_star'])) { $main['price_star']=$return_arr['price_star']; }
        if(@$return_arr['grp_weight']>0) { $main['grp_weight']=$return_arr['grp_weight']; }
        if(@$return_arr['grp_weight_flag']>0) { $main['grp_weight_flag']=$return_arr['grp_weight_flag']; }
        if(@$return_arr['grp_nazv']!="") { $main['grp_nazv']=$return_arr['grp_nazv']; }

        } // grp_nnn

        // img
         $sql_img=mysql_kall("SELECT img FROM ".DB_PREFIX."products_image WHERE products_nnn='".$i."' AND img!='' ORDER BY sort ASC");
        $sql_img2=mysql_fetch_assoc($sql_img);
        if(mysql_num_rows($sql_img)>0) {
            do { $main['img'][]=$sql_img2['img']; } while($sql_img2=mysql_fetch_assoc($sql_img)); }

        // cats
        
         $sql_cats=mysql_kall("SELECT shop_cat, basket_separate, prior, prior_bask, nazv FROM ".DB_PREFIX."products_2_cats, ".DB_PREFIX."catshop_config WHERE products_nnn='".$i."' AND ".DB_PREFIX."catshop_config.nnn=".DB_PREFIX."products_2_cats.shop_cat ".@$add_sql."");
        $sql_cats2=mysql_fetch_assoc($sql_cats);
        do {
        $main['shop_cat'][$sql_cats2['shop_cat']]=@$arr_status[$sql_cats2['shop_cat']]['status'];
        $main['shop_cat_currency'][$sql_cats2['shop_cat']]=@$arr_status[$sql_cats2['shop_cat']]['currency'];
        $main['shop_cat_nazv'][$sql_cats2['shop_cat']]=$sql_cats2['nazv'];
	$main['shop_cat_basketsep'][$sql_cats2['shop_cat']]=$sql_cats2['basket_separate'];
        if($sql_cats2['prior']=="1") { $main['shop_cat_priority']=$sql_cats2['shop_cat']; } else { if(!isset($main['shop_cat_priority'])) { $main['shop_cat_priority']=$sql_cats2['shop_cat']; } }
        
        if($sql_cats2['prior_bask']=="1") { 
                $catsup=cats_up($sql_cats2['shop_cat']);
                $main['shop_cat_onlyshop_arr'][$sql_cats2['shop_cat']]=$catsup;
                $main['shop_cat_onlyshop']=$catsup;  } else {
                                $catsup=cats_up($sql_cats2['shop_cat']);
                                $main['shop_cat_onlyshop_arr'][$sql_cats2['shop_cat']]=$catsup;
                                if(!isset($main['shop_cat_onlyshop'])) {
                                $main['shop_cat_onlyshop']=$catsup; }  }

        // shop_cat_priority - определяет откуда брать параметры для валюты и разделения по корзинам (доставка) если товар расположен в нескольких разделах
        // shop_cat_onlyshop - определяет откуда брать статусы магазина для разделения по корзинам (магазины)
        } while($sql_cats2=mysql_fetch_assoc($sql_cats));

        foreach($main['shop_cat_onlyshop_arr'] as $o1=>$o2) {
        if(!isset($main['shop_cat'][$o2['nnn']])) { // добавим в массив магазин товара
            $main['shop_cat'][$o2['nnn']]=@$arr_status[$o2['nnn']]['status'];
            $main['shop_cat_currency'][$o2['nnn']]=@$arr_status[$o2['nnn']]['currency'];
            $main['shop_cat_nazv'][$o2['nnn']]=$o2['nazv'];
            }}
            
        // keyws
         $keyws=mysql_kall("SELECT nnn,keyword FROM ".DB_PREFIX."products_keywords WHERE products_nnn='".$i."'");
        $keyws2=mysql_fetch_assoc($keyws);
        if(mysql_num_rows($keyws)>0) { do { $main['keywords'][$keyws2['nnn']]=$keyws2['keyword'];  } while($keyws2=mysql_fetch_assoc($keyws)); }

        // connects
         $connects=mysql_kall("SELECT products1_nnn, products2_nnn FROM ".DB_PREFIX."products_2_products WHERE products1_nnn='".$i."' OR products2_nnn='".$i."'");
        $connects2=mysql_fetch_assoc($connects);
        if(mysql_num_rows($connects)>0) {
            do {
                if($connects2['products1_nnn']!=$i) { $main['products_connects'][$connects2['products1_nnn']]=$connects2['products1_nnn']; }
                if($connects2['products2_nnn']!=$i) { $main['products_connects'][$connects2['products2_nnn']]=$connects2['products2_nnn']; }
            } while($connects2=mysql_fetch_assoc($connects)); }

        // attrs
        if($sel['type']=="prd") { $ii=$i; $priors[$ii]=array('prior_distr'=>$sel['prior_distr'],'prior_guest'=>$sel['prior_guest'],'prior_sales'=>$sel['prior_sales'],'prior_opt'=>$sel['prior_opt']);
        } else { $ii=$sel['grp_nnn']; $priors=@$return_arr['priors']; } // $return_arr['priors'][] - остался после функции product_group
        
        $return_attr=$this->product_attrs($ii,$sel['type'],$priors); // LEVEL 3->LEVEL 2
        // out -> [prd_id][attr_name][attr_val] attr_nnn, attr_type, attr_descr, change, attr_dominant, price, price_starter

        if(count(@$return_attr)>0) { $main=$main+$return_attr; } // объединяем массив со свойствами с остальными

        // reviews & rates
         $reviews=mysql_kall("SELECT * FROM ".DB_PREFIX."products_reviews WHERE products_nnn='".$i."'");
        if(mysql_num_rows($reviews)>0) {
            $reviews2=mysql_fetch_assoc($reviews);
            do {
                $main['review'][$reviews2['nnn']]['dat']=$reviews2['dat'];
                $main['review'][$reviews2['nnn']]['avtor']=$reviews2['avtor'];
                $main['review'][$reviews2['nnn']]['customers_id']=$reviews2['customers_id'];
                $main['review'][$reviews2['nnn']]['txt']=$reviews2['txt'];
                $main['rate']['sum']=$main['rate']['sum']+$reviews2['rate'];
                if($reviews2['rate']>0) { $main['rate']['nums']=$main['rate']['nums']+1; }
                $main['rate_yes']=$main['rate_yes']+$reviews2['vote_y'];
                $main['rate_no']=$main['rate_no']+$reviews2['vote_n'];
                } while($reviews2=mysql_fetch_assoc($reviews));
                if(@$main['rate']['nums']>0) { $main['rate']['rate_avr']=($main['rate']['sum']/$main['rate']['nums']); }
                if(($main['rate_yes']+$main['rate_no'])>0) {
                $main['rate']['rate_yn_avr']=(($main['rate_yes']*5)+($main['rate_no']*1))/($main['rate_yes']+$main['rate_no']);
                if($main['rate']['rate_avr']<=0) { $d=1; } else { $d=2; }
                $main['rate']['rate_avr_all']=round(($main['rate']['rate_avr']+$main['rate']['rate_yn_avr'])/$d,2);
                }
                }

        // check groups
        if($sel['type']!="grp") {
             $grp_check=mysql_kall("SELECT products.nnn FROM ".DB_PREFIX."products, ".DB_PREFIX."products_2_cats WHERE ".DB_PREFIX."products.nnn=".DB_PREFIX."products_2_cats.products_nnn AND (grp_nnn='".$i."' OR grp_nnn LIKE '".$i.",%%%' OR grp_nnn LIKE '%%%,".$i.",%%%' OR grp_nnn LIKE '%%%,".$i."' OR grp_nnn LIKE '%%%, ".$i.",%%%' OR grp_nnn LIKE '%%%, ".$i."') ".@$add_sql."") or die(mysql_error());
             if(mysql_num_rows($grp_check)>0) { $grp_check2=mysql_fetch_assoc($grp_check);
            do { $main['prd_in_grps'][$grp_check2['nnn']]=$grp_check2['nnn']; } while($grp_check2=mysql_fetch_assoc($grp_check)); }
            unset($main['prd_in_grps']['']); }

        return $main;
        }


        }

    //
    //
    // LEVEL 4
    //          collect_attrs, collect_pig
    //

    function collect_attrs($attrs,$prd_type,$grp_nnn="",$currency,$catshop_currency,$currency_price,$skip_flag="0") {
        Debug::log();
        // in -> [prd_id][attr_name][attr_val] attr_nnn, attr_type, attr_descr, change, attr_dominant, price, price_starter
        //// attrs- array, prd_type - prd,grp, grp_nnn - для группы, currency - коэф товара, catshop_currency - коэф магазина
        // currency_price - переведенный по коэф цена товара, skip_flag - для группы, если 1 - значит фиксированная цена и свойства ничего не меняют
        // out - attr_choose, attr_descr

        $arr['attrs']=$attrs;
        $arr['type']=$prd_type;
        $arr['grp_nnn']=$grp_nnn;
        $arr['currency']=$currency;
        $arr['currency_price']=$currency_price;
        $dominant_name = "";
        
        if(count($arr['attrs'])>0) {
            foreach($arr['attrs'] as $k=>$v) { if(count($v)>0) {
            foreach($v as $kk=>$vv) { if(count($vv)>0) { foreach($vv as $kkk=>$vvv) { if(count($vvv)>0) { 
            foreach($vvv as $kkkk=>$vvvv) { if($vvvv['attr_dominant']== "1") { $dominant_name = $kkk; break; } } 
            ksort($vvv);
            foreach($vvv as $kkkk=>$vvvv) { 
                                if(count($vvv)>1) { // если нет выбора, то цена не может меняться и в корзине не показывать
                                    $price_attr=""; 
                                    // $kkkk - значение: attr_nnn! attr_type!, attr_descr-, change!, attr_dominant!, price!
                                    if(trim($kkkk)=="") { continue; }
                                    if($kkk == $dominant_name) { $vvvv['attr_dominant'] = 1; } 
                                    if($vvvv['attr_type']=="choose") {

                                             // обр. цену, если это группа и цена не фиксированная
                                            if($arr['type']=="grp"&&$vvvv['price']>0&&$skip_flag!="1") {                                                
                                                $price_attr=$this->product_group($arr['grp_nnn'], 0, 
                                                        array($k=>$vvvv['attr_nnn']), @$_SESSION['customers_id']); // LEVEL 4 -> LEVEL 2
                                                // out -> grp_nazv, grp_weight, {grp_weight_flag}, priors[], price_star, price, 
                                                // {price_grp_currency, price_starter, price_grp_currency_starter}, img
                                                $price_attr_currency=@$price_attr['price_grp_currency']; $price_attr=$price_attr['price']; }

                                            if($arr['type']=="prd"&&$vvvv['price']>0) { $price_attr=$vvvv['price']; }

                                            // опр. что вообще делать дальше                                            
                                            if($vvvv['change']=="1"&&$vvvv['attr_dominant']=="1"&&@$price_attr>0) {

                                            $price_attr_format="";
                                            if($arr['type']!="grp") { 
                                                $price_attr=currency_converter($price_attr, $catshop_currency, $arr['currency']); } else {
                                                // если группа, то высчитываем разницу между новой ценой группы (price_attr_currency) 
                                                // и изначальной (currency_price)
                                                if(@$price_attr_currency>0) { // если новой цены нет, то значение передаем 0 (вообще, это ошибка)
                                                $price_attr=$price_attr_currency-$arr['currency_price']; // разница
                                                if($price_attr>=0) { $price_attr_format="+"; } // добавляем + для формы
                                                                            } else { $price_attr=0; } // ошибка
                                                unset($price_attr_currency);
                                                } // для группы

                                            if($price_attr<0||$price_attr>0) {
                                                        $price_attr_format=$price_attr_format.number_format($price_attr,2);
                                                        $str_opt=$kkkk."___".$price_attr."_";
                                                        $str_opt2=$kkkk." / ".$price_attr_format;
                                                } else { $price_attr=0; $str_opt2=$kkkk; $str_opt=$str_opt2; } // если 0, то ничего не меняем
                                            } else { $price_attr=0; $str_opt2=$kkkk; $str_opt=$str_opt2; } 
                                            // чтобы изм. цену должны быть соблюдены 3 условия: доминант-1, change-1, цена>0

                                            // $kk." ".$arr['type']." / ".$arr['currency_price']."
                                            //    / skip? ".$skip_flag." }{ ".$kkk." ".$kkkk." }{ change? <b>".$vvvv['change']."</b> dominant?
                                            //        <b>".$vvvv['attr_dominant']."</b> }{ ".$vvvv['price']." }{ ===> <b>".$price_attr."</b><br><Br>"; 
                                            // debug

                                                $arr['attr_choose'][$k][$kk][$kkk]=@$arr['attr_choose'][$k][$kk][$kkk].
                                                        "<option value='".$str_opt."'>".$str_opt2."</option>";
                                            } // choose
                                } // если нет выбора, то это не choose, а descr
                                
                                      if($vvvv['attr_type']=="descr"||$vvvv['attr_type']=="choose") { // собираем просто для описания и attr_id
                                          $arr['attr_descr'][$kkk][$kkkk]=$vvvv['attr_nnn'];
                                          }
                                    }

                                    if(isset($arr['attr_choose'][$k][$kk][$kkk])) { $arr['attr_choose'][$k][$kk][$kkk]="<select class='product_attr_choose' name='attr_".$k."_".$kk."_".$kkk."'><option value=''></option>".$arr['attr_choose'][$k][$kk][$kkk]."</select>"; }
        }}}}}}} // attrs
        $return_arr['attr_choose']=@$arr['attr_choose'];
        $return_arr['attr_descr']=@$arr['attr_descr'];
        return $return_arr;
        // out -> attr_choose, attr_descr
        }

    //
    //

    function collect_pig($prds) { // Products In Groups (PIG)
        // (костыль) спец функция для страниц товаров сгруп. по свойствам, ключ словам или результатам поиска. - для того, чтобы в результатах
        // также отображались группы, где есть встр. свойства, ключ слова или поисковый запрос
        Debug::log();
        if(count($prds)>0) { 
        $v4=array(); $v5=array();
        foreach($prds as $k=>$v) {
            if(isset($v['prd_in_grps'])) { 
                if(count($v['prd_in_grps'])>0) {                  
                    $v5=$v5+$v['prd_in_grps'];
                }
            }
        }
        
        if(count($v5)>0) { $v6=implode("', '",$v5); 
        $v3=$this->collect_products($v6,'nnn_connects');
        // LEVEL 4 -> LEVEL 3
        }
        if(count($v3)>0) {
            $prds=$prds+$v3;
        }
        return $prds;
        }
    }

    //
    //
    // LEVEL 5
    //          show_product, show_basket
    //

    function show_product($arr, $key_arr, $showtype="brief") { // arr - product array // $showtype=brief, full, tiny, tiny_bsk, compare
        // $arr['status'](buy predzakaz temp hid free), $arr['shop_cat'], $arr['shop_cat_currency'], 
        // $arr['currency'], $arr['img'], $arr['price_star'](0-5)
        // глобальный обработчки товара, корректирует вывод. вспомогательная функция
Debug::log();
        // kogda - показывает сообщение, если указана дата будущего появления товара
        if(isset($arr['kogda'])) { 
        $kogda2=ceil(($arr['kogda']-time())/60/60/24);
        if($kogda2>0) { $arr['kogda']="Товар появится через ".$kogda2." дн. (".date("d.m.Y",$arr['kogda']).")"; } else {
        $arr['kogda']=""; }}

        // img
        // убираем массив, если это корзина или страница товара, и группа        
        
        if(($showtype=="full"&&$arr['type']=="grp")||$showtype=="tiny_bsk"||$showtype=="compare") {
            if($arr['type']=="grp") { if($showtype=="compare") { $arr['img']=array_reverse($arr['img']); $arr_img=explode(";",$arr['img'][0]); $arr['img']=trim($arr_img[0]); } else { $arr['img']=@implode(";",array_reverse(@$arr['img'])); }
                } else {  $arr['img']=$arr['img'][0];  }
            }

            $arr['img_full'] = $arr['img'];
            
        if($arr['type']=="grp") { $arr['img']=img_mini(@$arr['img'],$showtype, $key_arr); } else { // если группа
        // если обычный товар
        if($showtype=="brief"||$showtype=="tiny"||$showtype=="tiny_bsk"||$showtype=="compare") { // по одной картинке
            $arr['img']=img_mini(@$arr['img'],$showtype,$key_arr);
            } else { // несколько картинок с возм. открытяи в отдельном окне
            $arr['img']=img_full(@$arr['img']);
            }}

        // product_connects
        if($showtype=="full") {
            if(count($arr['products_connects'])>0) {
            $v=implode("', '",$arr['products_connects']);
            $v2=$this->collect_products($v,'nnn_connects'); // LEVEL 5 -> LEVEL 3
            if(count($v2)>0) {
            $v2=$this->sort_products($v2, 'star', 'desc');
            foreach($v2 as $k=>$v) { $v3=$this->show_product($v, $k, 'tiny'); $v2[$k]=$v3; }
            $arr['products_connects']=$v2;}
            }
            }

        // prds_in_grps
        if(($showtype=="full"||$showtype=="compare")&&$arr['type']!="grp") {
            if(count($arr['prd_in_grps'])>0) { 
                 $v=implode("' OR ".DB_PREFIX."products.nnn='",$arr['prd_in_grps']);
                 $v2=$this->collect_products($v,'nnn'); // LEVEL 5 -> LEVEL 3
                 $v2=$this->sort_products($v2, 'star', 'desc');
                 foreach($v2 as $k=>$v) { $v3=$this->show_product($v, $k, 'tiny'); $v2[$k]=$v3; }
                 $arr['prd_in_grps']=$v2;
                }
            }

       // КОЭФФИЦИЕНТ, ВАЛЮТА И Т.П,
       // price_currency
       if(isset($arr['shop_cat_priority'])) { $catshop_currency=$arr['shop_cat_currency'][$arr['shop_cat_priority']]; } else { // опр по приоритету, а если нет то по старинке
       if(isset($arr['shop_cat_currency'])) { arsort($arr['shop_cat_currency']); foreach($arr['shop_cat_currency'] as $k=>$v) { $catshop_currency=$v; break; }}
       }
       // $arr['type']." -> ".@$arr['price_grp_currency']."/".@$arr['price_grp_currency_starter']."
       //<-> ".@$arr['currency_price']."/".@$arr['currency_price_starter']." <-> ".@$arr['price']."/".@$arr['price_starter']." <br>"; // debug
       
       if($arr['type']!="grp"||($arr['type']=="grp"&&!isset($arr['price_grp_currency']))) { // для товаров или групп с фиксированной ценой
       $arr['currency_price']=currency_converter($arr['price'],$catshop_currency,$arr['currency']);
       $arr['currency_price_starter']=currency_converter($arr['price_starter'],$catshop_currency,$arr['currency']);
       } else { $arr['currency_price']=$arr['price_grp_currency']; $arr['currency_price_starter']=$arr['price_grp_currency_starter']; } // в группе мы все сделали заранее

       if($arr['type']=="grp"&&!isset($arr['price_grp_currency'])) { $arr['grp_attr_skip_flag']="1"; }
       // если у группы цена фиксированная, то аттрибуты не могут менять цены, т.к. аттрибуты принадлежать к товарам и их ценам => ставим флажок

       // $arr['type']." -> ".@$arr['price_grp_currency']."/".@$arr['price_grp_currency_starter']."
       //<-> ".@$arr['currency_price']."/".@$arr['currency_price_starter']." <-> ".@$arr['price']."/".@$arr['price_starter']." <br>"; // debug
       ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       /////////
      
       // price_formated
       $arr['price_formated']=price_format($arr['currency_price'], $arr['currency_price_starter'], $arr['price_star']);

       // status
       $baskettxt=BASKET_TXT; $arr['basket_link']="";
       if($arr['status']=="predzakaz") { $baskettxt=BASKET_PREDZAKAZ_TXT; } // в зависимости от статуса меняем текст корзины

        // shop_cat, status
        // shop_cat -> статусы, shop_cat_priority -> приоритетный раздел, shop_cat_onlyshop -> приоритетный магазин
        // shop_cat_onlyshop_arr -> все магазины + connected_2_current - какова связь с текущим

        // TODO: проверить как это работает с подч. магазинами теперь; сделать более адекватную систему, 
        // так как иначе любой товар разм в разных магазинах перенаправляется в тот, кот. был создан первым
        if(isset($arr['shop_cat_priority'])) { $cat_status_k=$arr['shop_cat_priority']; $cat_status=$arr['shop_cat'][$arr['shop_cat_priority']]; } else {
        if(isset($arr['shop_cat'])) { asort($arr['shop_cat']);
        foreach($arr['shop_cat'] as $k=>$v) { $cat_status_k=$k; $cat_status=$v; break; }}}

        // сверяем статус товара в разделе со статусом магазина, к которому этот раздел принадлежит и заменяем, если тот меньше
        // но пропускаем если текущий магазин совпадает с магазином товара
        //$arr['shop_cat_onlyshop_arr'][$cat_status_k]['nnn']    
        
        if(@$arr['shop_cat_onlyshop_arr'][$cat_status_k]['nnn']!=SHOP_NNN) { // товар не относится к тек магазину

             if(@$arr['shop_cat_onlyshop_arr'][$cat_status_k]['connected_2_current']==""&&array_key_exists('connected_2_current',@$arr['shop_cat_onlyshop_arr'][$cat_status_k])) { // магазин товара не подчинен
                $cat_status=1; } else {  // магазин товара подчинен
        if(@$arr['shop_cat_onlyshop_arr'][$cat_status_k]['status']!=""&&@$arr['shop_cat_onlyshop_arr'][$cat_status_k]['status']>0&&@$arr['shop_cat_onlyshop_arr'][$cat_status_k]['status']<$cat_status) {
            // статус магазина товара перекрывает статус раздела
            $cat_status=@$arr['shop_cat_onlyshop_arr'][$cat_status_k]['status']; 
            }}}

        // определяем локальный это магазин или нет, если нет, то перенаправляем
        // TODO: именно здесь должна происходить основная доработка по статусам магазина (1-2-3-4) + отобр. разделов?
        if((@$cat_status=="1"||@$cat_status=="2")&&@$arr['shop_cat_onlyshop_arr'][$cat_status_k]['remote_addr']!="") { // локальной ссылки нет
        if(substr($arr['shop_cat_onlyshop_arr'][$cat_status_k]['remote_addr'],-1)!="/") { $arr['shop_cat_onlyshop_arr'][$cat_status_k]['remote_addr'].="/"; }
        $arr['nazv_link']=$arr['shop_cat_onlyshop_arr'][$cat_status_k]['remote_addr']."product/".$key_arr;
        // TODO: если данные о товаре из xml, то nnn из массива должно быть?! позже
        if($cat_status=="2"&&($arr['status']=="buy"||$arr['status']=="predzakaz"||$arr['status']=="free")) {
        $arr['basket_link']="<a href=".$arr['shop_cat_onlyshop_arr'][$cat_status_k]['remote_addr']."product/".$key_arr."/add2cart>".$baskettxt."</a>"; } // TODO: доработать

        } else { // локальная ссылка есть
        $arr['nazv_link']=MAINURL."/product/".$key_arr;
        if($arr['status']=="buy"||$arr['status']=="predzakaz"||$arr['status']=="free") { // TODO: predzakaz, free
        //if($showtype=="full") {
        //    $arr['basket_link']="<input type=\"submit\" title='".$baskettxt."' name=add2cart value='".$baskettxt."' class='add2cart'>";
        //    } else {
            if(BASKET_IMG == "") { $arr['basket_link']=$baskettxt; 
            } else { $arr['basket_link']="<input type=\"image\" title='".$baskettxt."' name=add2cart value='".$baskettxt."' src='".MAINURL."/template/".TEMPLATE."/images/".BASKET_IMG."'>"; }
        //}

        }}

        if($cat_status!="1"&&$arr['status']=="temp") { $arr['basket_link']=BASKET_NO_ORDER; $arr['price_formated']=""; } // TODO: доработать temp, predzakaz, free
        if($arr['status']=="predzakaz") { $arr['basket_link']=$arr['basket_link']; $arr['price_formated']=""; }
        if($arr['status']=="free") { $arr['price_formated']=BASKET_FREE; }

        if($arr['type']=="prd"&&$arr['star']<=0) { $arr['template_type']=0; }
        if($arr['type']=="prd"&&$arr['star']>0) { $arr['template_type']=1; }
        if($arr['type']=="grp"&&$arr['star']<=0) { $arr['template_type']=2; }
        if($arr['type']=="grp"&&$arr['star']>0) { $arr['template_type']=3; }

        if($showtype=="brief"||$showtype=="tiny") { if(strlen($arr['nazv'])>NAZV_TINY_LEN) { $arr['nazv_tiny']=substr($arr['nazv'],0,NAZV_TINY_LEN)."..."; } else { $arr['nazv_tiny']=$arr['nazv']; } }
        
        // PRICE ARRAY - засовываем все цены в один ключ
        $arr['all_prices']['price']=@$arr['price'];
        $arr['all_prices']['price_starter']=@$arr['price_starter'];
        $arr['all_prices']['currency_price']=@$arr['currency_price'];
        $arr['all_prices']['currency_price_starter']=@$arr['currency_price_starter'];
        $arr['all_prices']['price_grp_curerncy']=@$arr['price_grp_currency'];
        $arr['all_prices']['price_grp_currency_starter']=@$arr['price_grp_currency_starter'];
        $arr['all_prices']['price_formated']=@$arr['price_formated'];

        // attrs ///////////////////////////////////////////////////////////////////////////////
        if($showtype=="full"||$showtype=="compare"||$showtype=="tiny_bsk") { // k - num, kk- nnn, kkk - name

        // спец функция, которая оформляет уже существующий массив attrs в форму для корзины
        $return_attr=$this->collect_attrs($arr['attrs'], $arr['type'], @$arr['grp_nnn'], @$arr['currency'], @$catshop_currency, @$arr['currency_price'], @$arr['grp_attr_skip_flag']);
        // LEVEL 5 -> LEVEL 4 / out -> attr_choose, attr_descr

        $arr['attr_choose']=$return_attr['attr_choose'];
        $arr['attr_descr']=$return_attr['attr_descr'];
           } // attrs
        unset($arr['attrs']);
        ////////////////////////////////////////////////////////////////////////////////////

        // полная форма добавления в корзину
        if(@$arr['basket_link']!="") { // TODO: не показывать корзину в опр. случая, бесплатные товары?
        $everybody_forms=new forms;
        $add2bsk=$everybody_forms->add2basket_form($key_arr,@$arr['basket_link'],@$arr['attr_choose'],@$arr['attr_descr'],@$arr['grp_nazv'],@$showtype,@$arr['status']);
        $arr['full_basket_form']=$add2bsk; }
        //

        // OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT OUT
        if($showtype=="tiny"||$showtype=="tiny_bsk") { // удаляем почти все значения

        $tiny_arr['nazv']=@$arr['nazv'];
        $tiny_arr['grp_nazv']=@$arr['grp_nazv'];
        $tiny_arr['price_formated']=@$arr['price_formated'];
        $tiny_arr['currency_price_starter']=@$arr['currency_price_starter'];
        $tiny_arr['nazv_link']=@$arr['nazv_link'];
        $tiny_arr['price_star']=@$arr['price_star'];        
        $tiny_arr['img']=@$arr['img'];
        $tiny_arr['all_prices']=@$arr['all_prices'];
        
        if($showtype=="tiny") {

            $tiny_arr['basket_link']=@$arr['basket_link'];
            $tiny_arr['full_basket_form']=@$arr['full_basket_form'];
            $tiny_arr['type']=@$arr['type'];
            $tiny_arr['template_type']=$arr['template_type'];
            $tiny_arr['nazv_tiny']=$arr['nazv_tiny'];
            } else { // все для корзины

			// разделение корзины

			//
			
			
                $tiny_arr['attr_choose']=@$arr['attr_choose'];
                $tiny_arr['attr_descr']=@$arr['attr_descr'];
                $tiny_arr['type']=@$arr['type'];
                $tiny_arr['grp_nnn']=@$arr['grp_nnn'];
                $tiny_arr['status']=@$arr['status'];
                $tiny_arr['currency_price']=@$arr['currency_price'];

                $tiny_arr['shop_cat_status']=@$arr['shop_cat'][@$arr['shop_cat_onlyshop']['nnn']]; // к какому магазину относится товар
                $tiny_arr['shop_cat_nazv']=@$arr['shop_cat_nazv'][@$arr['shop_cat_onlyshop']['nnn']]; // назв. магаза, а не раздела
                $tiny_arr['shop_cat_basketsep']=@$arr['shop_cat_basketsep'][@$arr['shop_cat_priority']];
                $tiny_arr['shop_cat_onlyshop']=@$arr['shop_cat_onlyshop']['nnn'];

                    if(@$arr['type']=="grp") {
                    if(@$arr['grp_weight_flag']=="1") { // у одного из товаров группы не указан вес, ставим всей группе 0
                $tiny_arr['weight']="0";
                    } else { // веc группы
                $tiny_arr['weight']=@$arr['grp_weight'];
                    }} else { // вес товара
                $tiny_arr['weight']=@$arr['weight']; }

            } // tiny_bsk - данные для корзины
        unset($arr); $arr=$tiny_arr;
        } // tiny, tiny_bsk
        /////////////////////////////////////////////////// tiny, tiny_bsk ////////////////////////////////////////

        return $arr;
        }

    //
    //

    function show_basket() { 
        Debug::log();
        //
        $bask_out=""; $add_sql=cats();
        if(@$_SESSION['customers_basket_num']<=0) { $bask_out="{###}".BASKET_EMPTY."."; } else { 
        if(isset($_SESSION['customers_id'])) { $sql="SELECT prd_id, quantity, ".DB_PREFIX."customers_basket_lists.nnn, customers_id
            FROM ".DB_PREFIX."customers_basket_lists, ".DB_PREFIX."products_2_cats WHERE customers_id='".$_SESSION['customers_id']."' AND list_name='[basket]'
                AND ".DB_PREFIX."customers_basket_lists.prd_id=".DB_PREFIX."products_2_cats.products_nnn ".$add_sql." GROUP BY ".DB_PREFIX."customers_basket_lists.nnn
                    ORDER BY date_added ASC"; } else { $sessid=session_id();
        if(isset($sessid)&&$sessid!="") {
        $sql="SELECT ".DB_PREFIX."customers_basket_lists.nnn, prd_id, quantity, customers_id FROM ".DB_PREFIX."customers_basket_lists, ".DB_PREFIX."products_2_cats
            WHERE customers_id='0' AND temp_session='".$sessid."' AND list_name='[basket]'
                AND ".DB_PREFIX."customers_basket_lists.prd_id=".DB_PREFIX."products_2_cats.products_nnn ".$add_sql." 
                    GROUP BY ".DB_PREFIX."customers_basket_lists.nnn ORDER BY date_added ASC";
        }}
        if(@$sql=="") { $bask_out="{###}Ошибка сессии. ".BASKET_EMPTY."."; } else {
        $bsk_prd=mysql_call($sql) or die(mysql_error());
        if(mysql_num_rows($bsk_prd)<=0) { $bask_out="{###}".BASKET_EMPTY."."; } else {

            ///// ЧАСТЬ 1
            ////////////////////////////////////////////
            //////////////////////////

            $bp=mysql_fetch_assoc($bsk_prd);
            unset($bsk, $b_out, $norepeat);

            do {
            // количество
            $bsk[$bp['prd_id']][$bp['nnn']]['quantity']=$bp['quantity'];

            // собираем данные для товара и группы товара (делаем это 1 раз, потом используем собранную инфу
            if(isset($norepeat[$bp['prd_id']])) { $b_out=$b_out_backup[$bp['prd_id']]; } else { // COLLECT DATA
            $norepeat[$bp['prd_id']]=$bp['prd_id'];
            $b_out=$this->show_basket_collect_info($bp['prd_id']);
            $b_out_backup[$bp['prd_id']]=$b_out; } // COLLECT DATA
            if(@$b_out=="") { continue; } // данные по товару не получены (его нет в продаже, например)
            $bsk[$bp['prd_id']][$bp['nnn']]['data']=$b_out[$bp['prd_id']]['data'];
            
            // проверяем выбранные ранее свойства:
             $sql2=mysql_kall("SELECT ".DB_PREFIX."customers_basket_attr.nnn as nnn, attr_id,prd_id,grp_sub_id, attr_name, attr_val FROM ".DB_PREFIX."customers_basket_attr, ".DB_PREFIX."products_attr WHERE basket_id='".$bp['nnn']."' AND customers_id='".$bp['customers_id']."' AND ".DB_PREFIX."products_attr.nnn=".DB_PREFIX."customers_basket_attr.attr_id");
            if(mysql_num_rows($sql2)>0) { 
            $sql3=mysql_fetch_assoc($sql2);
            do {
            // подставляем ранее выбранные свойства в форму
            $form_change=$this->show_basket_attrs($bp['prd_id'], $bp['nnn'], $sql3, $b_out[$bp['prd_id']]['attr_form'], $b_out[$bp['prd_id']]['attr_form_shifr'], $b_out[$bp['prd_id']]['data']['type']);
            // out -> $form_change[bp[nnn]] attr_sel_name/attr_sel_val/attr_diff [$sql3['nnn']]
            $bsk[$bp['prd_id']][$bp['nnn']]['attr_diff_price']=@$bsk[$bp['prd_id']][$bp['nnn']]['attr_diff_price']+$form_change[$bp['nnn']][$sql3['nnn']]['attr_diff'];
            $form_change_save[$bp['nnn']][$sql3['nnn']]=$form_change[$bp['nnn']][$sql3['nnn']];
            } while($sql3=mysql_fetch_assoc($sql2)); }

            // меняем в текущей форме свойств те поля, где был сделан выбор
            if($b_out[$bp['prd_id']]['attr_form']!="") { $bsk[$bp['prd_id']][$bp['nnn']]['attr_form']=$b_out[$bp['prd_id']]['attr_form'];
            if(isset($form_change_save[$bp['nnn']])) { $temp_bsk=$b_out[$bp['prd_id']]['attr_form']; 
             foreach($form_change_save[$bp['nnn']] as $k=>$v) { $temp_bsk = strtr($temp_bsk, $v['attr_sel_change']); }
                        $bsk[$bp['prd_id']][$bp['nnn']]['attr_form']=$temp_bsk;
                    }
                foreach($b_out[$bp['prd_id']]['attr_form_shifr'] as $k=>$v) { 
                    $bsk[$bp['prd_id']][$bp['nnn']]['attr_form']=strtr($bsk[$bp['prd_id']][$bp['nnn']]['attr_form'],array("{#".$k."#}"=>$v));
                    $bsk[$bp['prd_id']][$bp['nnn']]['attr_form']=strtr($bsk[$bp['prd_id']][$bp['nnn']]['attr_form'],array("name='attr_".$k."'"=>"name='attr_".$k."[".$bp['prd_id']."][".$bp['nnn']."]'"));
                    }}
            } while($bp=mysql_fetch_assoc($bsk_prd));

            ///////////////////////////////////////
            ///// ЧАСТЬ 2
            ////////////////////////

        // сведение, группировка
        if(count($bsk)>0) {
        $bsk_all=$this->show_basket_mix($bsk);
        }

        $bask_out=@$bsk_all;


        }}}
        return $bask_out;
        }

    //
    //
    // LEVEL 0
    //          остальное
    //

    // костыли для корзины [1]
    function show_basket_collect_info($p_id) { // собираем данные по p_id из корзины // <-> collect_products_full!
Debug::log();
                    $v2=$this->collect_products_full($p_id); if($v2['type']=="") { return false; }
                    $v2=$this->show_product($v2, $p_id, 'tiny_bsk');

                    if($v2['status']!="buy") { unset($v2, $bsk[$p_id], $b_out[$p_id]); $form_out=""; } else { // оформляем заказ, толькое если статус buy

                    $form_out=""; if($v2['type']=="grp") { // группа
                         
                    $form_out_tmp="<table>"; foreach($v2['grp_nazv'] as $k=>$v) { if(isset($v2['attr_choose'][$k])) { if(is_array(@$v)) { foreach($v as $kk=>$vv) {
                    $form_out_tmp.="<tr><td colspan=3><strong>".$vv."</strong></td></tr>";  foreach($v2['attr_choose'][$k][$kk] as $kkk=>$vvv) {
                     
                    $form_out_tmp.="<tr><td width=15px></td><td valign=top>".$kkk."</td><td style='padding-left:5px;' valing=top>{#".$k."_".$kk."_".$kkk."#}</td></tr>";
                    $b_out[$p_id]['attr_form_shifr'][$k."_".$kk."_".$kkk]=$vvv;

                    }}}}}
                    $form_out_tmp.="</table>"; 
                    if($form_out_tmp!="<table></table>") { $form_out.=$form_out_tmp; }
                    } else { if(count($v2['attr_choose'])>0) { // одиночный товар:
                    $form_out.="<table>";  if(is_array(@$v2['attr_choose'][0][$p_id])) { foreach($v2['attr_choose'][0][$p_id] as $k=>$v) {
                    $form_out.="<tr><td valign=top>".$k."</td><td style='padding-left:5px;' valing=top>{#0_".$p_id."_".$k."#}</td></tr>";
                    $b_out[$p_id]['attr_form_shifr']["0_".$p_id."_".$k]=$v;
                    }
                    $form_out.="</table>";
                    }}}
                    
                    $b_out[$p_id]['attr_form']=$form_out;
                    $b_out[$p_id]['attr_choose_save']=$v2['attr_choose'];
                    unset($v2['attr_choose'], $v2['status']);
                    $b_out[$p_id]['data']=$v2; unset($v2);
                    }
                    return $b_out;
        }

    //
    //

    // костыли для корзины [2]
    function show_basket_attrs($p_id, $n, $sql3, $attr_form, $attr_form_shifr, $type="prd") {
        Debug::log();
                        $select_f="";
                        $shifr=$sql3['grp_sub_id']."_".$sql3['prd_id']."_".$sql3['attr_name'];
                        $select_f=$attr_form_shifr[$shifr]; // строчка, которую надо заменить

                        if(@$select_f!="") {
                            $select_f2=explode("<option value='".$sql3['attr_val']."",$select_f);
                            $select_f4=explode("'>",$select_f2[1]);
                            if(substr($select_f4[0],-1)=="_") { $attr_diff=substr($select_f4[0],3,-1); } else { $attr_diff=0; }
                            if($attr_diff>0&&$type=="grp") { $attr_diff_formatted="+"; } else { $attr_diff_formatted=""; }
                            $attr_form_name="attr_".$shifr."'>"; $select_f5=explode($attr_form_name,$select_f);
                            if($attr_diff<0||$attr_diff>0) {
                                $select_f=implode($attr_form_name."<option value='".$sql3['attr_val']."___".$attr_diff."_'>".$sql3['attr_val']." / ".$attr_diff_formatted.$attr_diff."</option>",$select_f5);
                            } else {
                                $select_f=implode($attr_form_name."<option value='".$sql3['attr_val']."'>".$sql3['attr_val']."</option>",$select_f5);
                            }
                            $attr_form_change[$n][$sql3['nnn']]['attr_diff']=$attr_diff;
                            $attr_form_change[$n][$sql3['nnn']]['attr_sel_change']["{#".$shifr."#}"]=$select_f;
                            
                        } else {} //
                        return $attr_form_change;
        }

    //
    //

    // костыли для корзины [3] сведение
    function show_basket_mix($bsk) {
        Debug::log();

        $bsk_all['total']['quantity']=0; $bsk_all['total']['pure_price']=0; $bsk_all['total']['current_price_no_attr']=0; $bsk_all['total']['current_price']=0;
        $bsk_all['total']['weight']=0; 

            foreach($bsk as $k=>$v) {

                $bsk_all[$k]['total_quantity']=0;
                $bsk_all[$k]['total_pure_price']=0;
                $bsk_all[$k]['current_price_no_attr']=0;
                $bsk_all[$k]['price_by_prd']="";
                $bsk_all[$k]['total_weight']=0;
                $bsk_all[$k]['price_by_prd_attr']="";
                $bsk_all[$k]['current_price']=0;

                        foreach($v as $kk=>$vv) {

                        $bsk_all[$k]['total_quantity']=$bsk_all[$k]['total_quantity']+$bsk[$k][$kk]['quantity'];

                        //if($bsk[$k][$kk]['data']['price_star']=="1") { // <s>если обычная скидочная цена, то считаем как pure</s>
                        //$bsk_all[$k]['total_pure_price']=$bsk_all[$k]['total_pure_price']+$bsk[$k][$kk]['data']['currency_price'];
                        //} else {
                          $bsk_all[$k]['total_pure_price']=$bsk_all[$k]['total_pure_price']+$bsk[$k][$kk]['data']['currency_price_starter'];
                        //} // отключаем т.к. лучше, когда всегда отображается скидка

                        $bsk_all[$k]['current_price_no_attr']=$bsk_all[$k]['current_price_no_attr']+$bsk[$k][$kk]['data']['currency_price'];
                        $bsk_all[$k]['price_by_prd']=$bsk_all[$k]['price_by_prd'].$bsk[$k][$kk]['data']['price_formated']."+";
                        $bsk_all[$k]['total_weight']=$bsk_all[$k]['total_weight']+$bsk[$k][$kk]['data']['weight'];

                                            if(@$bsk[$k][$kk]['attr_diff_price']<0||@$bsk[$k][$kk]['attr_diff_price']>0) {
                                            if($bsk[$k][$kk]['data']['type']=="grp") { // если группа, то производит сложение или вычитание; если товар, то просто заменяем
                        $add_prc=$bsk[$k][$kk]['data']['currency_price']+$bsk[$k][$kk]['attr_diff_price'];
                                            } else {
                        $add_prc=$bsk[$k][$kk]['attr_diff_price'];
                                            }} else { // diff_price=0 а следовательно используем нормальную текущую цену
                        $add_prc=$bsk[$k][$kk]['data']['currency_price'];
                                            }

                        $bsk_all[$k]['current_price']=$bsk_all[$k]['current_price']+$add_prc;
                        $bsk_all[$k]['price_by_prd_attr']=$bsk_all[$k]['price_by_prd_attr'].number_format($add_prc,2)."+";
                        $add_prc=0;

                        $bsk_all[$k]['nazv']=$bsk[$k][$kk]['data']['nazv'];
                        $bsk_all[$k]['nazv_link']=$bsk[$k][$kk]['data']['nazv_link'];
                        $bsk_all[$k]['type']=$bsk[$k][$kk]['data']['type'];
                        $bsk_all[$k]['form']['attr_form'][$kk]=@$bsk[$k][$kk]['attr_form'];
                        $bsk_all[$k]['img']=$bsk[$k][$kk]['data']['img'];
                        $bsk_all[$k]['price_star']=$bsk[$k][$kk]['data']['price_star'];
                        $bsk_all[$k]['form']['attr_descr']="<input type=hidden name=attrs_descr[".$k."] value='".@serialize($bsk[$k][$kk]['data']['attr_descr'])."'>";
                        }

                    $bsk_all[$k]['form']['quantity']="<input type=text name=quantity[".$k."] class='basketform' size=5 value='".$bsk_all[$k]['total_quantity']."'>";
                    $bsk_all[$k]['form']['del_prd']="<input type=checkbox name=delprd[".$k."] class='basketform'>";

                    $bsk_all['grp_by_shop'][$bsk[$k][$kk]['data']['shop_cat_status']][$bsk[$k][$kk]['data']['shop_cat_onlyshop']][$bsk[$k][$kk]['data']['shop_cat_basketsep']][$k]=$k;
                    $bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['nazv']=$bsk[$k][$kk]['data']['shop_cat_nazv'];

                    // суммируем по корзинам и магазинам
                    $bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['shop_price']=@$bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['shop_price']+$bsk_all[$k]['current_price'];
                    $bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['shop_quant']=@$bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['shop_quant']+$bsk_all[$k]['total_quantity'];
                    $bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['shop_weight']=@$bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['shop_weight']+$bsk_all[$k]['total_weight'];
                    // суммируме по доставке (если есть разделение)
                    $bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['totalbask'][$bsk[$k][$kk]['data']['shop_cat_basketsep']]['bask_price']=@$bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['totalbask'][$bsk[$k][$kk]['data']['shop_cat_basketsep']]['bask_price']+$bsk_all[$k]['current_price'];
                    $bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['totalbask'][$bsk[$k][$kk]['data']['shop_cat_basketsep']]['bask_quant']=@$bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['totalbask'][$bsk[$k][$kk]['data']['shop_cat_basketsep']]['bask_quant']+$bsk_all[$k]['total_quantity'];
                    $bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['totalbask'][$bsk[$k][$kk]['data']['shop_cat_basketsep']]['bask_weight']=@$bsk_all['shops'][$bsk[$k][$kk]['data']['shop_cat_onlyshop']]['totalbask'][$bsk[$k][$kk]['data']['shop_cat_basketsep']]['bask_weight']+$bsk_all[$k]['total_weight'];

                    $bsk_all[$k]['price_by_prd']=substr($bsk_all[$k]['price_by_prd'],0,-1);
                    $bsk_all[$k]['price_by_prd_attr']=substr($bsk_all[$k]['price_by_prd_attr'],0,-1);

                // суммируем всю корзину
                $bsk_all['total']['quantity']=$bsk_all['total']['quantity']+$bsk_all[$k]['total_quantity'];
                $bsk_all['total']['pure_price']=$bsk_all['total']['pure_price']+$bsk_all[$k]['total_pure_price'];
                $bsk_all['total']['current_price_no_attr']=$bsk_all['total']['current_price_no_attr']+$bsk_all[$k]['current_price_no_attr'];
                $bsk_all['total']['current_price']=$bsk_all['total']['current_price']+$bsk_all[$k]['current_price'];
                $bsk_all['total']['weight']=$bsk_all['total']['weight']+$bsk_all[$k]['total_weight'];
                if($bsk_all[$k]['price_star']>@$bsk_all['total']['price_star']) { $bsk_all['total']['price_star']=$bsk_all[$k]['price_star']; }
                //

                }

             $bsk_all['total']['discount_1']=$bsk_all['total']['current_price_no_attr']-$bsk_all['total']['pure_price'];
             $bsk_all['total']['discount_2']=$bsk_all['total']['current_price']-$bsk_all['total']['current_price_no_attr'];
             $bsk_all['total']['discount']=$bsk_all['total']['current_price']-$bsk_all['total']['pure_price'];

            //foreach($bsk_all['grp_by_shop'] as $k=>$v) {  foreach($v as $kk=>$vv) {  foreach($vv as $kkk=>$vvv) { foreach($vvv as $kkkk=>$vvvv) {
            //  $bsk_all['grp_by_shop'][$k][$kk][$kkk][$kkkk]=$bsk_all[$kkkk]; unset($bsk_all[$kkkk]);
            //}}}}

            return $bsk_all;
        }

    //
    //
        
    function sort_products($resort, $type="dat", $direction="desc", $limityes="0", $sortarr=array()) {
        Debug::log();
        //// type=dat, price,star,ordered(3),viewed(3),status,type, group-shopcat
        // shopcat_limit - только товары группы, по остальным лимит (smart sort)

        if(isset($resort)&&@$resort!=""&&count($resort)>0&&is_array($resort)&&$type!="as_array") { $kaunt_limit=0;
        foreach($resort as $k=>$v) { 
           if($type=="dat") { $resort2[$resort[$k]['dat']][$k]=$k; }
           if($type=="price") { $resort2[$resort[$k]['price']][$k]=$k; }
           if($type=="star") { $resort2[$resort[$k]['star']][$k]=$k; }
           if($type=="ordered_day") { $resort2[$resort[$k]['ordered_day']][$k]=$k; }
           if($type=="ordered_month") { $resort2[$resort[$k]['ordered_month']][$k]=$k; }
           if($type=="ordered") { $resort2[$resort[$k]['ordered']][$k]=$k; }
           if($type=="viewed_day") { $resort2[$resort[$k]['viewed_day']][$k]=$k; }
           if($type=="viewed_month") { $resort2[$resort[$k]['viewed_month']][$k]=$k; }
           if($type=="viewed") { $resort2[$resort[$k]['viewed']][$k]=$k; }
           if($type=="status") { $resort2[$resort[$k]['status']][$k]=$k; }
           if($type=="type") { $resort2[$resort[$k]['type']][$k]=$k; }
           if($type=="shopcat") { if($k=="group_by_cat"||$k=="group_by_shop") { $resort2[999999][$k]=$k;} else { foreach($v['shop_cat'] as $kk=>$vv) { $resort2[$kk][$k]=$k; } }}

           if($type=="shopcat_limit") { $resort2[$resort[$k]['smart_sort']][$k]=$k; } else { $kaunt_limit++; } // для shopcat_limit лимиты считаются только в непринадл разделу товарах
           if($limityes>0) { if($kaunt_limit>=$limityes) { break; }}
           }}

           if($type=="as_array") { $resort2['as_array']=$sortarr; }

           if(count(@$resort2)>0) {
           if($type!="as_array") { if($direction=="desc") { krsort($resort2); } else { ksort($resort2); }}
           foreach($resort2 as $k=>$v) { foreach($v as $kk=>$vv) { $resort3[$kk]=$resort[$kk]; 
           if($type=="shopcat_limit"&&$k=="1") { $kaunt_limit++; if($kaunt_limit>=$limityes&&$limityes>0) { break; }} // k=1: smart_sort:0,1
           }}} else { $resort3=$resort; }

        return $resort3;
        }

    //
    //

    function show_comments($i=SHOP_NNN, $limit="50") { // комменты где?
        Debug::log();
        $add_sql=cats('sql',$i); $arr_status=cats('arr',$i);
        $sql="SELECT ".DB_PREFIX."products_reviews.nnn, ".DB_PREFIX."products_reviews.products_nnn, ".DB_PREFIX."products_reviews.dat, avtor, customers_id, txt, rate, ".DB_PREFIX."products.nazv, ".DB_PREFIX."catshop_config.nazv AS catnazv, ".DB_PREFIX."catshop_config.status, ".DB_PREFIX."catshop_config.nnn AS catshop_nnn, ".DB_PREFIX."catshop_config.remote_addr, ".DB_PREFIX."catshop_config.remote_always
              FROM ".DB_PREFIX."products_reviews, ".DB_PREFIX."products, ".DB_PREFIX."products_2_cats, ".DB_PREFIX."catshop_config WHERE ".DB_PREFIX."products.status!='hid' AND ".DB_PREFIX."products_reviews.products_nnn=".DB_PREFIX."products.nnn ".$add_sql." AND ".DB_PREFIX."products_2_cats.products_nnn=".DB_PREFIX."products_reviews.products_nnn AND ".DB_PREFIX."products_2_cats.shop_cat=".DB_PREFIX."catshop_config.nnn ORDER BY ".DB_PREFIX."products_reviews.dat DESC LIMIT ".$limit."";
         $sel_sql=mysql_kall($sql) or die(mysql_error());
        if(mysql_num_rows($sel_sql)>0) {
            $sel_sql2=mysql_fetch_assoc($sel_sql);
            do {
                foreach($sel_sql2 as $k=>$v) { if($k=="nnn") { continue; }
                $main[$sel_sql2['nnn']][$k]=$v;
                }
                } while($sel_sql2=mysql_fetch_assoc($sel_sql));
            
            return $main;
            }
        }

    //
    //

    function add2list($post) { // list_name, prd_id - добавление в листы, но не в корзину. 
        Debug::log();
        if ($post['new_list_name'] != "") {
            $post['list_name'] = $post['new_list_name'];
        }
        if($post['list_name'] != "basket") {
        if(!isset($_SESSION['customers_id'])) { $cid="0"; $temp_session=session_id(); } else { $cid=$_SESSION['customers_id']; $temp_session=""; }

           if (mysql_num_rows(mysql_kall("SELECT list_name FROM " . DB_PREFIX . "customers_basket_lists 
                WHERE customers_id='" . $cid. "' AND temp_session='".$temp_session."' AND list_name='" . $post['list_name'] . "' 
                    AND prd_id='" . $post['prd_id'] . "'")) > 0) {
                mysql_call("UPDATE " . DB_PREFIX . "customers_basket_lists SET date_added='" . time() . "' 
                    WHERE customers_id='" . $cid . "'  AND temp_session='".$temp_session."' AND list_name='" . $post['list_name'] . "' 
                        AND prd_id='" . $post['prd_id'] . "'");
            } else {
                mysql_call("INSERT INTO " . DB_PREFIX . "customers_basket_lists (customers_id, list_name, prd_id, quantity, date_added, temp_session)
                VALUES ('" . $cid . "','" . trim($post['list_name']) . "','" . $post['prd_id'] . "','1','" . time() . "', '".$temp_session."')");
            }
            unset($_SESSION['customers_lists']);
             $_SESSION['send_login_message'] = "Товар добавлен в список \"" . $post['list_name'] . "\"";
        }
    }

    //
    //

    function add2basket($post="",$from="indoor") { // prd_id, attr_NNN_NNN_NNN=VALUE___NNN_ - добавление в корзину, для незарегеных и для зарег в бд
  Debug::log();
        if($from=="outdoor") { 
             if(isset($_COOKIE[SHOP_NNN.'_timeout_add2cart'])&&(time()<@$_COOKIE[SHOP_NNN.'_timeout_add2cart'])) {
                 $_SESSION['send_login_message']="Пожалуйста, подождите немного. Попробуйте еще раз."; return; } else {
                    setcookie(SHOP_NNN."_timeout_add2cart",(time()+5),"0","/",MAINURL_4); }}

         if(isset($post['attrs_descr'])) { $post['attrs_descr_uns']=@unserialize(stripslashes($post['attrs_descr'])); }
         if(!isset($_SESSION['customers_id'])) { $c_id="0"; $temp_session=session_id(); } else { $c_id=$_SESSION['customers_id']; $temp_session=""; }
         if(!isset($post['quantity'])) { $post['quantity']="1"; }
         // TODO: quantity сделать при добавлении более 1 товара!!! как-то по хитрому
         mysql_call("INSERT INTO ".DB_PREFIX."customers_basket_lists (customers_id, list_name, prd_id, quantity, date_added, temp_session)
                VALUES ('".$c_id."','[basket]','".$post['prd_id']."','".$post['quantity']."','".time()."','".$temp_session."')");
         $basket_id=mysql_insert_id();
         if(isset($post)&&@$post!=""&&count($post)>0&&is_array($post)) {
         foreach($post as $k=>$v) {
             if(substr($k,0,5)=="attr_") { $k2=explode("_",$k);
             if(substr($v,-1)=="_") { $v2=explode("___",$v); $v=trim($v2[0]); }
             $k22=$k2; unset($k22[0], $k22[1], $k22[2]); $k23=implode(" ",$k22);
             $attr_id=@$post['attrs_descr_uns'][trim($k23)][trim($v)];
             if($attr_id>0) {
                 mysql_call("INSERT INTO ".DB_PREFIX."customers_basket_attr (customers_id, temp_session, basket_id, grp_sub_id, prd_id, attr_id, date_added)
                              VALUES ('".$c_id."','".$temp_session."','".$basket_id."','".trim($k2[1])."','".trim($k2[2])."','".$attr_id."','".time()."')");
                 // $c_id." - ".$basket_id." - ".$post['prd_id']." - ".$attr_id."<br>"; // debug
                 // trim($k2[1])."/".trim($k2[2])." ".trim($k2[3]).": ".$v." = ".@$post['attrs_descr_uns'][trim($k2[3])][trim($v)]."<br>"; // debug
                 }
                 }
             }}
             $_SESSION['send_login_message']="Товар добавлен в корзину.";
             $_SESSION['customers_basket_num']=$_SESSION['customers_basket_num']+$post['quantity'];
        }

    //
    //

    function update_views($prd) { //
        Debug::log();
        mysql_call("UPDATE ".DB_PREFIX."products SET viewed=viewed+1 WHERE nnn='".$prd."'") or die(mysql_error());
        } // update views

    //
    //

    function update_basket($post="") {
        Debug::log();
        $_SESSION['basket_log_changed_q']=0; $_SESSION['basket_log_changed_a']=0;
        if(!isset($_SESSION['customers_id'])) { $c_id="0"; $temp_session=session_id(); } else { $c_id=$_SESSION['customers_id']; $temp_session=""; }
        if(isset($post)&&@$post!=""&&count($post)>0&&is_array($post)) {

             foreach($post as $k=>$v) { if(substr($k,0,5)=="attr_") { $k2=explode("_",$k);
             foreach($v as $kk=>$vv) { foreach($vv as $kkk=>$vvv) {
             // $k2[1]." / ".$k2[2]." / ".$k2[3]." / prd_id-".$kk." / basket_id-".$kkk." => ".$vvv."<br>"; // debug
             if(substr($vvv,-1)=="_") { $v2=explode("___",$vvv); $vvv=trim($v2[0]); }
             $k22=$k2; unset($k22[0], $k22[1], $k22[2]); $k23=implode(" ",$k22);
             $rearr[$kk][$kkk][$k2[1]."/".$k2[2]][trim($k23)]=trim($vvv);
             }}}}

        // автоудаление старых неавторизованных корзин            
        if(@filemtime(MAINURL_5."/temp/delete_all_temporary_baskets.txt")<(time()-(60*60*24*3))) { // 3 дня
            mysql_kall("DELETE FROM ".DB_PREFIX."customers_basket_lists WHERE customers_id='0' AND temp_session!='' AND date_added<='".(time()-(60*60*24*3))."'");
            mysql_kall("DELETE FROM ".DB_PREFIX."customers_basket_attr WHERE customers_id='0' AND temp_session!='' AND date_added<='".(time()-(60*60*24*3))."'");
            $f=fopen(MAINURL_5."/temp/delete_all_temporary_baskets.txt","w"); fwrite($f,"1"); fclose($f);
            }

        $old_quantity_basket_list=mysql_num_rows(mysql_call("SELECT nnn FROM ".DB_PREFIX."customers_basket_lists WHERE list_name='[basket]' AND customers_id='".$c_id."' AND temp_session='".$temp_session."'"));
        $old_quantity_basket_attr=mysql_num_rows(mysql_call("SELECT nnn FROM ".DB_PREFIX."customers_basket_attr WHERE customers_id='".$c_id."' AND temp_session='".$temp_session."'"));
       
        mysql_call("DELETE FROM ".DB_PREFIX."customers_basket_lists WHERE list_name='[basket]' AND customers_id='".$c_id."' AND temp_session='".$temp_session."'");
        mysql_call("DELETE FROM ".DB_PREFIX."customers_basket_attr WHERE customers_id='".$c_id."' AND temp_session='".$temp_session."'");

        $vall=0; $tm=time()-200; $vall_attr=0;
        if(isset($post['quantity'])&&@$post['quantity']!=""&&count($post['quantity'])>0&&is_array($post['quantity'])) { 
        foreach($post['quantity'] as $k=>$v) { if(isset($post['delprd'][$k])||$v=="0") {} else { $unser="";
        if(isset($post['attrs_descr'][$k])) { $unser=@unserialize(stripslashes($post['attrs_descr'][$k])); }

            $vall=$vall+$v;
            // $k."=> q:".$v."<br>"; // debug
            if(isset($rearr[$k])) {
            foreach($rearr[$k] as $kk=>$vv) { $v=$v-1; // добавляем товары со свойствами

            mysql_call("INSERT INTO ".DB_PREFIX."customers_basket_lists (customers_id, list_name, prd_id, quantity, date_added, temp_session)
                VALUES ('".$c_id."','[basket]','".$k."','1','".$tm."','".$temp_session."')");
                $basket_id=mysql_insert_id();
                $tm++;

                foreach($vv as $kkk=>$vvv) { foreach($vvv as $kkkk=>$vvvv) { // добавляем свойства
                $kkk2=explode("/",$kkk); 
                if(trim($vvvv)!=""&&@$unser[$kkkk][$vvvv]>0) { // свойство выбрано!
                // $kk.": ".$kkk2[0]." ".$kkk2[1]." =>".$kkkk."=>".$vvvv." => ".$unser[$kkkk][$vvvv]."<br>"; // debug
                       $vall_attr++;
                       mysql_call("INSERT INTO ".DB_PREFIX."customers_basket_attr (customers_id, temp_session, basket_id, grp_sub_id, prd_id, attr_id, date_added)
                              VALUES ('".$c_id."','".$temp_session."','".$basket_id."','".trim($kkk2[0])."','".trim($kkk2[1])."','".$unser[$kkkk][$vvvv]."','".time()."')");
                }}}
                if($v<=0) { break; } 
                }}
                
                if($v>0) { // добавляем в корзину товары без свойств или те, которых стало больше
                for($j=0;$j<$v;$j++) { 
                mysql_call("INSERT INTO ".DB_PREFIX."customers_basket_lists (customers_id, list_name, prd_id, quantity, date_added, temp_session)
                VALUES ('".$c_id."','[basket]','".$k."','1','".time()."','".$temp_session."')");
                }} //

            // "<br>";

            }}}

             if($old_quantity_basket_list!=$vall) { $_SESSION['basket_log_changed_q']=1; }
             if($old_quantity_basket_attr!=$vall_attr) { $_SESSION['basket_log_changed_a']=1; }
             $_SESSION['send_login_message']="Корзина обновлена.";
             $_SESSION['customers_basket_num']=$vall;
        }}

    //
    //

    function compare() { // COMPARE
        Debug::log();
            lists2session(); $compare_out=""; // проверим наличие товаров для сравнения
            if(!isset($_SESSION['customers_id'])) { $cid="0"; $temp_session=session_id(); } else { $cid=$_SESSION['customers_id']; $temp_session=""; }
            $c_lists4=@unserialize(@$_SESSION['customers_lists']);
                if(isset($c_lists4[COMPARE_LIST_NAME])&&@$c_lists4[COMPARE_LIST_NAME]>=1) {
                     $prds=mysql_kall("SELECT prd_id FROM ".DB_PREFIX."customers_basket_lists WHERE list_name='".COMPARE_LIST_NAME."' AND customers_id='".$cid."' AND temp_session='".$temp_session."'");
                    if(mysql_num_rows($prds)>0) {
                        $prds2=mysql_fetch_assoc($prds); $fields=explode(",",COMPARE_FIELDS);
                        do {
                        $compare_pre_out[$prds2['prd_id']]=$this->collect_products_full($prds2['prd_id']);
                        $compare_pre_out[$prds2['prd_id']]=$this->show_product($compare_pre_out[$prds2['prd_id']], $prds2['prd_id'], 'compare');
                        // какие поля для сравнения?
                            foreach($fields as $k=>$v) {
                               if(trim($v)=="prd_in_grps") { $compare_out[trim($v)][$prds2['prd_id']]=count($compare_pre_out[$prds2['prd_id']][trim($v)]); continue; }                             
                               $compare_out[trim($v)][$prds2['prd_id']]=$compare_pre_out[$prds2['prd_id']][trim($v)];
                            }
                            $compare_out['out_of_list'][$prds2['prd_id']]="<form method='post' action='".MAINURL_2."/user/done/'><input type=hidden name='prdid' value='".$prds2['prd_id']."'><input type=submit name='outoflist' value='убрать'></form>";
                            // TODO: перенести форму согласно шаблону в forms
                            unset($compare_pre_out[$prds2['prd_id']]);
                        } while($prds2=mysql_fetch_assoc($prds));


                    }} else { $compare_out="";  $_SESSION['send_login_message']="Нет товаров в списке для сравнения"; }

                return $compare_out;
        } // compare

    //
    //

    function compare_del($post) { // COMPARE_DEL
        Debug::log();
        if(!isset($_SESSION['customers_id'])) { $cid="0"; $temp_session=session_id(); } else { $cid=$_SESSION['customers_id']; $temp_session=""; }
        if($post['prdid']>0) {
            mysql_call("DELETE FROM ".DB_PREFIX."customers_basket_lists WHERE list_name='".COMPARE_LIST_NAME."' AND customers_id='".$cid."' AND temp_session='".$temp_session."' AND prd_id='".$post['prdid']."'");
            $_SESSION['send_login_message']="Товар удален из списка для сравнения";
            unset($_SESSION['customers_lists']); 
            }
        }

    //
    //
        
    function add_review($post) { // review
        Debug::log();
           if(trim($_SESSION['number1'])!=""&&trim($post['vercode'])!="") {
           if($_SESSION['number1']==mb_strtolower($post['vercode'])) {
           if($post['txt']!=""||$post['rate_star']>0) {
           if(isset($_COOKIE[SHOP_NNN.'_timeout_review'])&&(time()<@$_COOKIE[SHOP_NNN.'_timeout_review'])) {
           $_SESSION['send_login_message']="Перед написанием следующего отзыва, подождите, пожалуйста еще <b>".($_COOKIE[SHOP_NNN.'_timeout_review']-time())."</b> сек.";  } else {
           $timeout0=time()+(60*COMMENTS_TIMEBREAK);
           setcookie(SHOP_NNN."_timeout_review",$timeout0,"0","/",MAINURL_4);

         if(isset($_SESSION['customers_id'])) { $c_id=$_SESSION['customers_id']; } else { $c_id=0; }
         if(trim($post['avtor'])=="") { $post['avtor']=ANONIM_NAME; }
         if(!isset($post['rate_yn'])) { $post['rate_yn']="vote_y"; } 
           mysql_call("INSERT INTO ".DB_PREFIX."products_reviews (products_nnn, dat, avtor, customers_id, txt, rate, ".$post['rate_yn'].")
                         VALUES ('".$post['prdid']."','".time()."','".textprocess($post['avtor'],'sql')."','".$c_id."','".textprocess($post['txt'],'sql')."','".$post['rate_star']."','1')");
           $_SESSION['send_login_message']="Отзыв добавлен.";         
               }} else { $_SESSION['send_login_message']="Комментарий не добавлен, т.к. его нет."; }
                } else { $_SESSION['send_login_message']="Неверный проверочный код."; }
                } else { $_SESSION['send_login_message']="Проверочный код не указан."; }
        } // review rate
 
    //
    //
        
    function add_review_only_rate($post) { // review
        Debug::log();
        if($post['prdid']!="") {
        if(isset($_COOKIE[SHOP_NNN.'_timeout_rate_yn'])&&(time()<@$_COOKIE[SHOP_NNN.'_timeout_rate_yn'])) {
        $_SESSION['send_login_message']="Пожалуйста, подождите еще <b>".($_COOKIE[SHOP_NNN.'_timeout_rate_yn']-time())."</b> сек.";  } else {
           $timeout0=time()+(60*COMMENTS_TIMEBREAK);
           setcookie(SHOP_NNN."_timeout_rate_yn",$timeout0,"0","/",MAINURL_4);
         if(isset($_SESSION['customers_id'])) { $c_id=$_SESSION['customers_id']; } else { $c_id=0; }
         if(isset($post['vote_y_x'])) { $post['rate_yn']="vote_y"; $msg="Вам понравился товар."; } else { $post['rate_yn']="vote_n"; $msg="Вам не понравился товар."; }
           mysql_call("INSERT INTO ".DB_PREFIX."products_reviews (products_nnn, dat, avtor, customers_id, txt, rate, ".$post['rate_yn'].")
                         VALUES ('".$post['prdid']."','".time()."','','".$c_id."','','','1')");
           $_SESSION['send_login_message']=$msg;
            } }
        } // only rate

    //
    //
        
    // подготовка списков товаров, @reviewlate: жесткая привязка к опредленному выводу (флоат лефт, строка и тд) - не оч хорошо
    function product_listing($prd, $look = "", $group_flag = "0", $used_income = "", $inline = LIMIT_PRD_IN_LINE, 
            $maxprd = LIMIT_TOP_PRODUCTS, $group_by = "", $skip_show_product = "0", $start_from = "0", 
            $main_page_flag = "0", $arrflag = "0") {
        Debug::log();
        //// prd - массив с товарами, inline - кол-во товаров в линии, $maxprd - максимальное количество
        //// group_flag - группировка, $look - список всех сущ. разделов
        //// used_income - использованные прд, убираем повторы, group_by - вывод по разделам,
        //// skip_show_product - не обрабатываем функцией show_product,
        //// start_from - ?, main_page_flag = вывод для главной, другой темплейт; arrflag - отдаем необработанный массив!

        $prd_filename = MAINURL_5 . "/template/" . TEMPLATE . "/product_list.php";
        $templ_prd = get_include_contents($prd_filename); // ###шаблон_ввод
        $templ_prd_types = explode("[####]", $templ_prd); //  0, 1, 2, 3, 4; 5 -> назв раздела, если сгрупп.
        $templ_prd3 = "";

        $limit = 0;
        $kaunt = 0;
        if (count($prd) > 0) {
            $prd = array_slice($prd, $start_from, NULL, true);

            foreach ($prd as $k => $v) {
                if ($v['type'] == "") {
                    continue;
                }
                if (isset($used_income[$k])) {
                    $used[$k] = $k;
                    continue;
                }
                if ($k == "group_by_shop" || $k == "group_by_cat") {
                    continue;
                }

                $img_check = count(explode(";", $prd[$k]['img']));
                if ($kaunt == ($inline - 1) && $prd[$k]['type'] == "grp" && $img_check > 1) {
                    $kaunt = $kaunt + 2;
                }

                $template_type = 0;


                if ($skip_show_product == "1") {
                    $prd2 = $v;
                } else {
                    $prd2 = $this->show_product($v, $k, 'brief');
                }

                $template_type = @$prd2['template_type'];

                if ($img_check <= 1 && $prd[$k]['type'] == "grp") {
                    $template_type = 4;
                }

                if ($prd[$k]['type'] == "prd" && $prd2['star'] == "1" && strlen($prd2['nazv_tiny']) > (NAZV_TINY_LEN / 2)) {
                    $prd2['nazv_tiny'] = substr($prd2['nazv_tiny'], 0, ceil(NAZV_TINY_LEN / 2)) . "...";
                }

                if ($main_page_flag == "1") {
                    $template_type = 10;
                    $prd2['nazv_tiny'] = $prd2['nazv'];
                }

                if ($kaunt >= $inline && ($limit < $maxprd || $maxprd <= 0)) {
                    $kaunt = 0;
                    $templ_prd3 = $templ_prd3 . "<div class='products_line'></div>";
                }
                // линия завершена

                if ($prd[$k]['type'] == "grp") {
                    $kaunt100 = 2;
                } else {
                    $kaunt100 = 1;
                }
                $narrow_ajax = 1;
                if ($inline <= 4) {
                    $narrow_ajax = $kaunt + $kaunt100;
                }

                $templ_prd2 = strtr(@$templ_prd_types[$template_type], array(
                    "{IMG}" => $prd2['img'],
                    "{NAME_TINY}" => $prd2['nazv_tiny'],
                    "{NAME}" => $prd2['nazv'],
                    "{BASKET_FORM}" => $prd2['full_basket_form'],
                    "{NAME_LINK}" => $prd2['nazv_link'],
                    "{PRICE}" => $prd2['all_prices']['price_formated'],
                    "{IMG_PATH}" => MAINURL . "/template/" . TEMPLATE . "/images",
                    "{GROUP_PRD_NAME}" => GROUP_PRD_NAME,
                    "{STAR_PRD_NAME}" => STAR_PRD_NAME,
                    "{INLINE}" => $narrow_ajax,
                ));

                $prd2['template_type_remember'] = $templ_prd_types[$template_type];

                if ($v['type'] == "prd" || $img_check <= 1) {
                    $limit++;
                    $kaunt++;
                } else {
                    $limit++;
                    $kaunt = $kaunt + 2;
                } // limit всегда +1

                $templ_arr[$k] = $prd2;
                if ($group_flag == "1") {
                    $templ_prd4[$k] = $templ_prd2;
                    $templ_prd4_type[$k] = $v['type'];
                    $templ_prd4_countimg[$k] = $img_check;
                } else {
                    if ($limit > $maxprd && $maxprd > 0) {
                        break;
                    } else {
                        $templ_prd3 = $templ_prd3 . $templ_prd2;
                        $used[$k] = $k;
                    }
                }
            }
        }
        
        // группировка:
        if ($group_flag == "1") {
            $templ_prd3 = $this->product_listing_group($prd, $templ_prd4, $templ_prd4_type, $templ_prd4_countimg, $templ_prd_types[5], $look, $inline, $maxprd, $group_by);
            $used = @array_merge(@$used, @$templ_prd3['used']);
            $used = @array_combine(@$used, @$used);
            $templ_prd3 = $templ_prd3['templ_prd'];
        }
        if (@$used_income != "") {
            $used = @array_merge(@$used, @$used_income);
            $used = @array_combine(@$used, @$used);
        }

        if ($arrflag == "1") {
            $templ_prd3 = $templ_arr;
        } // возвращаем массив а не форматированный список

        return array("templ_prd" => $templ_prd3, "used" => @$used);
    }

    //
    //       

    function product_listing_group($prd, $templ_prd4, $templ_prd4_type, $templ_prd4_countimg, $templ_prd_types, $look, $inline, $maxprd, $grp_by="") {
        Debug::log();
            if(count($templ_prd4)>0) {

            if((count($prd['group_by_shop'])>1&&$grp_by!="cat")||$grp_by=="shop") { // группируем по магазинам
            $k3="group_by_shop";  } else { // по разделам
            $k3="group_by_cat"; }
            if(isset($prd[$k3])) {
            $templ_prd5=""; if(isset($_SESSION['check_used'])) { $used[$_SESSION['check_used']]=$_SESSION['check_used']; }
            foreach($prd[$k3] as $k=>$v) { $limit_top=0; $kaunt_4_height=0; if(count(@$used)>0) { foreach($used as $uuu=>$uuu2) { unset($v[$uuu]); } }
            if(count($v)>0) {
            if($look['podrazdel'][$k]['remote_addr']!=""&&$look['podrazdel'][$k]['remote_always']=="1") {} else { $look['podrazdel'][$k]['remote_addr']=MAINURL."/catalog/".$k; }
            $templ_prd5=$templ_prd5."".strtr($templ_prd_types,array("{FILTR_NAME}"=>@$look['podrazdel'][$k]['nazv'],"{FILTR_LINK}"=>@$look['podrazdel'][$k]['remote_addr']));
            foreach($v as $k2=>$v2) { if(isset($templ_prd4[$v2])) {
                if($kaunt_4_height==($inline-1)&&$templ_prd4_type[$v2]=="grp"&&$templ_prd4_countimg[$v2]>1) { $kaunt_4_height=$kaunt_4_height+2; }               
                if($kaunt_4_height>=$inline&&($limit_top<$maxprd||$maxprd<=0)) { $kaunt_4_height=0; $templ_prd5=$templ_prd5."<div class='products_line_catshop'></div>"; }
                $templ_prd5=$templ_prd5.$templ_prd4[$v2]; $used[$v2]=$v2; unset($templ_prd4[$v2]);
                if($templ_prd4_type[$v2]=="prd"||$templ_prd4_countimg[$v2]<=1) { $limit_top++; $kaunt_4_height++; } else {
                    $limit_top=$limit_top+2; $kaunt_4_height=$kaunt_4_height+2; }

            if($limit_top>=$maxprd&&$maxprd>0) { break; }}}
            $templ_prd5=$templ_prd5."<div class='products_line_catshop_clear'></div>";
            }}
            return array("templ_prd"=>$templ_prd5,"used"=>@$used);
            }}
            }

}
?>