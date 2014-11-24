<?
$id=1;
$rss2['rssurl']="http://news.google.com/news?svnum=10&as_scoring=n&hl=ru&ned=&ie=UTF-8&as_drrb=q&as_qdr=&as_mind=28&as_minm=4&as_maxd=28&as_maxm=5&q=allintitle:++%D0%B9%D0%BE%D0%B3%D0%B0+OR+%D0%B9%D0%BE%D0%B3%D0%B8+OR+%D0%B9%D0%BE%D0%B3%D0%B5+OR+%D0%B9%D0%BE%D0%B3%D0%BE%D0%B9+OR+%D0%B9%D0%BE%D0%B3&output=rss&ned=:ePkh8BM9E-LUYk_Myywuyc8T4tPiSc4vLSrJS61USM6vEOLV4s7JLE5UyC5NKcovFxLU4k9JLMtMUShOzijPzM1NLRLi1-LNTSwpyUgtVyhILSqqhAko5KQqJOUk5iXDnGYkIOHz0Vun73XWz8mdteITLl8GAAaAKRw";

$gn_str=readfromfile(SHOP_NNN."_google_news");
if($gn_str!="") { $out_str=$gn_str; } else {

$out_str="";
$f=file($rss2['rssurl']);
$f1="";
$out_str.="<div class=\"googlenews_name\">…ога в мире</div>";

foreach($f as $k=>$v) {
$v=mb_convert_encoding($v,"Windows-1251","Utf-8");
$f1=$f1.$v."\r\n";
}
$f1=htmlspecialchars($f1);
$findbook1="&lt;channel&gt;";
$findbook2="&lt;/channel&gt;";
$find2=explode($findbook2,$f1);
$f2=explode($findbook1,$find2[0]);
//$find2=eregi("$findbook1(.*)$findbook2",$f1,$f2);

$f3=explode("&lt;item&gt;",$f2[1]);

foreach($f3 as $k=>$v) {
if($k>=15) { break; }
if($k=="0") {
continue;
} else {
// всЄ остальное

$f8=explode("&lt;pubDate&gt;",$v);
$f9=explode("&lt;/pubDate&gt;",$f8[1]);
$rss_item_pubdate[$id]=$f9[0];

$f8=explode("&lt;title&gt;",$v);
$f9=explode("&lt;/title&gt;",$f8[1]);
$rss_item_titlepost[$id]=$f9[0];

$f8=explode("&lt;link&gt;",$v);
$f9=explode("&lt;/link&gt;",$f8[1]);
$rss_item_linkpost[$id]=$f9[0];

$f8=explode("&lt;description&gt;",$v);
$f9=explode("&lt;/description&gt;",$f8[1]);
$rss_item_descr[$id]=$f9[0];
}

$postdate=time();
$postdate_d=substr(@$rss_item_pubdate[$id],5,2);
$postdate_m=substr(@$rss_item_pubdate[$id],8,3);
$postdate_y=substr(@$rss_item_pubdate[$id],12,4);
$postdate_h=substr(@$rss_item_pubdate[$id],17,2);
$postdate_min=substr(@$rss_item_pubdate[$id],20,2);
$postdate_sec=substr(@$rss_item_pubdate[$id],23,2);
$postdate_month=array("Jan"=>"01","Feb"=>"02","Mar"=>"03","Apr"=>"04","May"=>"05","Jun"=>"06","Jul"=>"07","Aug"=>"08","Sep"=>"09","Oct"=>"10","Nov"=>"11","Dec"=>"12");
$postdate=mktime($postdate_h,$postdate_min,$postdate_sec,@$postdate_month[@$postdate_m],$postdate_d,$postdate_y);
if($postdate<0) { $postdate=time(); }

if(trim(@$rss_item_titlepost[$id])=="") { continue; }
$out_str.="<b>".date("d.m",$postdate)."</b> ";
$out_str.="<a href=".@$rss_item_linkpost[@$id].">".$rss_item_titlepost[$id]."</a>";
$out_str.="<p style='margin:10 0 0 0; padding:0 0 0 0;'></font>";

$id=$id+1;
}

write2file($out_str,SHOP_NNN."_google_news");
}


$out['{GOOGLE_NEWS}']=@$out_str;
?>