<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>����</title>
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

<b>�����: {COMPANY_ADDRESS}</b><br><br>

<b>������� ���������� ���������� ���������</b><br>

<table width=700px cellpadding=0 cellspacing=0 border=0 style="border-width:1px;border-color:#000000;border-style:solid;"><tr>
              <td style="padding:5px; border-right-width:1px;border-right-color:#000000;border-right-style:solid;padding-right:30px;">��� {INN}</td>
              <td style="padding:5px;">��� {KPP}</td>
			  <td></td>
            </tr>
            <tr>
              <td colspan=2 valign=top style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;padding:5px;border-right-width:0px;padding-right:30px;">����������<br>{COMPANY}</td>
              <td valign="bottom" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;padding:5px;border-right-width:0px;padding-right:30px;">��. � {RS}</td>
            </tr>
            <tr>
              <td colspan=2 style="padding:5px;padding-right:30px;">���� ����������<br>{BANK_NAME}</td>
              <td  style="padding:5px;">��� {BIK}<br>��. � {KORS}</td>
            </tr>
			</table>

<br><br><font size=4><b>���� � {OID}{SHOPLETTER} �� {DATE_PURCHASED} �.</b></font><br><br>
����������: {BILLING_NAME}<br>
���������������: {DELIVERY_NAME}<br><br>

<table width=700px cellpadding=2 cellspacing=0 border=0>
<tr>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;">�</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;">������������ ������</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=70>������� ���������</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=60>����������</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=100>����</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;" width=100>�����</td>
</tr>

####begin
<tr>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">{N_PRD}</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_NAME}</td>
<td align=center style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">��.</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_QUANTITY}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_PRICE_ONE}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{PRD_PRICE_TOTAL}</td>
</tr>
####end

<tr>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;">{N_LAST}</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">��������</td>
<td style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">-</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">1</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{DELIVERY_PRICE}</td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-left-width:0px;border-top-width:0px;">{DELIVERY_PRICE}</td>
</tr>

<tr><td colspan=4></td>
<td align="right" style="border-right-width:1px;border-right-color:#000000;border-right-style:solid;"><b>�����:</b></td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;border-left-width:0px;"><b>{TOTAL_PRICE}</b></td>
</tr>

<tr><td colspan=3></td>
<td align="right" colspan=2 style="border-right-width:1px;border-right-color:#000000;border-right-style:solid;"><b>��� ������ (���):</b></td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;border-left-width:0px;"><b>-</b></td>
</tr>

<tr><td colspan=4></td>
<td align="right" style="border-right-width:1px;border-right-color:#000000;border-right-style:solid;"><b>����� � ������:</b></td>
<td align="right" style="border-width:1px;border-color:#000000;border-style:solid;border-top-width:0px;border-left-width:0px;"><b>{TOTAL_PRICE}</b></td>
</tr>
</table>

<br>
����� ������������ {N_ALL}, �� ����� {TOTAL_PRICE}<br><b>{TOTAL_PRICE_PROPIS}</b>

<br><br><br>
������������ �����������_____________________ ({COMPANY_DIRECTOR})<br><br><br>
������� ���������____________________________ ({COMPANY_BUHG})<br><br>

</div>
</body>
</html>