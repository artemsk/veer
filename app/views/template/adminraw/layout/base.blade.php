<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{{ @$title_page }}}</title>

    <!-- Bootstrap -->
    <link href="{{ URL::asset('veer/template/'.$template.'/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('veer/template/'.$template.'/css/css.css') }}" rel="stylesheet">
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
      
  <div class="container">
      <div class="row">
          <div class="col-xs-12">
              <a href="{{ route('home') }}" class="logo-site">{{  array_get(Config::get('veer.site_config'),'SITE_TITLE') }}</a>
          </div>
      </div>
  </div>
      
  <div class="rowdelimiter"></div>          
          
    @yield('body')

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ URL::asset('veer/template/'.$template.'/js/bootstrap.min.js') }}"></script>
  </body>
</html>
<? echo number_format(memory_get_usage()); ?>
