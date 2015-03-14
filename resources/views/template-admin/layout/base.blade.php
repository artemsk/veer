<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <title>{{ ucfirst(app('router')->current()->admin) }} — Manage & Configure — Veer.</title>
    
    <!-- Bootstrap -->
    <link href="{{ asset(config('veer.assets_path').'/'.$template.'/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset(config('veer.assets_path').'/'.$template.'/css/css.css') }}" rel="stylesheet">
    <link href="{{ asset(config('veer.assets_path').'/'.$template.'/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset(config('veer.assets_path').'/'.$template.'/css/fileinput.min.css') }}" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ asset(config('veer.assets_path').'/'.$template.'/css/datepicker3.css') }}" rel="stylesheet">
    <link href="{{ asset(config('veer.assets_path').'/'.$template.'/css/bootstrap-switch.min.css') }}" rel="stylesheet">

    <link href='http://fonts.googleapis.com/css?family=Roboto:400,900,700,300,300italic,900italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  </head>
  <body>

	  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		  <div class="container-fluid">
			  <div class="navbar-header">
				  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
					  <span class="sr-only">Toggle navigation</span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
				  </button>
                              <a class="navbar-brand" href="{{ route("admin.index") }}"><span class="logo-site">veer</span></a>
			  </div>
			  <!-- Collect the nav links, forms, and other content for toggling -->
			  <div class="collapse navbar-collapse" id="navbar-collapse-1">
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
							  <li class="divider"></li>
							  <li><a href="{{ route("admin.show", array("pages", "id" => "new")) }}">New page</a></li>
							  <li><a href="{{ route("admin.show", array("products", "id" => "new")) }}">New product</a></li>
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
							  <li><a href="{{ route("admin.show", "comments") }}">Comments <strong>{{ unread('comment') }}</strong></a></li>
							  <li><a href="{{ route("admin.show", "communications") }}">Communications <strong>{{ unread('communication') }}</strong></a></li>
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
				  <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
					<input name="_method" type="hidden" value="PUT">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					  <div class="form-group">
						  <input type="text" class="form-control navbar-search navbar-search-input" placeholder="[id|field]" data-toggle="tooltip" data-placement="bottom"
								 data-html="true" title="Searching current element" id="SearchField" name="SearchField">
					  </div>
					  <button type="submit" name="SearchButton" class="btn btn-default navbar-search" value="Search">Search</button>
				   </form>
			  </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
	  </nav>

	<div class="rowdelimiter"></div>
	
    @yield('body')

	<div class="rowdelimiter"></div>
	<hr>
	<footer>
		<div class="container-fluid footer">
			<div class="row">
				 <div class="col-sm-6">					 
					 <?php app('veer')->statistics(); // TODO: remove from here? ?>
					 <p>Veer :: version <strong>{{ $app['veer']->statistics['version'] }}</strong> <a href="#" data-toggle="collapse" data-target="#qlog" aria-expanded="true" aria-controls="qlog">queries <strong>{{ $app['veer']->statistics['queries'] }}</strong></a> loading time <strong>{{ $app['veer']->statistics['loading'] }}</strong> memory usage <strong>{{ $app['veer']->statistics['memory'] }}</strong></p>
				 </div>
				 <div class="col-sm-3 text-center">
					 <p class="footer-block">created by <strong><a href="http://bolshaya.net">bolshaya.net</a></strong></p>
				 </div>
				 <div class="col-sm-3 text-right">	 
					 <p class="footer-block">powered by <strong>Veer.</strong></p>
				 </div>
				 </div>
			 </div>
	</footer>

	<div id="qlog" class="collapse out">
		<pre>
		<?php dump(DB::getQueryLog()) ?>
		</pre>
	</div>
	<!-- TODO: replace with view/include ? -->
	@if(!empty(veer_get('veer_message_center')))
	<div class="events-veer-message-center">{{ head(veer_get('veer_message_center')) }}</div>
	@endif
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/holder.js') }}"></script>
	<script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/fileinput.min.js') }}"></script>
	<script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/bootstrap-datepicker.js') }}"></script>
	<script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/bootstrap-switch.min.js') }}"></script>
	<script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/html.sortable.min.js') }}"></script>	
	<script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/script.js') }}"></script>
	<script src="{{ asset(config('veer.assets_path').'/'.$template.'/js/delete-categories.js') }}"></script>
  </body>
</html>