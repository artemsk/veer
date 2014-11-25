<?php
//

// textprocess: txt
function textprocess($txt,$type="basic") { //TODO: �������� ���������� ������
    Debug::log(); 
    $zamena=array("<br>"=>"<br />","<div style=\"margin: 0pt\">&nbsp;</div>"=>"","&nbsp;"=>" ","face=\"Tahoma\""=>"");

    if($type=="sql") {
    $zamena=array_merge($zamena,array('"'=>"&quot;","'"=>"&quot;","INSERT"=>"","SELECT"=>"","DELETE"=>"","UPDATE"=>"","^"=>"","`"=>"","javascript:"=>"","function"=>"")); }
    
    $txt2=strtr($txt,$zamena);

    if($type!="sql") {
    $txt2=explode("<br />",nl2br($txt2)); $v2="";
    foreach($txt2 as $k=>$v) { if(trim($v)!="") { if(trim(strip_tags($v))=="") { $v2.=$v.""; } else { 
        if(trim(strip_tags($v))=="]]]") { $v2.=strtr($v,array("]]]"=>"<p style=\"margin:20px 0 0 0;padding:0 0 0 0;\"></p>")); } else {
        $v2.=$v."<p></p>"; }
        } } }
    $txt2=$v2;
    }
    
    return $txt2;
    }
//

function callme($fn="",$ln="",$ph="",$ret="str") { Debug::log(); 
    if(substr($fn,0,1)=="/") { $fn=""; }
    if(!is_object(@$newform)) { $newform=new forms; }
    $callmeform=$newform->callme_form($fn,$ln,$ph);
    $templ_callme="<div class=\"callmeform\">".$callmeform."</div>";
    $callme_str="<a class='callme_nav' href='callme.php' onclick=\"javascript:show('callme_content');hide('head_content');\">".CALLME_TXT."</a>";
    if($ret=="str") { return $callme_str; } else { return $templ_callme; } 
    }
///////////////

function navigation ($url_detect) { Debug::log(); 
    switch($url_detect) {
        case "/page/": return (MAINURL_5."/code/pages_/prepare_page.php"); break; //+
        case "/pageall/": return (MAINURL_5."/code/pages_/prepare_page_all.php"); break; // +
        case "/catalog/": return (MAINURL_5."/code/pages_/prepare_cats.php"); break; // +
        case "/attr/": return (MAINURL_5."/code/pages_/prepare_attrs.php"); break; // +
        case "/keyword/": return (MAINURL_5."/code/pages_/prepare_keywords.php"); break; //+
        case "/search/": return (MAINURL_5."/code/pages_/prepare_srch.php"); break; //+
        case "/product/": return (MAINURL_5."/code/pages_/prepare_product.php"); break; //+
        case "/user/": return(MAINURL_5."/code/pages_/prepare_user.php"); break; //+
        case "/code/ext/": return(MAINURL_5."/code/pages_/prepare_ajax.php"); break; //+
        case "/adm/": return(MAINURL_5."/code/pages_/prepare_adm.php"); break; //+
        case "/filter/": return(MAINURL_5."/code/pages_/prepare_filter.php"); break; //+

        case "head_prepare": return(MAINURL_5."/code/duck_head.php"); break; //+
        case "body_prepare": return(MAINURL_5."/code/some_body.php"); break; //+
    }
    return(MAINURL_5."/code/pages_/".substr($url_detect,1,-1).".php");
    }
///////////////////////////////////////////////
    ///////////////////////////////////////////
    ///////////////////////////////////////////

function cats_up($shop_cat=SHOP_NNN) { // ������ �� ��������
    Debug::log(); 
    $temp_nnn=$shop_cat;
    $gcl=cats_tree();

    // oldmethod
    // for($j=0;$j<=1000;$j++) { if($gcl[$temp_nnn]['type']=="shop") { break; } else { $temp_nnn=$gcl[$temp_nnn]['parent']; }}
    
    // alternative simplify 
    $up2shop=$gcl[$temp_nnn]['hostshop'];
    $temp_nnn=$up2shop;
    
    // $shop_cat / $temp_nnn /$up2shop
    
    $shoparr['nnn']=$temp_nnn;
    $shoparr['nazv']=$gcl[$temp_nnn]['nazv'];
    $shoparr['status']=$gcl[$temp_nnn]['status'];
    $shoparr['remote_addr']=$gcl[$temp_nnn]['remote_addr'];
    $shoparr['remote_always']=$gcl[$temp_nnn]['remote_always'];
    $shoparr['admin_email']=@$gcl[$temp_nnn]['admin_email'];
    return $shoparr;
    }

function cats_tree() { // ������� ������ ������ �� 1 mysql ������, ��������� ��� � ����, � ������!
    Debug::log(); 
    // 1
    
    if(@$_SESSION['cache']['global_cats_lst']!="") { return $_SESSION['cache']['global_cats_lst']; }
       
    // 2
    
    $z=mysql_kall("SELECT * FROM ".DB_PREFIX."catshop_config") or die(mysql_error());
    $x=mysql_fetch_assoc($z); if(mysql_num_rows($z)>0) {
        do { 
            foreach($x as $k=>$v) { if($k=="nnn") { continue; } if($k=="parent") { $x2['parent'][$v][$x['nnn']]=$x['nnn']; } $x2[$x['nnn']][$k]=$v; }
            } while($x=mysql_fetch_assoc($z));            
            $_SESSION['cache']['global_cats_lst']=$x2;
            return @$x2;
       }
    
    } /////

function get_include_contents($filename) { // ��� ������ � ���������� ��� ��������
    Debug::log(); 
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}

function cats($returntype="sql", $shop_cat=SHOP_NNN) { // sql, arr / ���� ���� �����������/������������ � �.�.
Debug::log(); 
        if(isset($_SESSION['cache']['cats'][$returntype][$shop_cat])) { return $_SESSION['cache']['cats'][$returntype][$shop_cat]; }
        
        $gcl=cats_tree();
        $temp_lst[0]=$shop_cat;
        $cats_lst[$shop_cat]['status']=$gcl[$shop_cat]['status'];
        $cats_lst[$shop_cat]['currency']=$gcl[$shop_cat]['currency'];
        $cats_lst[$shop_cat]['type']=$gcl[$shop_cat]['type'];
        $cats_lst[$shop_cat]['hostshop']=$gcl[$shop_cat]['hostshop'];
        $sql_whr="'".$shop_cat."'";

        for($j=0;$j<=1000;$j++) { // @reviewlate: ������ ����������� ������
            if(!isset($temp_lst[$j])) { break; }
            $shop_cat_parent=@$gcl['parent'][@$temp_lst[@$j]];
            if(count($shop_cat_parent)>0) {
                foreach($shop_cat_parent as $k=>$v) {
                    $temp_lst[]=$k;
                    if(!isset($cats_lst[$k])) { $sql_whr=$sql_whr.", '".$k."'"; }
                    $cats_lst[$k]['status']=$gcl[$k]['status'];
                    $cats_lst[$k]['currency']=$gcl[$k]['currency'];
                    $cats_lst[$k]['type']=$gcl[$k]['type'];
                    $cats_lst[$k]['hostshop']=$gcl[$k]['hostshop'];                    
                    }

            } else { if($j==(count($temp_lst)-1)) { break; } }
        }


       $sql_whr="AND (".DB_PREFIX."products_2_cats.shop_cat IN (".$sql_whr."))";
       if($sql_whr=="AND ()"||$sql_whr=="AND (".DB_PREFIX."products_2_cats.shop_cat IN ())") { $sql_whr=""; }

       $_SESSION['cache']['cats']['sql'][$shop_cat]=$sql_whr;
       $_SESSION['cache']['cats']['arr'][$shop_cat]=$cats_lst;
       
       if($returntype=="sql") { return @$sql_whr;  }
       if($returntype=="arr") { return @$cats_lst; }
    }

function imgprocess($img,$w="0",$h="0",$reprocess_flag="1", $thumb_path="upload/thumb/", $ret="str") { 
 Debug::log();    
    $repocess_flag = 0;
    
    if($img=="") { return false; }
    if($w>0) { $w2="&w=".$w; } else { $w2=""; }
    if($h>0) { $h2="&h=".$h; } else { $h2=""; }
    
    if(timer_stop($_SESSION['timer_global'])>0.5) { $reprocess_flag=0; } // stop img processing if too slow!
    
    if($reprocess_flag=="1") { $img2=imgprocess_copy($img, $w, $h, $thumb_path); }
    
    if($reprocess_flag=="1"&&@$img2['fotoname']!=""&&@$img2['endwidth']>0&&@$img2['endheight']>0) { 
        if($ret=="str") { return @$img2['fotoname']; }
        if($ret=="arr") { return $img2; }
        } else {
         $img2=MAINURL."/code/thumbimg.php?img=".$img.$w2.$h2.""; 
         
    if($ret=="str") { return $img2; }
    if($ret=="arr") { return array("fotoname"=>$img2,"endwidth"=>$w,"endheight"=>$h); }
    }}

function imgprocess_copy($fotoname,$endwidth="0",$endheight="0",$thumb_path="upload/thumb/") { //  fotoname, endwidth, endheight
    Debug::log(); 
    }

function currency_converter($price, $catshop_currency="0", $prd_currency="0") {
    Debug::log(); 
     $currency_price=$price;
     if(SHOP_CURRENCY>0) { $currency_price=$price*SHOP_CURRENCY; }
     if($catshop_currency>0) { $currency_price=$price*$catshop_currency; }
     if($prd_currency>0) { $currency_price=$price*$prd_currency; }
     return $currency_price;
    }

function sort_links($detected, $type="cat") { 
    Debug::log(); 
     $sort_types=explode(",",CATPAGE_SORT);
     foreach($sort_types as $k=>$v) { $v=trim($v); $v_dir=explode("/",$v); $v=trim(@$v_dir[0]);
         if(trim($v)=="shopcat"&&$type=="cat") { continue; }
         if($v==@$detected['sort_type']) { if(@$detected['sort_direction']=="desc") { $direction="asc"; } else { $direction="desc"; }} else {
             if(count(@$v_dir)>1) { $direction=trim(@$v_dir[1]); } else { $direction="desc"; }}
         $v2=MAINURL.$detected['0'].$detected['1']."/sort/".$v."/".$direction;
           if($v=="dat") { $v3="�� ���� ����������"; }
           if($v=="price") { $v3="�� ����";}
           if($v=="star") { $v3="�� ��������"; }
           if($v=="ordered_day") { $v3="�� ������� � ����"; }
           if($v=="ordered_month") { $v3="�� �������"; }
           if($v=="ordered") { $v3="�� ������� �� ��� �����"; }
           if($v=="viewed_day") { $v3="�� ���������� � ����"; }
           if($v=="viewed_month") { $v3="�� ����������"; }
           if($v=="viewed") { $v3="�� ���������� �� ��� �����"; }
           if($v=="status") { $v3="�� �������"; }
           if($v=="type") { $v3="�� ����"; }
           if($v=="shopcat") { $v3="�� �������"; }
           if($v==@$detected['sort_type']) { $v3="<span class='sort_detected'>".$v3."</span>"; }
         $sort_arr[$v2]=$v3;
         }
         return $sort_arr;
    }

function formcreate($arr, $arrtype, $arrvalue, $arrattrs="") {
    Debug::log(); 
    // TODO: ���������� � ���������� ����������� ����� ������ �����

       foreach($arr as $k=>$v) {
        $str=""; $str_end="";

        if($arrtype[$k]=="text") { $str=$str."<input type='text' name='".$v."' ".@$arrattrs[$k]." value='"; $str_end="'>"; }
        if($arrtype[$k]=="text off") { $str=$str."<input type='text' name='".$v."' disabled  ".@$arrattrs[$k]." value='"; $str_end="'>"; }
        if($arrtype[$k]=="select") { $str=$str."<select name='".$v."' ".@$arrattrs[$k].">"; $str_end="</select>"; }
        if($arrtype[$k]=="password") { $str=$str."<input type='password' name='".$v."' ".@$arrattrs[$k]." value='"; $str_end="'>"; }
        if($arrtype[$k]=="textarea") { $str=$str."<textarea name='".$v."' ".@$arrattrs[$k].">"; $str_end="</textarea>"; }
        if($arrtype[$k]=="checkbox") { $str=$str."<input type='checkbox' name='".$v."' ".@$arrattrs[$k].""; if($arrvalue[$k]=="1") { $str=$str." checked"; } $str=$str.">"; $arrvalue[$k]=""; $str_end=""; }
        if($arrtype[$k]=="hidden") { $str=$str."<input type='hidden' name='".$v."' ".@$arrattrs[$k]." value='"; $str_end="'>"; }
        if($arrtype[$k]=="submit") { $str=$str."<input type='submit' name='".$v."' ".@$arrattrs[$k]." value='"; $str_end="'>"; }
        if($arrtype[$k]=="submit_img") { $str=$str."<input type='image' name='".$v."' ".@$arrattrs[$k]." src='"; $str_end="'>"; }
        if($arrtype[$k]=="radio") { $str=$str."<input type='radio' name='".$v."' ".@$arrattrs[$k]." value='"; $str_end="'>"; }

        if(count($arrvalue[$k])>1) { foreach($arrvalue[$k] as $kk=>$vv) {

            if($arrtype[$k]=="select") { $str=$str."<option value='".$kk."'>".$vv."</option>"; }

        } $str=$str.$str_end; } else { $str=$str.$arrvalue[$k].$str_end; }

        $str_return[$k]=$str;


        }

        return $str_return;
    } // form create

// login_check
function login_check() { // 0 - �� ��������, 1 - ��������, 2 - �� ������� ������, ����������� ������
    Debug::log(); 
    if((isset($_COOKIE['email'])&&isset($_COOKIE['lastlogon']))) {
    if(isset($_COOKIE['gender'])
            &&isset($_SESSION['customers_type'])&&isset($_SESSION['yur_status'])&&isset($_SESSION['customers_id'])
            &&isset($_SESSION['allow_orders'])&&isset($_SESSION['allow_everywhere'])&&isset($_SESSION['orig_catshop'])&&isset($_SESSION['customers_discount'])
            &&isset($_SESSION['customers_discount_expire'])&&isset($_SESSION['customers_basket_num'])&&$_SESSION['shop_logged']==SHOP_NNN) { $login_status=1; } else { $login_status=2; }
            //if(isset($_SESSION['lastlogon'])) { $ll_check=$_SESSION['lastlogon']; } else {
                $ll_check=$_COOKIE['lastlogon']; 
              //  }
     $last_check=mysql_call("SELECT lastlogon FROM ".DB_PREFIX."customers WHERE email='".$_COOKIE['email']."' AND lastlogon='".$ll_check."' AND hid!='1'");
     if(mysql_num_rows($last_check)<=0) { $login_status=0; }
        } else { $login_status=0; } // �����������       
    return $login_status;
    }

function show_customers_lists() {
    Debug::log(); 
     lists2session();
     if(isset($_SESSION['customers_lists'])) { $o="";
        $c=@unserialize($_SESSION['customers_lists']);
         foreach($c as $k=>$v) { if($v>0) { 
             $lnk1=""; $lnk2="";
             if($k=="������ ��� ���������") { $lnk1="<a href=".MAINURL."/user/compare>"; $lnk2="</a>"; }
             $o.=$lnk1.$k.$lnk2." (".$v."), "; }  }
         $o=substr($o,0,-2); return $o;
     }
 }   
 
// ������� � �����
function basket_head($d1,$d2, $t) {
    Debug::log(); 
        if($d1=="/product/"&&$d2!="") {
        $out="<div id='basket_head'>&larr; <a href=".MAINURL."/product/".$d2."/add2cart>��������</a> � ������� <a href=".MAINURL.
                "/product/".$d2."/add2cart>".txt_cut($t,50,'justcut')."</a></div>";
        if(!isset($_SESSION['customers_id'])) { $out.="<div id='basket_head_in'>&middot; <a href=javascript:show('login_div');hide('basket_head');hide('basket_head_in')>����</a></div>"; }        
        return $out;
        }
 }       
 
// ����� ��� ������ � �.�. � �����
function login_header($force="") {
    Debug::log(); 
        $out=""; 
        
        if($force!="1") { return $out; } // ������������� ��������� �������   

        $logstat=login_check();
       
        /// 0 - �������� �����
        if($logstat=="0") { $user_forms=new forms; $showform=$user_forms->login_form(); $out.=$showform; }

        $go_on=0;
        
        /// 2 - �������������� �������������
        if($logstat=="2") { $return_customer=new customers; $refer=$return_customer->login(array("remember_me"=>"0")); $go_on=1;
        // 
        if($_SESSION['wrong_shop']=="1") { $user_forms=new forms; $showform=$user_forms->login_form(); $out.=$showform; $_SESSION['wrong_shop']="0"; }   
        }

        /// 1 - ���������
        if($logstat=="1") { $go_on=2;
            if($_COOKIE['firstname']==""&&$_COOKIE['lastname']=="") { $username=$_COOKIE['email']; } else { 
                $username=$_COOKIE['firstname']." ".$_COOKIE['lastname']; }
        } 
            
        if($go_on=="1"||$go_on=="2") {
            
            $in_form_data = "";
            if ($_SESSION['customers_discount'] > 0) {
                if ($_SESSION['customers_coupon_active'] == "1") {
                    $in_form_data.="<b>�����</b> ";
                } else {
                    $in_form_data.="<b>������</b> ";
                }
                $in_form_data.=$_SESSION['customers_discount'] . "% ";
                if ($_SESSION['customers_discount_expire'] > 0) {
                    $in_form_data.="<b>��������</b> " . date("d.m.Y", $_SESSION['customers_discount_expire']) . " &nbsp; ";
                }
            }
            if ($_SESSION['yur_status'] == "1") {
                $in_form_data.="��.���� | ";
            }
            $in_form_data.=$_SESSION['customers_type_nazv'] . " ";
            
            if($go_on=="1") {
                $in_form_data.="<a href=".MAINURL."/user/".$_SESSION['customers_id'].">".$_SESSION['temp_firstname']." ".
                    $_SESSION['temp_lastname']."</a> &nbsp; "; }
            
            if($go_on=="2") {        
                $in_form_data.="<a href=".MAINURL."/user/".$_SESSION['customers_id'].">".$username."</a> &nbsp; "; }
            
            if($_SESSION['wrong_shop']!="1") { // @testing
            $user_forms=new forms; $showform=$user_forms->logout_form($in_form_data); $out.=$showform; }
            
        }   

        $iwant2see = $out;
        $_SESSION['logstat_temp']=$logstat;
        return $iwant2see;
     }



function write2file($what, $where, $ext = "txt", $time2write = "") { // ������ � ����; �����, ����� ��������� ������������
Debug::log(); 
    if (CACHE_SAVE_TYPE == "nocache") {
        return;
    }
    
    if (CACHE_SAVE_TYPE == "txt" || $ext == "html" || CACHE_SAVE_TYPE == "" || CACHE_SAVE_TYPE == "CACHE_SAVE_TYPE") { // html ������ ������������ � txt
        if (($time2write == "" && @filemtime(MAINURL_5 . "/code/txts/" . $where . "." . $ext) < (time() - 1)) || 
                ($time2write != "" && @filemtime(MAINURL_5 . "/code/txts/" . $where . "." . $ext) < (time() - (60 * 60 * $time2write)))) {
            $f = fopen(MAINURL_5 . "/code/txts/" . $where . "." . $ext, "w");
            fwrite($f, $what);
            fclose($f);
        }
    } // txt

    if (CACHE_SAVE_TYPE == "mysql" && $ext != "html") { // html �� ����������
        $allowwrite = 0;
        $e = mysql_kall("SELECT dat FROM " . DB_PREFIX . "cache_here WHERE whr='" . $where . "' AND extns='" . $ext . "'");
        $e2 = mysql_fetch_assoc($e);
        if ($time2write == "" && @$e2['dat'] < (time() - 1)) {
            $allowwrite = 1;
        }
        if ($time2write != "" && @$e2['dat'] < (time() - (60 * 60 * $time2write))) {
            $allowwrite = 1;
        }
        if (mysql_num_rows($e) <= 0) {
            $allowwrite = 2;
        }
        if ($allowwrite == "1") {
            mysql_kall("UPDATE " . DB_PREFIX . "cache_here SET whr='" . $where . "', extns='" . $ext . "', wht='" . 
                    strtr($what, array("'" => "&apos;")) . "', dat='" . time() . "'");
        }
        if ($allowwrite == "2") {
            mysql_kall("INSERT INTO " . DB_PREFIX . "cache_here (whr, extns, wht, dat) VALUES ('" . $where . "','" . $ext . "','" . 
                    strtr($what, array("'" => "&apos;")) . "','" . time() . "')");
        }
    } // mysql
}

function readfromfile($where, $overcook = "24", $ext = "txt") { // ������ �� �����, ����� ����� ���� ��������� ������������
Debug::log();     
    if (CACHE_SAVE_TYPE == "nocache") {
        return;
    }

    if (CACHE_SAVE_TYPE == "txt" || $ext == "html" || CACHE_SAVE_TYPE == "" || CACHE_SAVE_TYPE == "CACHE_SAVE_TYPE") {
        $f3 = "";
        $where2 = MAINURL_5 . "/code/txts/" . $where . "." . $ext;
        if (file_exists($where2)) {
            if (filemtime($where2) >= (time() - (60 * 60 * $overcook))) {
                $f = file($where2);
                $f2 = implode("", $f);
                return $f2;
                $f3 = $f2;
            }
        }
    } // txt

    if (CACHE_SAVE_TYPE == "mysql" && $ext != "html") {
        $f3 = "";
        $e = mysql_kall("SELECT dat, wht FROM " . DB_PREFIX . "cache_here WHERE whr='" . $where . "' AND extns='" . $ext . "'");
        $e2 = mysql_fetch_assoc($e);
        if (mysql_num_rows($e) > 0) {
            if ($e2['dat'] >= (time() - (60 * 60 * $overcook))) {
                $f3 = strtr($e2['wht'], array("&apos;" => "'"));
            }
        }
    } // mysql

    return $f3;
}

function clearfile($where="",$timeout="168",$what="txts",$onlycid="0",$auto_flag="0") { // ������� ���� � �������
Debug::log(); 
    $allow2clear=1;

    if($auto_flag>0) {
        $allow2clear=0; $cacheclear=readfromfile("CACHECLEAR_".$what,$auto_flag);
        if($cacheclear=="") { $allow2clear=1; }
    }
    
    if($allow2clear=="1") {

    // where-array,what-txts,thumbs,page_thumbs
    if($what=="txts") { $pth=MAINURL_5."/code/txts/"; $where2="txt"; }
    if($what=="thumbs") { $pth=MAINURL_5."/upload/thumb/"; }
    if($what=="page_thumbs") { $pth=MAINURL_5."/upload/pages/thumb/"; }
    if($what=="stats") { $pth=MAINURL_5."/stat/"; $where2="txt"; }
    if($what=="htmls") { $pth=MAINURL_5."/code/txts/total/"; $where2="html"; }
    if($what=="ips") { $pth=MAINURL_5."/code/txts/ips/"; $where2="txt"; }

    $time2clear=time()-(60*60*$timeout);

    if($where!=""&&$what=="txts") { 
        foreach($where as $k=>$v) {
            
        if(CACHE_SAVE_TYPE=="mysql") { 
            mysql_kall("DELETE FROM ".DB_PREFIX."cache_here WHERE whr='".$v."' AND extns='".$where2."' AND dat<='".$time2clear."'");         
        } else {
           $filenm=$pth.$v.".".$where2; if(@filectime($filenm)<=$time2clear) {
           @unlink($filenm); // TODO: ����� ����� ������� ���� ����?        
        }}
                  
        }
         return true;
     }

    if(CACHE_SAVE_TYPE=="mysql"&&$what=="txts"&&$where=="") { // 
       
       if($onlycid=="1") { // TODO: ���� cid=1 ��� cid=10 �� ��� ���������
       mysql_kall("DELETE FROM ".DB_PREFIX."cache_here WHERE extns='txt' AND whr!='CACHECLEAR' 
           AND whr!='CACHECLEAR_THUMBS' AND dat<='".$time2clear."' AND whr LIKE '%%%_cid".@$_SESSION['customers_id']."%%%'"); 
       } else {
       mysql_kall("DELETE FROM ".DB_PREFIX."cache_here WHERE extns='txt' AND whr!='CACHECLEAR' 
           AND whr!='CACHECLEAR_THUMBS' AND dat<='".$time2clear."'");
       }
       
    } else { // ��� ����� txts ��� ����������� �� cachetype ��� ��� txts ���� cachetype=txt 
     
    if($where==""||($what!="txts")) {
        if($where!="") { $where=array_flip($where); }
         $folder=array($pth);
         $path=$folder[0]; unset($folder[0]); 
         $end=1000; $allfolders=0;
         for($j=0;$j<=$end;$j++) {
         $qqq = @opendir($path);
         while ( $qqq2 = @readdir( $qqq ) ) {  if ( $qqq2 != '.' && $qqq2 != '..') {
            $qqq3=explode(".",$qqq2); 
            if(count($qqq3)>1) { 
                if($qqq==$pth) { continue; } //?
                if($onlycid=="1") { $qqq4=explode("_cid".@$_SESSION['customers_id'],$qqq3[0]); if(count($qqq4)>1) {} else { continue; }} // ���������� �����                
                if($where!="") { $qqq4=explode("_",$qqq3[0]); unset($qqq4[0],$qqq4[1]); $qqq5=implode("_",$qqq4); if($qqq5!="") {
                    if(isset($where[$qqq5])) {} else { continue; }}} // ��������                  
                if(trim($qqq3[1])=="jpg"||trim($qqq3[1])=="JPG"||trim($qqq3[1])=="gif"||trim($qqq3[1])=="GIF"||trim($qqq3[1])==$where2) { 
                if(filectime($path.$qqq2)<=$time2clear) { $files[]=$qqq2; $files_path[]=$path; } }
            } else { 
                if($qqq2=="txt"||substr($qqq2,0,6)=="thumb_") { continue; } $folder[]=$path.$qqq2."/"; $folder_double[]=$path.$qqq2."/"; $allfolders++; }
     }}
         $end=$j+count($folder);
         $kk=0;
         foreach($folder as $k=>$v) { if($kk=="0") { $path=$v; unset($folder[$k]); break; } $kk++; }
        }

        $zzz=0;
        if(count($files)>0) {
        foreach($files as $k=>$v) { if($v=="CACHECLEAR_THUMBS.txt"||$v=="CACHECLEAR.txt") { continue; }
        // $zzz." => ".$k." => ".$v." ".$files_path[$k]."<br>";
         @unlink($files_path[$k].$v);
         if($what!="txt") { @unlink($files_path[$k]."txt/".substr($v,0,-4).".txt"); }
         $zzz++;
        }
        }

        }

    } // cachetype=mysql&what=txts
    
        if($auto_flag>0) { write2file("1","CACHECLEAR_".$what); }        
        } // allow2clear
}

// follow customer
function follow($detected="", $c_id="") { // detected, customers_id
Debug::log();     
         if((@$detected[0]=="/catalog/"||@$detected[0]=="/product/")&&@$detected[1]!=""&&@$detected[1]!="all"&&@$detected[1]!="new"&&@$detected[1]!="comments"&&@$detected[1]!="ratings") {  // catalog, product
                  if($c_id!="") {} else { $c_id="0"; }
         $sql_whr="WHERE type='".@$detected[0]."' AND catshop_prd_id='".@$detected[1]."' AND customers_id='".$c_id."'";
         $sql="SELECT * FROM ".DB_PREFIX."customers_lastvisits ".$sql_whr;
         if(mysql_num_rows(mysql_kall($sql))>0) { mysql_kall("UPDATE ".DB_PREFIX."customers_lastvisits SET nums=nums+1, lastmodified=CURRENT_TIMESTAMP ".$sql_whr); } else {
              mysql_kall("INSERT INTO ".DB_PREFIX."customers_lastvisits (customers_id, type, catshop_prd_id, nums, lastmodified)
                                                         VALUES ('".$c_id."','".@$detected[0]."','".@$detected[1]."','1',CURRENT_TIMESTAMP)");
                 }
             }
         }
////////////////////////////////////

function img_mini($img="",$src="brief",$key_img="") { // ������ ����������� ������: ��� ������ ��������� ��������, ��� ��������� - ����
      //return $img; // list, prd
Debug::log();     
        if(@$img!="") {
                        if(substr($img,-1,1)==";") { $img=substr($img,0,-1); } // ������� ; � ����� ������
                        $img2=explode(";",$img);
                            if(count($img2)>1) { // � ������ ���� ��������� ��������
                                $v3=""; $v3_kaunt=ceil(GROUP_BLOK_WIDTH/IMG_HEIGHT_LIST_MAX)+1; 
                                if($v3_kaunt<=count($img2)) { $v3_h_overflow=(IMG_HEIGHT_LIST_MAX/2)-5; $v3_kaunt_w=ceil(GROUP_BLOK_WIDTH/$v3_kaunt)-10;
                                if(count($img2)==$v3_kaunt) { $v3_kaunt_w=$v3_kaunt_w+10; }} else { 
                                    $v3_h_overflow=IMG_HEIGHT_LIST_MAX-5; $v3_kaunt_w=ceil(GROUP_BLOK_WIDTH/count($img2))-10; }
                                foreach($img2 as $k=>$v) { if(trim($v)=="") { continue; } 
                                $v2="<div class='moreimg' style='width:".$v3_kaunt_w.";overflow:hidden;'>";
                                if($src=="full") { $v2.="<a rel=\"example_group\" title='���������' href=".MAINURL."/upload/".trim($v).">"; } else {
                                    if($key_img!="") { $v2.="<a class='imghref' href=".MAINURL."/product/".$key_img.">"; }}
                                $v2.="<img src='".imgprocess(MAINURL."/upload/".trim($v),"0",$v3_h_overflow)."' border=0 style='position:relative;left:0%;'>";
                                if($src=="full") { $v2.="</a>"; } else {if($key_img!="") { $v2.="</a>"; }}
                                $v2.="</div>";
                                $v3=$v3.$v2; } $img=$v3."<div style='clear:left;'></div>";
                                } else { 
                                    $img="<img border=0 src='".imgprocess(MAINURL."/upload/".$img,"0",IMG_HEIGHT_LIST_MAX)."'>";
                                    if($key_img!=""&&$src!="full") { $img="<a class='imghref' href=".MAINURL."/product/".$key_img.">".$img."</a>"; }
                                } // ���� ��������
            } else { $img="<img src='".imgprocess(MAINURL."/template/".TEMPLATE."/images/".BLANK_IMG)."'>"; } // ��������
        return $img;
      }

function img_full($img="") { // ��������� �������� � ����. �������� � ��������� ����
      //return $img;
 Debug::log();     
     if(isset($img)&&@$img!=""&&count($img)>0&&is_array($img)) { $v3=""; $v4=""; $kk=0;
      foreach($img as $k=>$v) { if($v!="") {
      if($kk=="0") { $v5=imgprocess(MAINURL."/upload/".$v,300,0,1,"upload/thumb/","arr");
      $v4="<a rel=\"example_group\" title='���������' href=".MAINURL."/upload/".$v."><img src=\"".$v5['fotoname']."\" border=0></a>"; } else {
      $v3=$v3."<div class='moreimg' style='float:left;'><a rel=\"example_group\" title='���������' href=\"".MAINURL."/upload/".$v."\"><img src='".imgprocess(MAINURL."/upload/".$v,0,50)."' border=0></a></div>"; }
      $kk++;
      }} $img['big']=@$v4; $img['all']=@$v3;
      }
      return $img;
      }

function lists2session() { // ���������� ������� � ���. ������
Debug::log();     
      if(!isset($_SESSION['customers_id'])) { $cid="0"; $temp_session=session_id(); } else { $cid=$_SESSION['customers_id']; $temp_session=""; }
      if(!isset($_SESSION['customers_lists'])) {     
       $c_lists=mysql_kall("SELECT SUM(quantity), list_name FROM ".DB_PREFIX."customers_basket_lists WHERE customers_id='".$cid."' AND temp_session='".$temp_session."' AND list_name!='[basket]' GROUP BY list_name");
       if(mysql_num_rows($c_lists)>0) {
       $c_lists2=mysql_fetch_assoc($c_lists);
       do { $c_lists3[$c_lists2['list_name']]=$c_lists2['SUM(quantity)']; } while($c_lists2=mysql_fetch_assoc($c_lists));
       $_SESSION['customers_lists']=serialize($c_lists3);
       }}
       return $_SESSION['customers_lists'];
      }

// ������� ������
function srch_history($q) { 
      Debug::log(); 
      $q=strip_tags($q);
      $q=strtr($q,array("/"=>"",".php"=>"",".htm"=>"",".html"=>"",".php"=>"",".js"=>"","javascript"=>"","java"=>"","http"=>"",
          '"'=>"","'"=>"","*"=>"","("=>"",")"=>"",";"=>"",":"=>"",","=>"","."=>"","INSERT"=>"","SELECT"=>"","DELETE"=>"","UPDATE"=>""));
      $i=mysql_call("SELECT customers_id, nnn FROM ".DB_PREFIX."search_history WHERE q='".trim(mb_strtolower($q))."'");
      if(mysql_num_rows($i)>0) {
          $i2=mysql_fetch_assoc($i); $i4="";
          if(isset($_SESSION['customers_id'])) { // ��������� customers_id
          $i3=explode("{".@$_SESSION['customers_id']."}",$i2['customers_id']);
          if(count($i3)>1) { $i4=""; } else { $i4=", customers_id='".$i2['customers_id']."{".$_SESSION['customers_id']."}'"; }}

          mysql_call("UPDATE ".DB_PREFIX."search_history SET nums=nums+1, dat='".time()."'".$i4." WHERE q='".trim(mb_strtolower($q))."'");
          $return_str=$i2['nnn'];

      } else { if(isset($_SESSION['customers_id'])) {
            mysql_call("INSERT INTO ".DB_PREFIX."search_history (q, nums, dat, customers_id) VALUES ('".trim(mb_strtolower($q))."','1','".time()."','{".$_SESSION['customers_id']."}')");
           } else {
            mysql_call("INSERT INTO ".DB_PREFIX."search_history (q, nums, dat) VALUES ('".trim(mb_strtolower($q))."','1','".time()."')");
          } $return_str=mysql_insert_id(); }
          return $return_str;
      }

// ������� �������� �������� �� ����� value
function trim_blank(&$item, &$key) { Debug::log(); $item=trim($item); }

function search() { Debug::log();  $r=explode(",",SEARCH_DEFAULTS); $r2=trim($r[array_rand($r)]);
        $user_forms=new forms; 
        $showform=$user_forms->search_form($r2);
        return $showform;
       }

function notifyadmin($type="0",$str="") { Debug::log(); 
     $auto_str="";  
     if($type=="1") { $auto_str="�������� ������� ��� ������:\n"; }
     $d="url: ".MAINURL."\n";
     mail(TECH_EMAIL, "[ORIENTIR] ���������, ��������� #".$type, $auto_str.$str."\n--------\n".$d, 
             "From: Orientir Notification Center <no-reply@".substr(MAINURL_4,1).">");
   }   
   
function savereferals($url="", $url2="") { Debug::log(); 
           if($url!="/code/ext/") {
           $zzz=strtr($_SERVER['HTTP_HOST']."_".$_SERVER['REQUEST_URI'],array("."=>"_","/"=>"_",":"=>"_","?"=>"_","="=>"_"," "=>"","+"=>""));
           if($_SERVER['HTTP_REFERER']!="") {
           $f=fopen(MAINURL_5."/stat/refers_".$zzz.".txt","a+");
           fwrite($f,"[s]".date("Y.m.d. H:i:s",time())."[l]".time()." <- [r]".$_SERVER['HTTP_REFERER']."[e]\n"); fclose($f);
           }
           if(DEBUG_MODE=="1") {
           $f=fopen(MAINURL_5."/code/txts/ips/".date("YmdH",time()).".txt","a+"); 
           fwrite($f,time()."[#]".$_SERVER["REMOTE_ADDR"]."[#]".$_SERVER['HTTP_REFERER']."[#]".$url."[#]".$url2."[#]".
           number_format(memory_get_usage())."\n"); fclose($f); } 
           }}

function price_format($price, $price_starter = "", $star = "0") { Debug::log(); 
            if (@$star <= 0 || $price == $price_starter || $price_starter == "") {
                if (round($price) == $price) {
                    return number_format($price, 0) . "" . ROUBLE;
                } else {
                    return number_format($price, 2) . "" . ROUBLE;
                }
            } else {
                
                $price_diff_percent = round(100 - ($price * 100 / $price_starter), 0);
                        if (round($price_starter) == $price_starter) {
                    $price_starter = number_format($price_starter, 0);
                } else {
                    $price_starter = number_format($price_starter, 2);
                }
                if (round($price) == $price) {
                    $price = number_format($price, 0);
                } else {
                    $price = number_format($price, 2);
                }
                $price_formated = "<font class='price_star_1 strikethrough '>" . $price_starter . 
                        "</font><wbr></wbr><font class='price_star_new'>" . $price . "" . ROUBLE . 
                        "</font> <font class='price_star_2'>" . PRICE_DIFF . "&nbsp;" . $price_diff_percent . "%</font>";
                 return $price_formated;
            }
           
       }

function prds_show_more($nums,$curr,$max=PRDS_PER_PAGE) { Debug::log(); 
                $how_many_pages=ceil($nums/$max); 
                //$start_from=($curr-1)*$max;
                $start_from=0;
                $start_from_next=$curr+1;
                $o['start_from']=$start_from;
                //if(($nums-2)>($start_from+$max)) { // -2: sory by shop, sort by cat!
                $o['4out']['{SHOW_MORE}']=
                "<div id=\"show_more_prds_content\"></div>
                 <div class=\"show_more_1\" id=\"show_more_next_".$start_from_next."\"><div class=\"show_more_2\"><a id=\"sm".$start_from_next."\" class=\"show_more_lnk\" href=\"#\">�������� ������</a></div></div>"; 
                
                //}
                if($curr>1) {
                     $o['4bloks']['BREAK_OUTPUT_FLAG']="1";
                     $o['4bloks']['BREAK_OUTPUT_FILE_BODY']=MAINURL_5."/template/".TEMPLATE."/break_output_only_products.php";
                } // ���� ������ ��� �� ���������� ������ ������ (��� ���������)
                return $o;
           }

function __unserialize($sObject) { Debug::log(); 
               $__ret =preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $sObject );
               return unserialize($__ret);
           }

function txt_cut($txt, $limit=100, $type="more", $type2="<br>-") { // type= more, abbr, justcut, wrap, zoom
    Debug::log(); 
               if(strlen($txt)>$limit) {
               if($type=="more") {
                           $r=rand(0,50).rand(0,50).rand(0,50).rand(0,50).rand(0,50);
                           $txt_new="<div id='txt_autocollapse_more".$r."' >".substr($txt,0,$limit).
                                   " <a href=javascript:show('txt_autocollapse".$r."');hide('txt_autocollapse_more".$r."');>...��������</a></div>".
                                   "<div id='txt_autocollapse".$r."' style='display:none;'>".$txt."</div>";
                            return $txt_new; }
               if($type=="abbr") {
                   $txt_new="<abbr title=\"".$txt."\">".substr($txt,0,$limit)."...</abbr>";
                   return $txt_new;
                   }
               if($type=="justcut") { $txt_new=substr($txt,0,$limit)."..."; return $txt_new; }
               if($type=="wrap") { $txt=strtr($txt,array(","=>" ,","."=>" .","\""=>" \"","'"=>" '"));
                    $txt2 = explode(" ", $txt);
                    $v2 = "";
                    foreach ($txt2 as $k => $v) {  
                        if (strlen($v) > $limit) {
                            $v2.=wordwrap($v, $limit, $type2, 1) . " ";
                        } else {
                            $v2.=$v . " ";
                        }
                    } $txt=strtr($v2,array(" ,"=>","," ."=>"<br>"," \""=>"\""," '"=>"'")); return $txt;
                }
               if($type=="zoom") { 
                    $txt2 = explode(" ", $txt);
                    $v2 = ""; $v3="1";
                    foreach ($txt2 as $k => $v) { 
                            if($v3<0.2) { $v3=0.2; }        
                            $v2.="<font style='font-size:".$v3."em;'>".$v."</font> ";
                            $v3=$v3-$limit;                        
                    } return $v2;
                }                
               } else { return $txt; } 
               }
      
// simplify array     
function simplifyarr($arr, $prevkey = "", $delim = "#") {
    $arr_new = array();
    $arr2 = array();
    if(is_array($arr)) {
    foreach ($arr as $k => $v) {

        $k2 = $prevkey . $delim . $k;

        if (is_array($v)) {
            $v2 = simplifyarr($v, $k2, $delim);
            $v = $v2[0];
            if (is_array($v2[1])) {
                $arr2 = $arr2 + $v2[1];
            }
        } else {
            $arr2[$k2] = $v;
        }

        $arr_new[$k2] = $v;
    }}
    return array($arr_new, $arr2); // 0 -> multidimension, 1 -> onedimension
}    
  