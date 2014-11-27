    <?php

    $templ_prd99['templ_prd'] = "";
    $manuf_k = @$info['4bloks']['prd']['attr_descr']['производитель'][@array_rand(@$info['4bloks']['prd']['attr_descr']['производитель'])];
    
    if ($manuf_k != "") {

        if (!is_object($rating)) {
            $rating = new ratings;
        }

        $prd_lst = $rating->random("manuf", 10, $manuf_k);

        if (count($prd_lst['prds']) > 0) {
            $prd_lst2 = implode("' OR " . DB_PREFIX . "products.nnn='", $prd_lst['prds']);

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

            $prd_rec = $showprd->sort_products($prd_rec, 'as_array', 'desc', '0', $prd_lst['prds']);
            $templ_prd99 = $showprd->product_listing($prd_rec, $look, '0', @$templ_prd3['used'], '5', '5');
            $templ_prd3['used'] = $templ_prd99['used'];
        }

        if ($templ_prd99['templ_prd'] != "") {
            $out['{RECOMENDATIONS_MANUF}'] = "<div class=\"products_list\" style=\"clear:right;margin-top:25px;\"><hr class=\"divider_2\">
            <div class=\"product_listing_name_blok\">" . $prd_lst['zag'] . "</div>
            " . @$templ_prd99['templ_prd'] . "</div>";
        }
    }
    ?>