<?php

if(!is_array($prd)) { // �������� �� bigpic
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

//0 - �������
//1 - ���������
//2 - �����
//3 - �������������
//4 - ��������
//5 - �����, ��������������
//6 - ������ �� �����
//
//
// TODO: ��������� ����������������� �� ��������: ��������
//5 - ����� ��, remote_addr ��������
//4 - ��������� �������, ����� ��, ���������� ������ � ��
//3 - �� ����, �� ���������� ������ ������ ���� � �����
//2 - �� ����, ������ ������� �� ��� ���������� � ������� ���������������!
//1 - �� ���, ������ ������ ������� � ������
?>