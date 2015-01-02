@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-user', array('place' => 'books'))

<h1>Books 
	@if(Input::get('filter',null) != null) <small>
			filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
	</small>
			{{ array_pull($items, 'counted', 0) ? '' : '' }}
	@else
	:{{ array_pull($items, 'counted', 0) }}
	@endif
	<small> | users addresses</small></h1>
<br/>
<div class="container">
	
	@include($template.'.lists.books', array('items' => $items))
	
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->links() }}
		</div>
	</div>	
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add users book</label>	
	@include($template.'.layout.form-userbook')
	{{ Form::close() }}
</div>
@stop