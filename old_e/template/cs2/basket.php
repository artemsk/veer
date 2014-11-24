<?php

// Шаблон корзины
// - внутри блоков может быть что угодно, но их порядок должен быть сохранен
// - 1. пустая корзина
// - 2. общие данные
// - 3. строка с товаром (единица)
// - 4. таблица корзины из строк и подитога
//
?>
<!--- 1 --->[####]
<div class="basket_descr">{BASKET_NOTE}</div>

<!--- 2 --->[####]
<div class="basket_total"><hr class="divider"><span id="total_prc">{TOTAL_CURRENT_PRICE}</span></div><p id="total_prc_p"></p>
<div class="basket_descr">Без доп. свойств и параметров заказа: <strong>{TOTAL_PRICE_NO_ATTR}</strong><p></p>
Кол-во наименований: <strong>{TOTAL_QUANTITY} шт.</strong><p></p>
Общий вес: <strong>{TOTAL_WEIGHT} г.</strong><p></p></div>{QUICK_ORDER_1}

<!--- 3 --->[####]
<tr><td valign="top" width="250" class="bsk_borders" style="border-left:1px #999999 dashed;">{IMG}</td>
    <td valign="top"  class="bsk_borders"><div class="basket_prd_nazv"><a href="{NAZV_LINK}">{NAZV}</a><p></p>
        {ATTR_FORM}<p></p>{ATTR_DESCR}</div></td><td valign="top" class="bsk_borders">{PRD_QUANTITY}</td><td valign="top" class="bsk_borders"><div class="basket_prd_price">{CURRENT_PRICE}</div></td><td valign="top" width="30px" class="bsk_borders"><img src="{IMG_PATH}/del.gif">{PRD_DEL}</td></tr>

<!--- 4 --->[####]
<div class="basket_tbl"><span class="basket_title">{BASK_INFO}</span><p></p>
<table cellpadding="7">{CONTENT}<tr><td class="bsk_borders" colspan="2" style="border-right-width:0px;">
Общее количество в корзине: {BASK_Q} шт.<p></p>
Итого по корзине: {BASK_P}<p></p>
Вес корзины: {BASK_W} г.
<td valign="top" class="bsk_borders" colspan="3" style="text-align:right;border-right-width:0px;"><input type="submit" class="update_cart_button" name="update_cart" value="обновить корзину" disabled="disabled" style="font-size:1.2em;"></td></tr></table>
{QUICK_ORDER_2}<table style="border-width:0px;"><tr>
{BASK_ADDRESS}<td valign="top">
<div class="basket_steps">Способы доставки</div><hr class="divider">
<div class="basket_tbl_delivery" {ERROR_STYLE2}>{BASK_DELIVERY}</div>
</td></tr></table>
<table style="border-width:0px;"><tr>{BASK_PAYMENT}{BASK_COMMENTS}</tr></table>
</div>