<?php
header("HTTP/1.1 200 Ok");
header("Status: 200 OK");
header("Last-modified:".gmdate("D, d M Y H:i:s")."GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('ENGINEVER','0.89'); 
define('TECH_EMAIL','garcia82@yandex.ru');

if($_SERVER["HTTP_HOST"]=="test1.ru") { define('MAINURL_LOCALHOST','1'); } else { define('MAINURL_LOCALHOST','0'); } // 1 - на локальном сервере

if(MAINURL_LOCALHOST=="0") { // для remote server
define('DB_LOG','u59221');
define('DB_PSSW','crochanicic2');
define('DB_SERV','u59221.mysql.masterhost.ru');
define('DB_NAME','u59221_4');
define('DB_PREFIX','');
define('MAINURL_2','');
define('MAINURL_5','/home/u59221/ganeshastores.ru/www/'); // прямой путь
//
} ELSE {
define('DB_LOG','root');
define('DB_PSSW','');
define('DB_SERV','localhost');
define('DB_NAME','newshop');
define('DB_PREFIX','');
define('MAINURL_2','/shop-new'); // TODO: придумать что-то
define('MAINURL_5','z:/home/test1.ru/www/shop-new'); // прямой путь
//
}

if(substr($_SERVER["HTTP_HOST"],0,4)=="www.") {
define('MAINURL','http://'.substr($_SERVER["HTTP_HOST"],4).MAINURL_2.'');
    define('MAINURL_4','.'.substr($_SERVER["HTTP_HOST"],4));
} else {
define('MAINURL','http://'.$_SERVER["HTTP_HOST"].MAINURL_2.'');
    define('MAINURL_4','.'.$_SERVER["HTTP_HOST"]);
} // TODO: rewriterule

$shopnew = @mysql_pconnect(DB_SERV,DB_LOG,DB_PSSW);

    // отобр. статической версии сайта из бэкапа, если произошел сбой БД
    if(@$shopnew=="") { if(substr($_SERVER['REQUEST_URI'],-1)=="/") { $look2cache2=substr($_SERVER['REQUEST_URI'],0,-1); } else { $look2cache2=$_SERVER['REQUEST_URI']; }
    $look2cache=strtr("http://".$_SERVER["HTTP_HOST"].$look2cache2,array("/"=>"_","http://"=>"http_","."=>"_")); 
    if(file_exists(MAINURL_5."/code/txts/total/".$look2cache.".html")) {
        require_once(MAINURL_5."/template/zaglushka.html"); 
        require_once(MAINURL_5."/code/txts/total/".$look2cache.".html");
    } $f=fopen(MAINURL_5."/code/txts/notify/db_fail_".date("d_m_Y_H_i",time()).".txt","w"); fwrite($f,""); fclose($f); exit; }
    //////////////////////////////////////////

mysql_select_db(DB_NAME,$shopnew);
$shopnew2=mysql_query("SELECT nnn, nazv, currency FROM ".DB_PREFIX."catshop_config WHERE type='shop' AND remote_addr='".MAINURL."'") or die(mysql_error());
if(mysql_num_rows($shopnew2)<=0) { 
    $redirect_check=mysql_query("SELECT targ FROM ".DB_PREFIX."catshop_redirect WHERE src='".MAINURL."'");
    if(mysql_num_rows($redirect_check)>0) { header("Location:".mysql_result($redirect_check,0,'targ')."".@$_SERVER['REQUEST_URI']); } else {
    $r="Error: Unable to find shop"; }
    echo $r; exit; 
    }

$shopnew3=mysql_fetch_assoc($shopnew2);

define('SHOP_NNN',$shopnew3['nnn']);
define('SHOP_NAME',$shopnew3['nazv']);
define('SHOP_CURRENCY',$shopnew3['currency']);

$shopnew4=mysql_query("SELECT conf_key, conf_val FROM ".DB_PREFIX."configuration WHERE shop_cat='".SHOP_NNN."'");
if(mysql_num_rows($shopnew4)>0) {} else { 
    $shopnew4=mysql_query("SELECT conf_key, conf_val FROM ".DB_PREFIX."configuration WHERE shop_cat='289'"); } // default: у remote другой
$shopnew5=mysql_fetch_assoc($shopnew4);
do { define($shopnew5['conf_key'],$shopnew5['conf_val']); } while($shopnew5=mysql_fetch_assoc($shopnew4));


// session
ini_set('session.save_path', MAINURL_5 .'/temp/');
ini_set('session.gc_maxlifetime', 7200);
//ini_set('session.cookie_lifetime', 7200);
session_set_cookie_params(0);
ini_set("max_execution_time", "120");
//error_reporting(E_ALL);
session_start(); 

// classes & functions
require_once(MAINURL_5."/code/modules/global.php");
require_once(MAINURL_5."/code/modules/elements.php");
require_once(MAINURL_5."/code/modules/forms.php");
require_once(MAINURL_5."/code/modules/mail.php"); // TODO: переписать
require_once(MAINURL_5."/code/modules/pages.php");
require_once(MAINURL_5."/code/modules/customers.php");
require_once(MAINURL_5."/code/modules/cats.php");
require_once(MAINURL_5."/code/modules/products.php");
require_once(MAINURL_5."/code/modules/order.php");
//require_once(MAINURL_5."/code/modules/adm.php");

cats_tree();

ob_start("ob_gzhandler"); // TODO: проверить
?>