<?
require_once("define_names.php");
$_SESSION['timer_global']=timer_begin();

////////////////////////////////////////////
////////////////////////////////////////////

    $detected=url_detect($_SERVER['REQUEST_URI']); // url_detect
    follow(@$detected, @$_SESSION['customers_id']); // follow visits

    /////////////////////////////////////////////
    // �������� ������ �� ������� ��������     //   ������ product ��������
    /////////////////////////////////////////////

        if(@$detected[0]!=""&&@$detected[1]!="") {
            $navigate=navigation($detected[0]); 
            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_all.php";            
            if(@$navigate!="") { require($navigate); }}
        if(@$detected[0]==""&&@$detected[1]=="") {
            $body_filename=MAINURL_5."/template/".TEMPLATE."/body_index.php";
            }       
            
    /////////////////////////////////
    // ������������ � �������      //
    /////////////////////////////////
         
        require(navigation("head_prepare")); // <head></head>  TODO: �������� �������� �������� � ����
        require(navigation("body_prepare")); // �������� �����

 
//clearfile("", "auto"); //2
clearfile("", "3720", "thumbs", "0", "744"); // 3
clearfile("", "5", "stats", "0", "5"); 
clearfile("", "72", "htmls", "0", "120"); // 1
clearfile("", "72", "ips", "0", "120"); // 4
savereferals(@$detected[0], @$detected[1]); 
if(DEBUG_MODE=="1") { debugfunc(); }
?>