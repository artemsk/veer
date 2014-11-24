<body>
    <!-- top block -->    
    <div id="notifyblock" class="{NOTIFY_CLS}">{NOTIFY}</div>
    <div class="top_top_blok" style="z-index:99">
        <div style="margin: 0px auto; overflow:auto; width:100%; max-width:1024px; 
            width:expression(document.body.clientWidth > 1024? '1024pxpx': '100%' );">
            <div class="top_blok" style="margin: 0px auto; overflow:hidden; width:100%; max-width:1024px; 
                 width:expression(document.body.clientWidth > 1024? '1024pxpx': '100%' );">
                <div class="up_basket">{BASKET_L}корзина: {BASKET} шт.{BASKET_L2}</div>
                <div class="up_header">{BASKET_HEAD}{LOGIN_F}</div>
            </div><div id="callme_content"></div>
        </div></div>
    
    <!-- main -->
    <div class="main_block" style="z-index: 95; width:100%;">   
        <!-- left -->
        <div id='leftdiv' class="left_div">

            <!-- left column -->
            <div class="header_logo" ><a href="{SHOP_PATH}" class='imghref'><img alt="" src="{IMG_PATH}/zland_a2.gif" border="0" style="0.9"></a>
                <div style="margin-top:30px;text-align:right;">{SEARCH}</div>
                {CATEGORIES_ANCHOR}
                <div class="cat_list">{CATEGORIES}</div>
                <div class="attr_list">{ATTR_2}</div>
                <div class="attr_list">{POPULARATTRS}</div>
                <div class="keyword_list">{POPULARKEYWORDS}</div>              
                <div class="last_articles">{LAST_ARTICLES}</div>
                <div class="popular_cats">{POPULAR_CATS}</div>
                {BANNERS_2}{PAGE_4}
            </div>

        </div>
        
        <!-- right -->
        <div id="rightdiv" class="right_div">

            <!-- container -->
            <div class="{BODY_DIV}" style="margin-top: 0px; width:100%; 
                 max-width:{MAXWIDTH}px; width:expression(document.body.clientWidth > {MAXWIDTH}? '{MAXWIDTH}px': '100%' );">

                <!-- right top -->
                <div class="phone_cls" style="float:right;{COMPARE_CLEAN}">{PAGE_1}<p class="commonp"></p>
                     <div class="page_on_main">{PAGE_2}</div>
                     <div class="callme_st">{CALLME}</div><div style="clear:both;"></div>
                     <div class="page_on_main" style="text-align:left;margin-top:20px;">{PAGE_3}</div>
                     {CAT_NEWS_ALL_RIGHT}
                     <!-- only on main -->
                     <div class="markd_all" style="clear:both;"></div>  
                     <div class="markd_all recomendations_products">{RECOMENDATIONS}</div>
                     <div class="markd_all recomendations_products">{POP_PRDS}</div>
                     <div class="markd_all" style="clear:both;"></div> 
                     <div class="markd_all recomendations_products">{RANDOM_PRDS_1}</div><div class="markd_all" style="clear:both;"></div>
                     <div class="markd_all recomendations_products">{RANDOM_PRDS_2}</div><div class="markd_all" style="clear:both;"></div>
                     <div class="markd_all recomendations_products">{RANDOM_PRDS_3}</div><div class="markd_all" style="clear:both;"></div> 
                </div>

                <!-- main top column-->
                {TOP_MAINPAGE_PIC}
                <!-- <div class='headerback'><img src='{IMG_PATH}/back7.png'></div> -->

                <!-- main -->
                <div id="container_site">{CONTAINER}</div>

            </div> 

        </div>
        
    </div>    
       
    <div style="clear:both;"></div>
    <div style="margin: 0px auto; overflow:auto; width:100%; max-width:1024px; width:expression(document.body.clientWidth > 1024? '1024pxpx': '100%' );">
    &nbsp;</div>

    <div id="footer" class="footer" style="{COMPARE_CLEAN}">
        <div style="margin: 0px auto; overflow:auto; width:100%; max-width:1024px; width:expression(document.body.clientWidth > 1024? '1024px': '100%' );">
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
                <div style="clear:left;font-size:0.65em;margin-top:10px;color:#999999;">движок "Ориентир" (бд. {SQL_NUMS} вер. {ENGINE_VERSION} стр. {MYROWS} ск. {SPEED_LOAD} | {MEMORYUSED})</div>
                <!--- ^ --->
            </div>
        </div>
    </div>
</body>