<body>
{NOTIFY}

{CONTAINER}


 <footer>
     <div class="container-fluid footer">
         <div class="row">
             <div class="col-sm-3 text-right"><p class="footer-block">мы в социальных сетях</p></div>
             <div class="col-sm-4">
                 <p class="footer-block">Кондитерский магазин</p>
                 <p class="footer-block">&copy; 2014 москва</p>
                      <p class="footer-block">Бла бла бла бла бла бла<br/>
                      бла бла бла бла бла бла бла</p>
                  </div>
                  <div class="col-sm-13">
                      <div class="tags-lnks footer-block"><a href="?6">все тэги</a></div>
                      <div class="tags-lnks footer-block"><a href="?7">авторы</a></div>
                      <div class="tags-lnks footer-block"><a href="?8">журналы</a></div>
                      <div class="tags-lnks footer-block"><a href="?9">подписка</a></div>
                      <div class="clearfix"></div>
                      <br/>
                      <span class="footer-block">Еще какой-то текст</span>
                      <br/><br/>
                      <!--- эта часть кода должна оставатьcя в шаблоне! --->
                      <span class="footer-block">движок "Ориентир" (бд. {SQL_NUMS} вер. {ENGINE_VERSION} стр. {MYROWS} ск. {SPEED_LOAD} пам. {MEMORYUSED})</span>
                      <!--- ^ --->    
                  </div>
                  <div class="col-sm-4">
                      <p class="footer-block">Сделано в Большой Мастерской</p><br/><br/>
                      <p class="footer-block">Движок Ориентир</p>
                  </div>
              </div>
          </div>
</footer>

<!-- basket -->
<div id="to-top">
  <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>{BASKET}
</div>
      
 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{TEMPL_PATH}/js/bootstrap.min.js"></script>
    <script src="{TEMPL_PATH}/js/holder.js"></script>
    <script src="{TEMPL_PATH}/js/shop.js"></script>
  </body>
</html>