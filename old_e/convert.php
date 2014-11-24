<?php
/*
define('DB_LOG','u390764');
define('DB_PSSW','5c-2vIAT_d');
define('DB_SERV','u390764.mysql.masterhost.ru');
define('DB_NAME','u390764');
define('DB_PREFIX','');
//define('MAINURL_2','');
define('MAINURL_5','/home/u390764/koalalab.ru/www/'); // прямой путь

*/
define('DB_LOG','root');
define('DB_PSSW','');
define('DB_SERV','localhost');
define('DB_NAME','newshop');
define('DB_PREFIX','');
//define('MAINURL_2','/shop-new'); 
define('MAINURL_5','z:/home/test1.ru/www/shop-new'); // прямой путь

$shopnew = @mysql_pconnect(DB_SERV,DB_LOG,DB_PSSW);
mysql_select_db(DB_NAME,$shopnew);

$i = mysql_query("SELECT * FROM yk_posts ORDER BY dat ASC") or die(mysql_error());

$str2=""; $xxx = 0; $filenumber=1;

while($i2=  mysql_fetch_assoc($i)) {
    
    if($i2['type'] == "аудио") {
        continue; }
    
    $i3 = mysql_query("SELECT * FROM yk_txt WHERE number = '".$i2['number']."'");
    $i4 = mysql_fetch_assoc($i3);
    
    if(mysql_num_rows($i3)<=0) { continue; }
    
    if(isset($theme_cache[$i2['theme']])) { $catid = $theme_cache[$i2['theme']]; } else {
        $t = mysql_query("SELECT nnn FROM catshop_config WHERE nazv = '".$i2['theme']."' AND type = 'cat' ");
        if(mysql_num_rows($t)>0) {
        $catid = mysql_result($t,0,'nnn'); 
        } else {
            mysql_query("INSERT INTO catshop_config (nazv, type, parent, hostshop) VALUES ('".$i2['theme']."','cat','2','2')");
            $catid = mysql_insert_id();
        }
        $theme_cache[$i2['theme']] = $catid;
    }
    
    $str = "INSERT DELAYED INTO pages (dat, shop_cat, nazv, txt, comm_allow, show_nazv, show_in_list, show_in_last)
        VALUES ('".$i2['dat']."','".$catid."','".$i2['nazv']." (".$i2['type'].")',
            '".strtr(@$i4['smalltekct'],array("'"=>" "))."<br/><br/>".strtr(@$i4['fulltekct'],array("'"=>" "))."','0','1','1','1'); \r\n";

    $str2.=$str;
    
    $xxx++;
    
    if($xxx>=1000) {
        
       $f=fopen("sqls_".$filenumber.".sql","w");
       fwrite($f,$str2);
       fclose($f);
       $xxx=0; $filenumber++; $str2="";
    }
    
    // nnn dat shop_cat nazv txt pic comm_allow show_nazv show_in_list show_in_last
    
    // type, dat, theme, author, nazv, keyword, email, flagrss, kwords, hidden, rsshid, onmain, views, comment_ok, smalltekct_onpost,
    // once_reedit, extended_theme, mainfoto, mainfoto_flag, library, library_author
    
        
}

$filenumber++;
$f=fopen("sqls_".$filenumber,"w");
 fwrite($f,$str2);
 fclose($f);
