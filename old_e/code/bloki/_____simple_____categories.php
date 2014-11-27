    <?php

    // in -> url, url_val, info
    $out["{CATEGORIES}"] = "";
    $out["{CATEGORIES_ANCHOR}"] = "<span id=\"catlist\"> </span>";

    $cc = cats_tree();

    if ($url != "/catalog/") {
        $cats = new categories;
        $c = $cats->gather(SHOP_NNN, 'brief');
        $podrazdel_arr = $c['podrazdel'];
    } else {
        $podrazdel_arr = $info['podrazdel'];
        $nadrazdel_arr = $info['nadrazdel'];
    }

    $o = "";
    if (count($nadrazdel_arr) > 0) {
        $nadrazdel_arr = array_reverse($nadrazdel_arr, true);
        foreach ($nadrazdel_arr as $k => $v) {
            if ($v['remote_always'] == "1") {
                $lnk = $v['remote_addr'];
            } else {
                $lnk = MAINURL . "/catalog/" . $k;
            } 
            $o.="<a class='simple_cat_up' href='" . $lnk . "'>" . $v['nazv'] . "</a><p class='commonp'></p>";
        }
    }

    if ($info['nnn'] > 0) { if($url_val!="all") { $currn= txt_cut($info['nazv'], "11", "wrap"); } else { $currn=$info['nazv']; }
        $o.="<div class='simple_cat_current'><div class='simple_cat_current_in'>" . $currn . "<p class='commonp'></p></div></div>";
    }

    if (count($podrazdel_arr) > 0) {
        foreach ($podrazdel_arr as $k => $v) {
            if ($v['remote_always'] == "1") {
                $lnk = $v['remote_addr'];
            } else {
                $lnk = MAINURL . "/catalog/" . $k;
            }
            if (count($cc['parent'][$k]) > 0) {
                $deep = "simple_cat_down_deep";
                $adddeep="<font class='simple_cat_down_deep2'>+".count($cc['parent'][$k])."</font>";
            } else {
                $deep = "simple_cat_down_no";
                $adddeep="";
            }
            if ($url != "/catalog/") { $deep="simple_cat_down_first"; $deep2="simple_cat_down"; $adddeep=""; } else { $deep2="simple_cat_down2"; } 
            $o.="<div class='".$deep2."'><a class='" . $deep . "' href='" . $lnk . "'>" . $v['nazv'] . "</a>".$adddeep."<p class='commonp'></p></div>";
        }
    }

    $out["{CATEGORIES}"] = "<div id=\"catcontent\">" . $o . "</div>";

    if ($url_val == "all") {
        $out['{PAGE_ALL}'] = "<div id=\"catcontent\" style=\"margin-left:35px;margin-top:35px;font-size:1em;\">" . $o . "</div>";
        $out['{PAGE_ALL_ANCHOR}'] = "<span id=\"catlist\"> </span>";
        $out["{CATEGORIES}"]="";
        $out["{CATEGORIES_ANCHOR}"]="";
    }
    ?>