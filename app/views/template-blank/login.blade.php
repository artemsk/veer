@extends($template.'.layout.base')

@section('body')
    <section class="slide-2">
        <div class="content container">
			<div class="row"><div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
{{ Form::open(array('route' => 'user.login.post')) }}
    
{{ Form::label('email', 'E-Mail Address'); }}<br/>
{{ Form::email('email', '', array('class' => 'form-control veer-form')); }}<br/><br/>

{{ Form::label('password', 'Password'); }}<br/>
{{ Form::password('password', array('class' => 'form-control veer-form')); }}<br/><br/>

{{ Form::submit('Login', array('class' => 'btn btn-default btn-lg')); }}
{{ Form::close() }}
				</div></div>
		</div>
	</section>
@stop