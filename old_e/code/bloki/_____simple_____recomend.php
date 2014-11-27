    <?php

    $recs = "";
    $templ_prd88['templ_prd'] = "";


    if (!is_object($rating)) {
        $rating = new ratings;
    }

    if ($url != "/product/") {
        $prd_lst = $rating->recomend(@$_SESSION['customers_id']);
    } else {
        //$prd_lst=$rating->recomend(@$_SESSION['customers_id'],"visits",'10',$url_val);
        // TODO: перегруз базы
    }

    if (count($prd_lst['lst']) > 0) {
        $prd_lst2 = implode("' OR " . DB_PREFIX . "products.nnn='", $prd_lst['lst']);

        if (!is_object($showprd)) {
            $showprd = new products();
        }

        $prd_rec = $showprd->collect_products($prd_lst2, 'nnn', '1');

        if (!is_object($cats)) {
            $cats = new categories();
        }
        if (!isset($look)) {
            $look = $cats->gather(SHOP_NNN, 'full');
        } // $look['podrazdel']['nnn']

        $prd_rec = $showprd->sort_products($prd_rec, 'as_array', 'desc', '0', $prd_lst['lst']);
        if ($url != "/product/") {
            $lll = "2";
            $lll2 = "1";
        } else {
            $lll = "5";
            $lll2 = "5";
        }
        $templ_prd88 = $showprd->product_listing($prd_rec, $look, '0', @$templ_prd3['used'], $lll2, $lll);
        $templ_prd3['used'] = $templ_prd88['used'];
    }

    if ($templ_prd88['templ_prd'] != "") {
        if ($url != "/product/") {
            $out['{RECOMENDATIONS}'] = "" . @$templ_prd88['templ_prd'];
        } else {
            $out['{RECOMENDATIONS}'] = "<div class=\"products_list\" style=\"clear:right;margin-top:25px;\"><hr class=\"divider_2\">
                <div class=\"product_listing_name_blok\">" . $prd_lst['zag'] . "</div>
                " . @$templ_prd88['templ_prd'] . "</div>";
        }
    }
    ?>