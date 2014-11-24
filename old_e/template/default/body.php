<body><div class="{BODY_DIV}" style="margin: 0px auto; width:100%; max-width:{MAXWIDTH}px; width:expression(document.body.clientWidth > {MAXWIDTH}? '{MAXWIDTH}px': '100%' );">

<!-- top blok -->
<div class="top_blok" style="width:100%; max-width:{MAXWIDTH}px; width:expression(document.body.clientWidth > {MAXWIDTH}? '{MAXWIDTH}px': '100%' );">
<div class="{NOTIFY_CLS}">{NOTIFY}</div>
<div class="up_basket" style="background-image:url({IMG_PATH}/left_r.gif);background-repeat:no-repeat;background-position:left bottom;">{BASKET_L}корзина: {BASKET} шт.{BASKET_L2}</div>
<div class="up_header" style="background-image:url({IMG_PATH}/right_r.png);background-repeat:no-repeat;background-position:right bottom;">{LOGIN_F}</div>
<div id="head_content">{ADD2LIST}</div>
</div><div id="callme_content"></div>

<div class="{EVERYTHING_ELSE}">

<!-- left column -->
<div class="header_logo" style="clear:left;float:left;"><a href="{SHOP_PATH}"><img alt="" src="{IMG_PATH}/logo.jpg" border="0"></a>
    <div style="margin-top:30px;text-align:center;">{SEARCH}</div>
    {NEWS_IMPORTANT}{CATEGORIES_ANCHOR}
    <div class="cat_list">{CATEGORIES}</div>
    <div class="keyword_list">{POPULARKEYWORDS}</div>
    <div class="attr_list">{POPULARATTRS}</div>
    <div class="attr_list">{ATTR_2}</div>
    <div class="last_articles">{LAST_ARTICLES}</div>
    <div class="popular_cats">{POPULAR_CATS}</div>
    {BANNERS_2}{PAGE_4}
</div>

<!-- container -->
<div class="container">


<!-- right top -->
<div class="phone_cls" style="float:right;{COMPARE_CLEAN}">{PAGE_1}<p></p>
    <div class="callme_st">{CALLME}</div>
    <div class="page_on_main">{PAGE_2}</div>
    {CAT_NEWS_ALL_RIGHT}
</div>

<!-- main top column-->
<div class="{HEADER_TOP_CSS}">
<div class="header_chosen">{TOP_MAINPAGE_PIC}</div>
<div class="{HEADER_NAME_CSS}">{HEADER_NAME_PRD}</div>
</div>

<!-- main -->
<span id="container_site">{CONTAINER}</span>

</div>


</div></div>


    <div class="footer" style="{COMPARE_CLEAN}">
        <div style="margin: 0px auto; overflow:auto; width:100%; max-width:{MAXWIDTH}px; width:expression(document.body.clientWidth > {MAXWIDTH}? '{MAXWIDTH}px': '100%' );">
            <div style="text-align:left;margin-left:10px;">
                <div style="float:left;margin-right:50px;">{PAGE_1}</div>
                <div style="float:left;margin-right:50px;font-size:0.65em;">
                    {PAGE_5}
                </div>
                <div style="float:left;margin-right:50px;font-size:0.65em;">
                    {PAGE_6}
                </div>
                <div style="float:left;margin-right:50px;font-size:0.65em;">
                    {PAGE_7}
                </div>
                <!--- эта часть кода должна оставатьcя в шаблоне! --->
                <div style="clear:left;font-size:0.65em;margin-top:10px;color:#999999;">движок "Ориентир" (бд. {SQL_NUMS} вер. {ENGINE_VERSION} стр. {MYROWS} ск. {SPEED_LOAD})</div>
                <!--- ^ --->
            </div>
        </div>
    </div>
</body>