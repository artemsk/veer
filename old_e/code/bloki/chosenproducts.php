<?php

if(!is_array($prd)) { // передали из bigpic
$showprd=new products();
$prd=$showprd->collect_products(SHOP_NNN,'main'); unset($prd[@$check]);
}

if(!is_array($cats)) { $cats=new categories(); }
$look=$cats->gather(SHOP_NNN, 'full'); // $look['podrazdel']['nnn']
$templ_prd3=$showprd->product_listing($prd, $look, GROUP_MAIN_FLAG,array(@$_SESSION['check_used']=>@$_SESSION['check_used']));

$templ_prd3['used'][@$_SESSION['check_used']]=@$_SESSION['check_used'];
$_SESSION['check_used']="";

$out['{CHOSEN_PRODUCTS}']=@$templ_prd3['templ_prd'];

//$templ_prd88=$showprd->product_listing($prd,$look,GROUP_MAIN_FLAG,$templ_prd3['used'],"4","0");
//$out['{LEFTOUTS}']=@$templ_prd88['templ_prd'];

//0 - обычная
//1 - скидочная
//2 - гостя
//3 - дистрибьютора
//4 - оптовика
//5 - купон, индивидуальная
//6 - скидка на заказ
//
//
// TODO: проверить работоспособность по статусам: магазины
//5 - здесь же, remote_addr работает
//4 - отдельная корзина, здесь же, оформление заказа с бд
//3 - бд есть, но оформление заказа только файл и почта
//2 - бд есть, список товаров но при добавлении в корзину перенаправление!
//1 - бд нет, просто список товаров и ссылки
?>