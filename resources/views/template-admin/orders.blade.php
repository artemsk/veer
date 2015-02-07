@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-order', array('place' => 'orders'))
	
<h1>Orders :{{ $items->total() }}<small> @if(array_get($data, 'counted.archived', 0) > 0)<a href="{{ route("admin.show", array("orders", "filter" => "archive", "filter_id" => true)) }}">~{{ array_get($data, 'counted.archived', 0) }}</a>&nbsp;@endif 
	@if(Input::get('filter',null) != null) 
	filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong> | 
	@endif	
	sort by <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "created_at", "direction" => "desc")) }}">created</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "updated_at", "direction" => "desc")) }}">updated</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "status_id", "direction" => "asc")) }}">status</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "price", "direction" => "desc")) }}">price</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "delivery_plan", "direction" => "desc")) }}">shipping</a></small> <a class="btn btn-default" 
									   href="{{ route("admin.show", array("orders", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">

	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	
	@include($template.'.lists.orders', array('items' => $items))
	
	</form>
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
					'sort' => Input::get('sort', null),
					'direction' => Input::get('direction', null)
				))->render() }}
		</div>
	</div>	
	
</div>
@stop