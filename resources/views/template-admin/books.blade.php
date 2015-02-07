@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-user', array('place' => 'books'))

<h1>Books 
	@if(Input::get('filter',null) != null) <small>
			filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
	</small>
	@endif
	:{{ $items->total() }}
	
	<small> | users addresses</small></h1>
<br/>
<div class="container">
	
	@include($template.'.lists.books', array('items' => $items))
	
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->render() }}
		</div>
	</div>	
	
	<div class='rowdelimiter'></div>
	<hr>
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	<label>Add users book</label>	
	@include($template.'.layout.form-userbook')
	</form>
</div>
@stop