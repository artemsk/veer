<?php
header("HTTP/1.1 200 Ok");
header("Status: 200 OK");
header("Last-modified:".gmdate("D, d M Y H:i:s")."GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('ENGINEVER','0.89');
define('TECH_EMAIL','garcia82@yandex.ru');
define('DEBUG_MODE','1');

if($_SERVER["HTTP_HOST"]=="test1.ru") { define('MAINURL_LOCALHOST','1'); } else { define('MAINURL_LOCALHOST','0'); } // 1 - �� ��������� �������

if(MAINURL_LOCALHOST=="0") { // ��� remote server
define('DB_LOG','u390764');
define('DB_PSSW','5c-2vIAT_d');
define('DB_SERV','u390764.mysql.masterhost.ru');
define('DB_NAME','u390764');
define('DB_PREFIX','');
//define('MAINURL_2','');
define('MAINURL_5','/home/u390764/koalalab.ru/www/'); // ������ ����
//
} ELSE {
define('DB_LOG','root');
define('DB_PSSW','');
define('DB_SERV','localhost');
define('DB_NAME','newshop');
define('DB_PREFIX','');
//define('MAINURL_2','/shop-new'); 
define('MAINURL_5','z:/home/test1.ru/www/shop-new'); // ������ ����
}

if(substr($_SERVER["HTTP_HOST"],0,4)=="www.") { $host=substr($_SERVER["HTTP_HOST"],4); } else { $host=$_SERVER["HTTP_HOST"]; }

$shopnew = @mysql_pconnect(DB_SERV,DB_LOG,DB_PSSW);

    // �����. ����������� ������ ����� �� ������, ���� ��������� ���� ��
    if(@$shopnew=="") { if(substr($_SERVER['REQUEST_URI'],-1)=="/") { $look2cache2=substr($_SERVER['REQUEST_URI'],0,-1); } else { $look2cache2=$_SERVER['REQUEST_URI']; }
    $look2cache=strtr("http://".$_SERVER["HTTP_HOST"].$look2cache2,array("/"=>"_","http://"=>"http_","."=>"_")); 
    if(file_exists(MAINURL_5."/code/txts/total/".$look2cache.".html")) {
        require_once(MAINURL_5."/template/zaglushka.html"); 
        require_once(MAINURL_5."/code/txts/total/".$look2cache.".html");
    } $f=fopen(MAINURL_5."/code/txts/notify/db_fail_".date("d_m_Y_H_i",time()).".txt","w"); fwrite($f,""); fclose($f); exit; }
    //////////////////////////////////////////
    
mysql_select_db(DB_NAME,$shopnew);
       
$shopnew2=mysql_query("SELECT nnn, nazv, currency, remote_addr FROM ".DB_PREFIX."catshop_config WHERE type='shop'") or die(mysql_error());
while($shopnew3=mysql_fetch_assoc($shopnew2)) {
    if(strpos("http://".$host.$_SERVER['REQUEST_URI'],$shopnew3['remote_addr'])!==false) {
       define('MAINURL', $shopnew3['remote_addr']);
       define('SHOP_NNN',$shopnew3['nnn']);
       define('SHOP_NAME',$shopnew3['nazv']);
       define('SHOP_CURRENCY',$shopnew3['currency']);
       define('MAINURL_2',strtr($shopnew3['remote_addr'],array("http://".$host=>"")));
       $shop_not_found=0;
       break;
    } else { $shop_not_found=1; }  
}

define('MAINURL_4','.'.$host);

if(@$shop_not_found=="1") { 
    $redirect_check=mysql_query("SELECT targ FROM ".DB_PREFIX."catshop_redirect WHERE src LIKE '%%%http://".$host."%%%'");
    $targ=@mysql_result($redirect_check,0,'targ');      
    if(mysql_num_rows($redirect_check)>0) { 
        define('MAINURL', $targ);
        header("Location:".mysql_result($redirect_check,0,'targ')."".@$_SERVER['REQUEST_URI']); exit; } else {
    echo "Error: Unable to find shop"; exit; }
    }

$shopnew4=mysql_query("SELECT conf_key, conf_val FROM ".DB_PREFIX."configuration WHERE shop_cat='".SHOP_NNN."'");
if(mysql_num_rows($shopnew4)>0) {} else { 
    $shopnew4=mysql_query("SELECT conf_key, conf_val FROM ".DB_PREFIX."configuration WHERE shop_cat='289'"); } // default: � remote ������
$shopnew5=mysql_fetch_assoc($shopnew4);
do { define($shopnew5['conf_key'],$shopnew5['conf_val']); } while($shopnew5=mysql_fetch_assoc($shopnew4));


// session
ini_set('session.save_path', MAINURL_5 .'/temp/');
ini_set('session.gc_maxlifetime', 7200);
//ini_set('session.cookie_lifetime', 7200);
session_set_cookie_params(0);
ini_set("max_execution_time", "120");
//error_reporting(E_ALL);

ob_start("ob_gzhandler"); // TODO: ���������

session_start(); 

// classes & functions: ��� ������� ����������� �� ���� ����������:
require_once(MAINURL_5."/code/modules/global.php"); 
require_once(MAINURL_5."/code/modules/mail.php"); // TODO: ����������

cats_tree();
?>