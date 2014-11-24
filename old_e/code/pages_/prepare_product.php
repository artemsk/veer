<?php
if($detected[1]==""||$detected[1]=="all") { exit; }

    // заменяем шаблон
    $body_filename=MAINURL_5."/template/".TEMPLATE."/product.php";
    $head_filename=MAINURL_5."/template/".TEMPLATE."/head_add.php";

// remote добавление в корзину
if(substr($detected[1],-9)=="/add2cart") {  $detected[1]=substr($detected[1],0,-9);
     $product_action=new products;
     $product_action->add2basket(array("prd_id"=>$detected[1]),'outdoor'); // добавляем в список
}

// списки, форма, очищаем кэш
    if(isset($_SESSION['customers_id'])&&@$_SESSION['logstat_temp']!="2"&&$_SESSION['logstat_temp']!=""&&$_SESSION['logstat_temp']!="0") {
    $clearrecs=array(SHOP_NNN."_cid".@$_SESSION['customers_id']."_recomend_prd",                     
                     SHOP_NNN."_cid".@$_SESSION['customers_id']."_recomend_base_on_keywords_templ_pid".$detected[1]
                     );
    clearfile($clearrecs, "0", "txts"); // cache_investigation
    }

    if(!isset($user_forms)) { $user_forms=new forms; } $add2list=$user_forms->add2list_form(@$detected[1]);
    $info['4out']['{ADD2LIST}']=@$add2list;
    
    $showprd=new products();

// полное
    $prd=$showprd->collect_products_full($detected[1]);

    if($prd['nnn']<=0) { exit; } // нет товара
    
    $prd=$showprd->show_product($prd,$detected[1],'full');

    $title_head=$prd['nazv'];
    
    $info['4bloks']['prd']=@$prd;
    if(count(@$prd['review'])>0) { $info['4bloks']['comments']=$prd['review']; }

// учет посещений товаров
    $prd_views=$showprd->update_views($detected[1]);

// создание формы для добавление комментариев и оценки
    if(!isset($user_forms)) { $user_forms=new forms; }
    $review=$user_forms->review_form($detected[1]);
    $rate_prd=$user_forms->rate_form($detected[1],@$prd['rate_yes'],@$prd['rate_no']);
    if(@$review!="") { $info['4out']['{COMMENTS_FORM}']=@$review; } else { if(count(@$prd['review'])<=0) { $info['4bloks']['comm_allow']="0"; }}

    $info['4bloks']['rate_prd']=@$rate_prd;