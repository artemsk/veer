<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>Счет-фактура</title>
<script type="text/javascript">
function show(elementname)
{
 document.getElementById(elementname).style.display='block';
}

function hide(elementname)
{
 document.getElementById(elementname).style.display='none';
}
</script>
<style>
table, body {
 font-family:Arial, Helvetica, sans-serif;
 font-size:9px;
}
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- body_text //-->

<div style="padding-left:10px;padding-top:10px;width:950px;">

<div align="right">
Приложение №1<br>
к Правилам ведения журналов учета полученных и выставленных счетов-фактур,<br>
книг покупок и книг продаж при расчетах по налогу на добавленную стоимость,<br>
утвержденным постановлением Правительства Российской Федерации от 2 декабря 2000 г. N 914<br>
(в редакции постановлений Правительства Российской Федерации<br>
от 15 марта 2001 г. N 189, от 27 июля 2002 г. N 575, от 16 февраля 2004 г. N 84, от 11 мая 2006 г. N 283)<br>
</div>

<br><font size=4><b>Счет-фактура № {OID}{SHOPLETTER} от {DATE_PURCHASED} г.</b></font><p style="margin:2px 0 0 0;padding:0;"></p>
<font style='font-size:10px;'>
Продавец: {COMPANY}<p style="margin:2px 0 0 0;padding:0;"></p>
Адрес: {COMPANY_ADDRESS}<p style="margin:2px 0 0 0;padding:0;"></p>
ИНН/КПП продавца: {INN}/{KPP}<p style="margin:2px 0 0 0;padding:0;"></p>																							
Грузоотправитель и его адрес: он же<p style="margin:2px 0 0 0;padding:0;"></p>	
Грузополучатель и его адрес: {DELIVERY_NAME}
{DELIVERY_ADDRESS}
<p style="margin:2px 0 0 0;padding:0;"></p>																			
К платежно-расчетному документу № -- от --<p style="margin:2px 0 0 0;padding:0;"></p>
Покупатель: {BILLING_NAME}	<p style="margin:2px 0 0 0;padding:0;"></p>
Адрес: {BILLING_ADDRESS}
<p style="margin:2px 0 0 0;padding:0;"></p>																				
ИНН/КПП покупателя: {YUR_INN}<p style="margin:2px 0 0 0;padding:0;"></p>
</font>
<div style="width:950px;" align="right">Валюта: руб</div><table width=950px cellpadding=2 cellspacing=0 border=0>
<tr>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;">Наименование товара (описание выполненных работ, оказанных услуг), имущественного права</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=60>Единица измерения</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=60>Количество</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=100>Цена (тариф) за единицу измерения</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=100>Стоимость товаров (работ, услуг), имущественных прав, всего без налога</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=50>В том<br>числе<br>акциз</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=50>Налоговая<br>ставка</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=50>Сумма налога</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=100>Стоимость товаров (работ, услуг), имущественных прав, всего с учетом налога</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=60>Страна происхож- дения</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=60>Номер таможенной декларации</td>
</tr>
<tr>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">1</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">2</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">3</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">4</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">5</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">6</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">7</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">8</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">9</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">10</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">11</td>
</tr>

####begin
<tr>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">{PRD_NAME}</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">шт.</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_QUANTITY}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_PRICE_ONE}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_PRICE_TOTAL}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">--</td>
<td align="center" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">Без НДС</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">--</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_PRICE_TOTAL}</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">--</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">--</td>
</tr>
####end

<tr>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">ДОСТАВКА</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">-</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">1</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{DELIVERY_PRICE}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{DELIVERY_PRICE}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">--</td>
<td align="center" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">Без НДС</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">--</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{DELIVERY_PRICE}</td>
<td colspan=2></td>
</tr>

<tr><td colspan=7 style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">Всего к оплате:</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">--</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{TOTAL_PRICE}</td>
<td colspan=2></td>
</tr>

</table>

<br><br>
<table><tr><td style="padding-left:50px;">Руководитель организации </td><td width=10px></td><td style="border-bottom-width:1px;border-bottom-color:#000000;border-bottom-style:solid;" width=150px>&nbsp;</td><td width=10px></td><td style="border-bottom-width:1px;border-bottom-color:#000000;border-bottom-style:solid;" width=100px> {COMPANY_DIRECTOR} </td><td width=80px></td><td>Главный бухгалтер </td><td width=10px></td><td style="border-bottom-width:1px;border-bottom-color:#000000;border-bottom-style:solid;" width=150px>&nbsp;</td><td width=10px></td><td style="border-bottom-width:1px;border-bottom-color:#000000;border-bottom-style:solid;" width=100px> {COMPANY_BUHG}</td></tr>
<tr><td></td><td></td><td align="center">(подпись)</td><td></td><td align="center">(ф.и.о.)</td><td></td><td></td><td></td><td align="center">(подпись)</td><td></td><td align="center">(ф.и.о.)</td></tr>
</table><br><br>

<table><tr><td style="padding-left:10px;">Индивидуальный предприниматель</td><td width=10px></td><td style="border-bottom-width:1px;border-bottom-color:#000000;border-bottom-style:solid;" width=150px>&nbsp;</td><td width=10px></td><td style="border-bottom-width:1px;border-bottom-color:#000000;border-bottom-style:solid;" width=100px>&nbsp;</td><td width=80px><td style="border-bottom-width:1px;border-bottom-color:#000000;border-bottom-style:solid;" width=400px>&nbsp;</td></tr>
<tr><td></td><td></td><td align="center" valign="top">(подпись)</td><td></td><td align="center" valign="top">(ф.и.о.)</td><td></td><td>(реквизиты свидетельства о государственной
регистрации индивидуального предпринимателя)</td></tr>
</table><br><br><br>
Примечание. Первый экземпляр - покупателю, второй экземпляр - продавцу

</div>
</body>
</html>