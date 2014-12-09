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

	  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		  <div class="container-fluid">
			  <!-- Brand and toggle get grouped for better mobile display -->
			  <div class="navbar-header">
				  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					  <span class="sr-only">Toggle navigation</span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
				  </button>
				  <a class="navbar-brand" href="#"><strong>The Veer</strong></a>
			  </div>
			  <!-- Collect the nav links, forms, and other content for toggling -->
			  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				  <ul class="nav navbar-nav">
					  <li class="dropdown">
						  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Structure 
							  <span class="caret"></span></a>
						  <ul class="dropdown-menu" role="menu">
							  <li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
							  <li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
							  <li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
							  <li><a href="{{ route("admin.show", "products") }}">Products</a></li>
							  <li class="divider"></li>
							  <li><a href="{{ route("admin.show", "images") }}">Images</a></li>
							  <li><a href="{{ route("admin.show", "attributes") }}">Attributes</a></li>
							  <li><a href="{{ route("admin.show", "tags") }}">Tags</a></li>
							  <li><a href="{{ route("admin.show", "downloads") }}">Downloads</a></li>
							  <li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>
						  </ul>
					  </li>
					  <li class="dropdown">
						  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Users 
							  <span class="caret"></span></a>
						  <ul class="dropdown-menu" role="menu">
							  <li><a href="{{ route("admin.show", "users") }}">Users</a></li>
							  <li><a href="{{ route("admin.show", "books") }}">Books</a></li>
							  <li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
							  <li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>
							  <li class="divider"></li>
							  <li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>
							  <li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
							  <li class="divider"></li>
							  <li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
						  </ul>
					  </li>
					  <li class="dropdown">
						  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">E-commerce 
							  <span class="caret"></span></a>
						  <ul class="dropdown-menu" role="menu">
							  <li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
							  <li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
							  <li class="divider"></li>
							  <li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
							  <li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>
							  <li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>
							  <li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
						  </ul>
					  </li>
					  <li class="dropdown">
						  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Settings
							  <span class="caret"></span></a>
						  <ul class="dropdown-menu" role="menu">
							  <li><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
							  <li><a href="{{ route("admin.show", "components") }}">Components</a></li>
							  <li><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
							  <li><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>
							  <li class="divider"></li>
							  <li><a href="{{ route("admin.show", "etc") }}">etc.</a></li>
						  </ul>
					  </li>					  
				  </ul>
			  </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
	  </nav>

	<div class="rowdelimiter"></div>
	 
	<!-- 
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="#">Users</a></li>
		<li><a href="#">Books</a></li>
		<li><a href="#">Lists</a></li>
		<li><a href="#">Searches</a></li>		
		<li><a href="#">Comments</a></li>	
		<li><a href="#">Communications</a></li>
		<li><a href="#">Roles</a></li>
	</ol> 
	<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="#">Orders</a></li>
		<li><a href="#">Bills</a></li>
		<li><a href="#">Discounts</a></li>
		<li><a href="#">Shipping methods</a></li>		
		<li><a href="#">Payment methods</a></li>	
		<li><a href="#">Statuses</a></li>
	</ol> 	
	<ol class="breadcrumb">
		<li><strong>Settings</strong></li>
		<li><a href="#">Configuration</a></li>
		<li><a href="#">Components</a></li>
		<li><a href="#">Secrets</a></li>
		<li><a href="#">Jobs</a></li>		
		<li><a href="#">etc.</a></li>	
	</ol>
	-->
	
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
					 <p>Veer {{ $app['veer']->statistics['version'] }} (q. {{ $app['veer']->statistics['queries'] }} l. {{ $app['veer']->statistics['loading'] }} m. {{ $app['veer']->statistics['memory'] }})</p>
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
<?php echo "<pre>";
print_r(DB::getQueryLog());
echo "</pre>"; ?>