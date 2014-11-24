<?
if(@$flag_no_repeat_lastnews!="1") { 
//$out["{NEWS_IMPORTANT}"]="";
//$out["{NEWS}"]="";
//$out["{LAST_ARTICLES}"]="";
    if($url!="/catalog/") { $url_temp=$url; $url=""; $url_val_tmp_2=$url_val; $url_val=""; }
    
    if($url=="") {
        
    $newslast=readfromfile(SHOP_NNN."_pages_last_news","0.5"); 

    if($newslast!="") {
    $newslast2=unserialize($newslast);
    foreach($newslast2 as $k=>$v) { $out[$k]=$v; }
    $flag_got_news_from_file=1;
    }
    } // новости для всех кроме главной

    if($url_val==""||$url_val=="all"||$url_val=="new"||$url_val=="ratings"||$url_val=="comments") { $url_val_tmp=SHOP_NNN; } 
        else { $url_val_tmp=$url_val; }
        
    $newslast=readfromfile(SHOP_NNN."_pages_last_articles_".$url_val_tmp."","0.5"); 

    if($newslast!="") {
    $newslast2=unserialize($newslast);
    foreach($newslast2 as $k=>$v) { $out[$k]=$v; }
    $flag_got_last_articles_from_file=1;
    }

  if(@$flag_got_last_articles_from_file=="1"&&@$flag_got_news_from_file=="1") {} else {

        if(!is_object($ppp)) { $ppp=new pages; }

        if($url=="") {
           $news_list=$ppp->show_pages(SHOP_NNN, "main"); } else {
              if(@$flag_got_last_articles_from_file!="1") { 
                  if($url_val==""||$url_val=="all"||$url_val=="new"||$url_val=="ratings"||$url_val=="comments") { 
                  $url_val_tmp=SHOP_NNN; } else { $url_val_tmp=$url_val; }
           $news_list=$ppp->show_pages($url_val_tmp, "last",CATPAGE_ARTICLES_LIMIT); } // для неглавной страницы только последние статьи
        }

        $n4=0;

        if($url==""&&@$flag_got_news_from_file!="1") { $news_arr=$ppp->prepare_news($news_list[1]); } // только на главной

        if(count($news_list[0])>0&&@$flag_got_last_articles_from_file!="1") { $n5="";
         foreach($news_list[0] as $n1=>$n2) { // список статей
             $n5=$n5."<a href=".MAINURL."/page/".$n1.">".$news_list[0][$n1]['nazv']."</a><p></p>"; }
             
             $n5=LAST_ARTICLES_TXT_1."<p></p>".$n5.
                     "<span class=\"last_articles_one\">&rarr; <a href=".MAINURL."/page/all>".LAST_ARTICLES_TXT_2."</a></span>";
        }

        // out -> $news_arr;
        if($url=="") {
           if(@$flag_got_news_from_file!="1") { 
               
               $out["{NEWS_IMPORTANT}"]="<div style=\"margin-top:35px;margin-bottom:0px;\">".@$news_arr['imp']."</div>";
               $out["{NEWS}"]=@$news_arr['all'];
               
           write2file(serialize(array("{NEWS_IMPORTANT}"=>$out["{NEWS_IMPORTANT}"],"{NEWS}"=>$out["{NEWS}"])),SHOP_NNN."_pages_last_news");  
           
           }
        }

        if(@$flag_got_last_articles_from_file!="1") { 
            
               $out["{LAST_ARTICLES}"]=@$n5;
                
                if($url_val==""||$url_val=="all"||$url_val=="new"||$url_val=="ratings"||$url_val=="comments") { 
                    $url_val_tmp=SHOP_NNN; } else { $url_val_tmp=$url_val; }
                                                      
         write2file(serialize(array("{LAST_ARTICLES}"=>$out["{LAST_ARTICLES}"])),SHOP_NNN."_pages_last_articles_".$url_val_tmp);  
         
        }
        
        $flag_no_repeat_lastnews="1";

    }

    if(isset($url_temp)) { $url=$url_temp; $url_val=$url_val_tmp_2; }
}

?>