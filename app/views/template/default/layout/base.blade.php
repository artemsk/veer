<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="{{{ db_parameter('SITE_KEYWORDS') }}}" />
    <meta name="description" content="{{{ db_parameter('SITE_DESCR') }}}" />
    <title>НАЗВАНИЕ САЙТА И СТРАНИЦЫ</title>

    <!-- Bootstrap -->
    <link href="{{ URL::asset('assets/template/'.$template.'/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/template/'.$template.'/css/css.css') }}" rel="stylesheet">
    
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,900,700,300,300italic,900italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
      
    @yield('body')
      
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

                      <span class="footer-block">Veer {{ $app['veer']->statistics['version'] }} (q. {{ $app['veer']->statistics['queries'] }} l. {{ $app['veer']->statistics['loading'] }} m. {{ $app['veer']->statistics['memory'] }})</span>
  
                  </div>
                  <div class="col-sm-4">
                      <p class="footer-block">Сделано в Большой Мастерской</p><br/><br/>
                      <p class="footer-block">VEER Engine</p>
                  </div>
              </div>
          </div>
    </footer>
      
    <!-- basket -->
    <div id="to-top">
      <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>{{{ stored() }}}
    </div>
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ URL::asset('assets/template/'.$template.'/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/template/'.$template.'/js/holder.js') }}"></script>
    <script src="{{ URL::asset('assets/template/'.$template.'/js/shop.js') }}"></script>
  </body>
</html>