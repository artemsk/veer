@extends($template.'.layout.base')

@section('body')
    <section class="slide-2">
        <div class="content container">
			<div class="row"><div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
<form method="POST" action="{{ route('user.login.post') }}" accept-charset="UTF-8">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
    
<label for="email">Email Address</label><br/>
<input class="form-control veer-form" name="email" type="email" value="" id="email" placeholder="Email"><br/><br/>

<label for="password">Password</label><br/>
<input class="form-control veer-form" name="password" type="password" value="" id="password"><br/><br/>

<input class="btn btn-default btn-lg" type="submit" value="Login">
</form>
				</div></div>
		</div>
	</section>
@stop