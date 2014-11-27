    <?php

    if (!is_object($rating)) {
        $rating = new ratings;
    }
    $prd_lst = $rating->recomend_cats(@$_SESSION['customers_id']);

    if (!is_object($cats)) {
        $cats = new categories();
    }
    if (!isset($look)) {
        $look = $cats->gather(SHOP_NNN, 'full');
    } // $look['podrazdel']['nnn']
    $str = "";
    if (count($prd_lst['lst']) > 0) {

        foreach ($prd_lst['lst'] as $z) {
            if ($look['podrazdel'][$z]['remote_addr'] != "" && $look['podrazdel'][$z]['remote_always'] == "1") {

            } else {
                $look['podrazdel'][$z]['remote_addr'] = MAINURL . "/catalog/" . $z;
            }
            $str.="<a href=" . $look['podrazdel'][$z]['remote_addr'] . ">" . $look['podrazdel'][$z]['nazv'] . "</a><p></p>";
        }
    }

    $out['{POPULAR_CATS}'] = "<div class='popcat_title'>" . $prd_lst['zag'] . "</div>" . @$str;
    
    ?>