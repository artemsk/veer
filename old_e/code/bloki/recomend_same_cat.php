    <?php

    $cat_rand = @array_rand(@$info['4bloks']['recomend_same_cat']);
    $templ_prd99['templ_prd'] = "";

    if (@$cat_rand > 0) {

        // products
        if (!is_object($showprd)) {
            $showprd = new products;
        }

        if (@$flag_skip_collect != "1") {
            $prds = $showprd->collect_products($cat_rand, 'cat');
        }

        if (count($prds) > 0) {

            if (!is_object($cats)) {
                $cats = new categories();
            }
            if (!isset($look)) {
                $look = $cats->gather(SHOP_NNN, 'full');
            } // $look['podrazdel']['nnn']

            $prds = $showprd->sort_products($prds, "shopcat_limit", "asc", "20");
            $templ_prd99 = $showprd->product_listing($prds, $look, '0', @$templ_prd3['used'], '5', '5');
            $templ_prd3['used'] = $templ_prd99['used'];
        }

        if ($templ_prd99['templ_prd'] != "") {
            $out['{RECOMENDATIONS_SAMECAT}'] = "<div class=\"products_list\" style=\"clear:right;margin-top:25px;\"><hr class=\"divider_2\">
            <div class=\"product_listing_name_blok\">" . RECOMEND_NAME_SAME_CAT . "</div>
            " . @$templ_prd99['templ_prd'] . "</div>";
        }
    }
    ?>