@extends($template.'.layout.base')

@section('body')
<br/><br/>
{{ Form::open(array('route' => 'order.store')) }}
    
{{ Form::label('password', 'Secret Code'); }}<br/>
{{ Form::password('password'); }}<br/><br/>

{{ Form::submit('Show'); }}
{{ Form::close() }}
@stop