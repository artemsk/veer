<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <title>The Veer Layer - Administrating Route</title>

    <!-- Bootstrap -->
    <link href="{{ URL::asset('assets/'.$template.'/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/'.$template.'/css/css.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ URL::asset('assets/'.$template.'/css/animate.css') }}">
    
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

	<br/><br/><br/>
	<footer>
		<div class="container-fluid footer">
			<div class="row">
				<div class="col-sm-1 text-center">
				<p><a href="http://yandex.ru/cy?base=0&amp;host=koalalab.ru"><img src="http://www.yandex.ru/cycounter?koalalab.ru" width="88" height="31" alt="Индекс цитирования" border="0" /></a>
				</p>
				</div>
				<div class="col-sm-2">
					<p class="footer-block">&copy; 2014 Москва <br/>
						<strong>Кондитерский магазин<br/>«Кэндишоп»</strong></p>

				</div>
				 <div class="col-sm-7">
					 <div class="tags-lnks"><a href="?6">Все тэги</a></div>
					 <div class="tags-lnks"><a href="?7">Все свойства</a></div>
					 <div class="tags-lnks"><a href="?8">Все фабрики</a></div>
					 <div class="tags-lnks"><a href="?9">Все товары</a></div>
					 <div class="clearfix"></div>
					 <p></p>
					 <p class="footer-block">Самый большой выбор сладостей в Москве.</p>
				 </div>
				 <div class="col-sm-2">
					 <p class="footer-block">Сделано в <br/><strong><a href="bolshay.net">Большой Мастерской</a></strong></p>
					 <p class="footer-block">powered by <br/><strong>The Veer Layer.</strong></p>
				 </div>
				 </div>
			 </div>
	</footer>

	{{ empty($data['veer_message_center']) ? null : $data['veer_message_center'] }}
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ URL::asset('assets/'.$template.'/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/'.$template.'/js/holder.js') }}"></script>
	<script src="{{ URL::asset('assets/'.$template.'/js/script.js') }}"></script>
  </body>
</html>