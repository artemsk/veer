    <?php

    if (!is_array($prd)) { // не передали из bigpic
        $showprd = new products();
        $prd = $showprd->collect_products(SHOP_NNN, 'main');
        unset($prd[@$check]);
    }

    if (!is_array($cats)) {
        $cats = new categories();
    }
    $look = $cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']
    $templ_prd3 = $showprd->product_listing($prd, $look, GROUP_MAIN_FLAG, array(@$_SESSION['check_used'] => @$_SESSION['check_used']), 100, LIMIT_TOP_PRODUCTS, "0", "0", "0", "1", "1");

    $v6 = "<table><tr><td valign=top>";
    $v3 = ceil(count($templ_prd3['templ_prd']) / 2);
    $v4 = 0;
    $v5 = 1;
    $v7 = "";
    foreach ($templ_prd3['templ_prd'] as $k => $v) {
        $v4++;
        if ($v4 > $v3) {
            $v5++;
            if ($v5 > 2) {

            } else {
                $v6.="</td><td valign=top>";
            } $v4 = 0;
        }
        $v2 = strtr(@$v['template_type_remember'], array(
            "{IMG}" => $v['img'],
            "{NAME_TINY}" => txt_cut($v['nazv_tiny'], 13, "wrap"),
            "{NAME}" => $v['nazv'],
            "{BASKET_FORM}" => $v['full_basket_form'],
            "{NAME_LINK}" => $v['nazv_link'],
            "{PRICE}" => $v['all_prices']['price_formated'],
            "{IMG_PATH}" => MAINURL . "/template/" . TEMPLATE . "/images",
            "{GROUP_PRD_NAME}" => GROUP_PRD_NAME,
            "{STAR_PRD_NAME}" => STAR_PRD_NAME,
            "{INLINE}" => "1",
        ));
        $v6.=$v2 . "<div style='clear:both;'></div>";
    }

    $v6.="</tr></table>";

    $templ_prd3['templ_prd'] = $v6;

    $templ_prd3['used'][@$_SESSION['check_used']] = @$_SESSION['check_used'];
    $_SESSION['check_used'] = "";

    $out['{CHOSEN_PRODUCTS}'] = @$templ_prd3['templ_prd'];

    //$templ_prd88=$showprd->product_listing($prd,$look,GROUP_MAIN_FLAG,$templ_prd3['used'],"4","0");
    //$out['{LEFTOUTS}']=@$templ_prd88['templ_prd'];
    //0 - обычная
    //1 - скидочная
    //2 - гостя
    //3 - дистрибьютора
    //4 - оптовика
    //5 - купон, индивидуальная
    //6 - скидка на заказ
    //
    //
    // TODO: проверить работоспособность по статусам: магазины
    //5 - здесь же, remote_addr работает
    //4 - отдельная корзина, здесь же, оформление заказа с бд
    //3 - бд есть, но оформление заказа только файл и почта
    //2 - бд есть, список товаров но при добавлении в корзину перенаправление!
    //1 - бд нет, просто список товаров и ссылки
    ?>