<?php

class elements {

    // динамические элементы: т.е. неизвестно будут они на странице или в шаблоне или нет,
    // зависит от наличия в папке опр файла, константы или страница в бд, места в шаблоне!

    function look_elements($url = "", $url_val = "") { // опр страницу и соотв. блоки
        Debug::log();
        if ($url != "/code/ext/" && $url != "/adm/") {
            $elements = PAGE_ELEMENTS;
        } // TODO: с осторожностью. под наблюдением
        if ($url == "") {
            $elements = $elements . "," . PAGE_ELEMENTS_MAIN;
        }
        if ($url == "/catalog/") {
            $elements.="," . PAGE_ELEMENTS_CATS;
        }
        if ($url == "/keyword/") {
            $elements.="," . PAGE_ELEMENTS_KEYWS;
        }
        if ($url == "/attr/") {
            $elements.="," . PAGE_ELEMENTS_ATTRS;
        }
        if ($url == "/search/") {
            $elements.="," . PAGE_ELEMENTS_SRCH;
        }
        if ($url == "/pageall/") {
            $elements.="," . PAGE_ELEMENTS_PAGES;
        }
        if ($url == "/page/") {
            $elements.="," . PAGE_ELEMENTS_PAGE1;
        }
        if ($url == "/product/") {
            $elements.="," . PAGE_ELEMENTS_PRODUCT;
        }
        if ($url == "/user/" && $url_val == "contact") {
            $elements.="," . PAGE_ELEMENTS_USER_CONTACT;
        }
        if ($url == "/user/" && $url_val == "compare") {
            $elements.="," . PAGE_ELEMENTS_USER_COMPARE;
        }
        if ($url == "/user/") {
            $elements.="," . PAGE_ELEMENTS_USER;
        }

        function kill_some_elements(&$item, &$key) {
            if (substr($item, 0, 1) == "-") {
                
            } else {
                unset($item);
            }
        }

        $elements_arr = explode(",", $elements);
        //foreach($elements_arr as $k=>$v) { $v=trim($v); if(substr($v,0,1)=="-") { $elements_kill[substr($v,1)]=$v; unset($elements_arr[$k]); }} // TODO: включить? или нет?
        foreach ($elements_arr as $k => $v) {
            $v = trim($v); //if(isset($elements_kill[$v])) { continue; }
            if ($v == "") {
                continue;
            }
            if (substr($v, 0, 1) == "[" && substr($v, -1) == "]") {
                $main['constants'][$k] = $v;
                continue;
            } // constants
            if (substr($v, 0, 1) == "#") {
                $v2 = explode("_", substr($v, 1));
                $main['pages'][$v2[0]] = $v2[1];
                continue;
            } // pages
            if (substr($v, 0, 1) == "!") {
                $main['funcs'][$k] = $v;
                continue;
            } // functions elements->
            $main['bloks'][$k] = $v;
        }
        return $main;
    }

    function making($url = "", $url_val = "", $info = "") { // url, url_val - о странице, info - заранее подготовленные данные, если есть | создаем массив
        Debug::log();
        $main = $this->look_elements($url, $url_val);
        $out_2 = array();
        if (count($main) > 0) {
            foreach ($main as $k => $v) {
                $k2 = "parent_" . $k;
                $out = $this->$k2($url, $url_val, $info, $v);
                $out_2 = array_merge($out_2, (array) @$out);
            }
        }
        if (is_array($info) && count(@$info['4out']) > 0) { // уже есть подготовленный массив для шаблона
            foreach ($info['4out'] as $k => $v) {
                $out_2[$k] = $v;
            }
        }
        return $out_2;
    }

    function parent_constants($url = "", $url_val = "", $info = "", $v = array()) {
        Debug::log();
        foreach ($v as $kk => $vv) {
            $k2 = strtoupper(substr($vv, 1, -1));
            $out["{" . $k2 . "}"] = constant($k2);
        }
        return $out;
    }

    function parent_pages($url = "", $url_val = "", $info = "", $v = "") {
        Debug::log();
        $mainpage = new pages;
        $vv2 = strtr(implode("', '", $v), array('#' => ''));
        $mainpage2 = $mainpage->show_page($vv2);
        foreach ($v as $kk => $vv) {
            $out["{PAGE_" . $kk . "}"] = $mainpage2[$vv]['txt'];
        }
        return $out;
    }

    function parent_funcs($url = "", $url_val = "", $info = "", $v = "") {
        Debug::log();
        foreach ($v as $kk => $vv) {
            $k2 = substr($vv, 1);
            $k3 = strtoupper($k2);
            $out["{" . $k3 . "}"] = call_user_func($k2, $url);
        }
        return $out;
    }

    function parent_bloks($url = "", $url_val = "", $info = "", $zzzz = "") {
        Debug::log();
        $out = array();
        if ($url_val == "") {
            $url_val = SHOP_NNN;
        }
        foreach ($zzzz as $kk => $vv) {
            $fn = MAINURL_5 . "/code/bloki/" . $vv . ".php";
            if (file_exists($fn)) {
                require(MAINURL_5 . "/code/bloki/" . $vv . ".php");
            }
        }
        return($out);
    }

    // contacts,manufacturers,banners,search
}

?>