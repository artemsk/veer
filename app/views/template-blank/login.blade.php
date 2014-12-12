{{ Form::open(array('route' => 'user.login.post')) }}
    
{{ Form::label('email', 'E-Mail Address'); }}<br/>
{{ Form::email('email'); }}<br/><br/>

{{ Form::label('password', 'Password'); }}<br/>
{{ Form::password('password'); }}<br/><br/>

{{ Form::submit('Login'); }}
{{ Form::close() }}