@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-order', array('place' => 'bills'))

<h1>Bills <small>
		@if(Input::get('filter',null) != null) 
			filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong> | 
		@endif
		sort by <a href="{{ route("admin.show", array("bills", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "created_at", "direction" => "desc")) }}">created</a> | <a href="{{ route("admin.show", array("bills", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "updated_at", "direction" => "desc")) }}">updated</a> | <a href="{{ route("admin.show", array("bills", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "price", "direction" => "desc")) }}">price</a> | <a href="{{ route("admin.show", array("bills", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "payment_method", "direction" => "asc")) }}">type</a></small></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}

	@include($template.'.lists.bills', array('items' => $items))
	
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
	</form>
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add new bill</label>
	
	@include($template.'.layout.form-bill')
	
	</form>
</div>
@stop