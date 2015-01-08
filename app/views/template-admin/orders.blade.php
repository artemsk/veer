@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "orders") }}">Orders</a></strong></li>
		@else
		<li class="active">Orders</li>
		@endif		
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
	</ol> 
<h1>Orders :{{ array_get(app('veeradmin')->counted, 'active', 0) }}<small> @if(array_get(app('veeradmin')->counted, 'archived', 0) > 0)<a href="{{ route("admin.show", array("orders", "filter" => "archive", "filter_id" => true)) }}">~{{ array_get(app('veeradmin')->counted, 'archived', 0) }}</a>&nbsp;@endif 
	@if(Input::get('filter',null) != null) 
	filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong> | 
	@endif	
	sort by <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "created_at", "direction" => "desc")) }}">created</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "updated_at", "direction" => "desc")) }}">updated</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "status_id", "direction" => "asc")) }}">status</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "price", "direction" => "desc")) }}">price</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "delivery_plan", "direction" => "desc")) }}">shipping</a></small> <a class="btn btn-default" 
									   href="{{ route("admin.show", array("orders", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	
	@include($template.'.lists.orders', array('items' => $items))
	
	{{ Form::close() }}
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
					'sort' => Input::get('sort', null),
					'direction' => Input::get('direction', null)
				))->links() }}
		</div>
	</div>	
	
</div>
@stop