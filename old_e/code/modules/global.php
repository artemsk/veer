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

function search() { $r=explode(",",SEARCH_DEFAULTS); $r2=trim($r[array_rand($r)]);

       }

function notifyadmin($type="0",$str="") { Debug::log(); 
     $auto_str="";  
     if($type=="1") { $auto_str="�������� ������� ��� ������:\n"; }
     $d="url: ".MAINURL."\n";
     mail(TECH_EMAIL, "[ORIENTIR] ���������, ��������� #".$type, $auto_str.$str."\n--------\n".$d, 
             "From: Orientir Notification Center <no-reply@".substr(MAINURL_4,1).">");
   }   

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