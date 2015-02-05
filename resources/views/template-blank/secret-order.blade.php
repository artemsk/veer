@extends($template.'.layout.base')

@section('body')
<br/><br/>
<form method="POST" action="{{ route('order.store') }}" accept-charset="UTF-8">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
    
<label for="password">Secret Code</label><br/>
<input name="password" type="password" value="" id="password"><br/><br/>

<input type="submit" value="Show">
</form>
@stop