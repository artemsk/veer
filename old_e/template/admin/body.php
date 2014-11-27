<body style="background-image: url({ADM_IMG_PATH}/bg_dark2.png); background-position: top left; background-repeat:repeat-y; padding:0px; margin:0px auto;">

<div style="width:100%;float:left;">
       <div id="message_block" style="position:relative;display:block; float:right;width:16%; z-index:98;">
           <div id="message_form1" style="display:none;">{MESSAGE_FORM}</div>
           <div id="message_form2" style="display:block;"><a href="javascript:show('message_form1');hide('message_form2');">Написать</a></div>
           <div id="messages">{MESSAGES}</div>
    </div>
    
    <div id="container" style="position:relative; z-index:97; background-color:#ffffff; float:left;position:relative;margin-left:1%;width:82%; ">
        <div class="{NOTIFY_CLS}">{NOTIFY}</div>
        <table width="100%" border="0" style="margin-top:-7px; background-color: #ffffff; "><tr>
                <td width="150px"><div class="adm_username">{ADMIN_USERNAME} <a href="{MAINURL_ADM}/logout"><img src="{ADM_IMG_PATH}/out2.png" border="0" align="absbottom"></a></div>
                </td>
                <td align="center" style="background-image: url({ADM_IMG_PATH}/bg_light2.png); background-position: top left; background-repeat:repeat;">
                </td>
                <td width="130px" align="center" style="background-color:#ffffff;padding:10px 10px 0px 10px;"><a href="{MAINURL_ADM}/orientir"><img src="{ADM_IMG_PATH}/sett.png" border="0" align="absbottom"></a>&nbsp; &nbsp;<a href="#" id="view3" style="border-bottom: 0px #cf1a59 solid;"><img src="{ADM_IMG_PATH}/view3.png" border="0"></a>&nbsp; &nbsp;<a href="#" id="view1" style="border-bottom: 2px #cf1a59 solid;"><img src="{ADM_IMG_PATH}/view1.png" border="0"></a>&nbsp; &nbsp;<a href="#" id="view2"  style="border-bottom: 0px #cf1a59 solid;"><img src="{ADM_IMG_PATH}/view2.png" border="0"></a></td>
            </tr>
        </table>
<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
        <td width="150px" valign="top" style="background-image: url({ADM_IMG_PATH}/bg_dark3.png); background-position: top left; background-repeat:repeat-y; ">
                             <div id="adm_menu">
                             <a id="lnk_orders" href="{MAINURL_ADM}/orders">Заказы</a><p></p>
                             <a id="lnk_catalog" href="{MAINURL_ADM}/catalog">Каталог</a><p></p>
                             <a id="lnk_customers" href="{MAINURL_ADM}/customers">Клиенты</a><p></p>
                             <a id="lnk_pages" href="{MAINURL_ADM}/pages">Тексты</a><p></p>
                             <a id="lnk_shops" href="{MAINURL_ADM}/shops">Магазины</a>                             
                             {CONTEXT_MENU}</div><br><br><br><br>
                             
        </td><td valign="top" class="container2">
            <div style="background-image: url({ADM_IMG_PATH}/bg_light2.png); background-position: top left; background-repeat:repeat;height:7px;"><font style="font-size:7px;">&nbsp;</font></div>

        {MAIN_BLOCK}

        </td></tr></table>

    </div>


</div>
</body>
