<?php
$info['4bloks']['page_type']=$detected[1];

$showall=new pages;
$pages_go=$showall->show_pages(SHOP_NNN, "news"); // ALL NEWS

if($detected[1]=="news") { $title_head="новости";
        if(count(@$pages_go[1])>0) {
        $news_arr=$showall->prepare_news($pages_go[1], 999, 0,array("news_dat_page","news_pic_page","news_txt_page","news_page_all","news_imp"));
        $info['4bloks']['news_arr']=$news_arr;
        }
        $pages_go=$showall->show_pages(SHOP_NNN,"last",100); $info['4bloks']['pages_go_right']=$pages_go[0];

} else { $title_head="статьи";
        if(count(@$pages_go[1])>0) {
        $news_arr=$showall->prepare_news($pages_go[1], 3, 0,array("news_dat_page","news_pic_page","news_txt_page","news_page_all","news_imp"));
        $info['4bloks']['news_arr']=$news_arr;
        }
    $pages_go=$showall->show_pages(SHOP_NNN,"list"); $pages_go=$pages_go[0];
    if(is_array($pages_go)) { $info['4bloks']['pages_go']=$pages_go; }
}
