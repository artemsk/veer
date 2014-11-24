<?php
// nnn, shop_cat

$showpage=new pages; 
$page_0=$showpage->show_page(@$detected[1]);
$page=@$page_0[@$detected[1]];

if(count(@$page)>0) {  

    $title_head=$page['nazv'];
    
    $txt=textprocess($page['txt'],"basic");
    
    $info['4out']['{PAGE_NAZV}']="<div class='page_nazv_2'>".$page['nazv']."</div>";
    $info['4out']['{PAGE_FLAG}']="<div class=\"page_flag_2\">[".date("d.m.Y",$page['dat'])."]</div>";
    if(@$page['txt']!="") { 
        $page['txt']=$page['txt'].@$page['txt_full'];
    $info['4out']['{PAGE_TXT}']="<div class=\"page_txt\">".$txt."</div>"; }
    if(@$page['pic']!="") { $info['4bloks']['show_in_bigpic']=$page['pic'];  }

    // учет посещений
    $showpage->update_views(@$detected[1]);
    
    }

// отобр. комментарии
$comms=$showpage->comments(@$detected[1],@$page['comm_allow']);

if(count($comms)>0) { $comments_number="<span class=\"comments_number\">".COMMENTS_TXT." <a href=".MAINURL."/page/".@$detected[1]."#comms>".count($comms)."</a></span>";
 $info['4out']['{PAGE_NAZV}']=substr($info['4out']['{PAGE_NAZV}'],0,-6).$comments_number."</div>";
$info['4bloks']['comments']=$comms; }

// создание формы для добавление комментариев 
if(!isset($user_forms)) { $user_forms=new forms; }
$review=$user_forms->comment_form($detected[1],@$page['comm_allow']);
$info['4out']['{COMMENTS_FORM}']=@$review;
$info['4bloks']['comm_allow']=@$page['comm_allow'];



// $title_head, {PAGE_NAZV} + $comments_number, {PAGE_FLAG}, {PAGE_TXT}, $info['4bloks']-> show_in_bigpic, comments, comm_allow, {COMMENTS_FORM}


 // IF ORIGINAL PAGE DESGIN:
 //

  if(count(@$page)>0) {  
  $check_original_design=MAINURL_5."/template/".TEMPLATE."/p_".@$detected[1].".html";
  $check_original_design_head=MAINURL_5."/template/".TEMPLATE."/p_".@$detected[1]."_head.html";
  $check_original_design_halfhead=MAINURL_5."/template/".TEMPLATE."/p_".@$detected[1]."_halfhead.html";
 
  if(file_exists($check_original_design)) {
     $info['4bloks']['BREAK_OUTPUT_FLAG']="1";
     $info['4bloks']['BREAK_OUTPUT_FILE_BODY']=$check_original_design;

     if(file_exists($check_original_design_head)) { 
     $info['4bloks']['BREAK_OUTPUT_FILE_HEAD']=$check_original_design_head;    
     } else {
     $info['4bloks']['BREAK_OUTPUT_FILE_HEAD']=MAINURL_5."/template/".TEMPLATE."/head.php";    
     }

     if(file_exists($check_original_design_halfhead)) { 
     $head_filename=$check_original_design_halfhead; // additional head params    
     }

     // $title_head
     // $info['4out']['{COMMENTS_FORM}']=@$review;
     // $info['4bloks']['comm_allow']=@$page['comm_allow'];
     // $info['4bloks']['show_in_bigpic']=$page['pic'];
     // $info['4bloks']['comments']=$comms;
     $info['4out']['{PAGE_NAZV}']=$page['nazv'];
     $info['4out']['{PAGE_FLAG}']=date("d.m.Y",$page['dat']);
     $info['4out']['{PAGE_TXT}']=$txt;
     if(count($comms)>0) { $info['4out']['{COMMENTS_NUMBER}']=count($comms); }
     
 }}
 