<?php
if($info['4bloks']['page_type']=="news") { $out['{PAGE_NAZV}']="<a href=".MAINURL."/page/all>".LAST_ARTICLES_TXT_1."</a> <font style='font-size:0.6em;'>/ Новости</font>"; } else {
    $out['{PAGE_NAZV}']=LAST_ARTICLES_TXT_1." <font style='font-size:0.6em;'>/ <a href=".MAINURL."/page/news>Новости</a></font>"; }

if($info['4bloks']['page_type']=="news") {
    if(@$info['4bloks']['news_arr']['all']!="") { 
    $out['{CAT_NEWS_ALL}']="<div id=\"pagenews_full\" class=\"page_news_full\">".$info['4bloks']['news_arr']['all']."</div>";
    }

    if(count($info['4bloks']['pages_go_right'])>0) { $n5="";
    foreach($info['4bloks']['pages_go_right'] as $n1=>$n2) { // список статей
     $n5=$n5."<a href=".MAINURL."/page/".$n1.">".$info['4bloks']['pages_go_right'][$n1]['nazv']."</a><p></p>"; }
     $out['{CAT_NEWS_ALL_RIGHT}']="<div class=\"page_news_full_2\">".$n5."</div>";

     }

    }

if(count($info['4bloks']['pages_go'])>0&&$info['4bloks']['page_type']!="news") {

            $pag_filename=MAINURL_5."/template/".TEMPLATE."/pages_list.php";
            $templ_pag=get_include_contents($pag_filename); // ###шаблон_ввод
            $templ_pag_types=explode("[####]",$templ_pag);
            
    if(!is_array($cats)) { $cats=new categories(); }
    if(!isset($look)) { $look=$cats->gather(SHOP_NNN, 'full'); } // $look['podrazdel']['nnn']

    $str="";

    foreach($info['4bloks']['pages_go'] as $p1=>$p2) { // shop_cat, dat, nazv, pic, show_in_news
        if(date("Y",$p2['dat'])!=date("Y",time())) { $p_dat=date("d.m.Y",$p2['dat']); } else { $p_dat=date("d.m.",$p2['dat']); }
        if($p2['shop_cat']==SHOP_NNN) { $p_cat=""; } else { 
            if($look['podrazdel'][$p2['shop_cat']]['remote_addr']!=""&&$look['podrazdel'][$p2['shop_cat']]['remote_always']=="1") { $p_cat_lnk=$look['podrazdel'][$p2['shop_cat']]['remote_addr']; } else { $p_cat_lnk=MAINURL."/catalog/".$p2['shop_cat']; }
            $p_cat="<div class='article_cat'><a href=".$p_cat_lnk.">".$look['podrazdel'][$p2['shop_cat']]['nazv']."</a></div>";
            }
        if($p2['show_in_news']=="1") { $p_n="<span class='article_news_flag'>[новость]</span>"; } else { $p_n=""; }
        if($p2['show_nazv']!="1") { $p2['nazv']=""; $p_dat="<a href=".MAINURL."/page/".$p1.">".$p_dat."</a>"; } else {
            $p2['nazv']="<a href=".MAINURL."/page/".$p1.">".$p2['nazv']."</a>"; }
        if($p2['pic']!="") { $p_pic="<img class='article_pics' src=".imgprocess(MAINURL."/upload/pages/".$p2['pic'], 0, 50, "1", "upload/pages/thumb/").">"; } else { $p_pic=""; }
        if($p2['num_comms']>0) { $p_comm="<span class='comments_number' style='font-size:0.8em;'>".COMMENTS_TXT." ".$p2['num_comms']."</span>"; } else { $p_comm=""; }

                $templ_pag2 = strtr(@$templ_pag_types[1], array(
                        "{P_PIC}" => @$p_pic,
                        "{P_DAT}"   => @$p_dat,
                        "{P_CAT}" => @$p_cat,
                        "{P_N}" => @$p_n,
                        "{P_NAZV}" => @$p2['nazv'],
                        "{P_COMM}" => @$p_comm,
                ));

        $str.=$templ_pag2;
        }
     if($str!="") { $out['{PAGE_ALL}']=$templ_pag_types[0].$str.$templ_pag_types[2]; }
        if(@$info['4bloks']['news_arr']['all']!="") { 
        $out['{CAT_NEWS_ALL_RIGHT}']="<div class=\"page_news_full_2\">".$info['4bloks']['news_arr']['all']."</div>";
        }

    }

?>