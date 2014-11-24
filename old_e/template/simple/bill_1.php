<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>Счет</title>
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
 font-size:12px;
}
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- body_text //-->
<div style="padding-left:10px;padding-top:10px;">

<b><u>{COMPANY}</u></b><br><br>

<b>Адрес: {COMPANY_ADDRESS}</b><br><br>

<b>Образец заполнения платежного поручения</b><br>

<table width=700px cellpadding=0 cellspacing=0 border=0 style="border-width:1px;border-color:#000000;border-style:solid;"><tr>
              <td style="padding:5px; border-right-width:1px;border-right-color:#000000;border-right-style:solid;padding-right:30px;">ИНН {INN}</td>
              <td style="padding:5px;">КПП {KPP}</td>
			  <td></td>
            </tr>
            <tr>
              <td colspan=2 valign=top style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;padding:5px;border-right-width:0px;padding-right:30px;">Получатель<br>{COMPANY}</td>
              <td valign="bottom" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;padding:5px;border-right-width:0px;padding-right:30px;">Сч. № {RS}</td>
            </tr>
            <tr>
              <td colspan=2 style="padding:5px;padding-right:30px;">Банк получателя<br>{BANK_NAME}</td>
              <td  style="padding:5px;">БИК {BIK}<br>Сч. № {KORS}</td>
            </tr>
			</table>

<br><br><font size=4><b>СЧЕТ № {OID}{SHOPLETTER} от {DATE_PURCHASED} г.</b></font><br><br>
Плательщик: {BILLING_NAME}<br>
Грузополучатель: {DELIVERY_NAME}<br><br>

<table width=700px cellpadding=2 cellspacing=0 border=0>
<tr>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;">№</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;">Наименование товара</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=70>Единица измерения</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=60>Количество</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=100>Цена</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=100>Сумма</td>
</tr>

####begin
<tr>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">{N_PRD}</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_NAME}</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">шт.</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_QUANTITY}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_PRICE_ONE}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_PRICE_TOTAL}</td>
</tr>
####end

<tr>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">{N_LAST}</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">ДОСТАВКА</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">-</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">1</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{DELIVERY_PRICE}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{DELIVERY_PRICE}</td>
</tr>

<tr><td colspan=4></td>
<td align="right" style="border-right-width:1px;border-right-color:#000000;border-right-style:solid;"><b>Итого:</b></td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;border-left-width:0px;"><b>{TOTAL_PRICE}</b></td>
</tr>

<tr><td colspan=3></td>
<td align="right" colspan=2 style="border-right-width:1px;border-right-color:#000000;border-right-style:solid;"><b>Без налога (НДС):</b></td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;border-left-width:0px;"><b>-</b></td>
</tr>

<tr><td colspan=4></td>
<td align="right" style="border-right-width:1px;border-right-color:#000000;border-right-style:solid;"><b>Всего к оплате:</b></td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;border-left-width:0px;"><b>{TOTAL_PRICE}</b></td>
</tr>
</table>

<br>
Всего наименований {N_ALL}, на сумму {TOTAL_PRICE}<br><b>{TOTAL_PRICE_PROPIS}</b>

<br><br><br>
Руководитель предприятия_____________________ ({COMPANY_DIRECTOR})<br><br><br>
Главный бухгалтер____________________________ ({COMPANY_BUHG})<br><br>

</div>
</body>
</html>