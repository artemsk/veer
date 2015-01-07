@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "bills") }}">Bills</a></strong></li>
		@else
		<li class="active">Bills</li>
		@endif		
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
</ol>
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
				))->links() }}
		</div>
	</div>
	{{ Form::close() }}
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add new bill</label>
	<div class="row">
        <div class="col-md-4">
			<div class="checkbox">
				<input type="text" class="form-control" name="billCreate[fill][orders_id]" placeholder="Orders ID">
			</div>			
			<div class="form-group">
				<select class="form-control" name="billCreate[fill][status_id]">
					<option value="{{ !isset(statuses("payment")->first()->id) ? 0 : statuses("payment")->first()->id }}">{{ !isset(statuses("payment")->first()->name) ? '[?]error' : statuses("payment")->first()->name }}</option>
                @foreach(statuses() as $status)
					<option value="{{ $status->id }}">{{ $status->name }}</option>
				@endforeach
				</select>
			</div>
			<div class="form-group">
                <select class="form-control" name="billCreate[fill][payment_method_id]">
					<option value="0">[manual]</option>
                @foreach(payments() as $payment)
					<option value="{{ $payment->id }}">{{ $payment->name }}</option>
				@endforeach
				</select>
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="billCreate[fill][payment_method]" placeholder="Payment Method Name [manual]">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="billCreate[fill][link]" placeholder="Link" value="{{ str_random(18) }}">
				<small>link</small>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
                <input type="text" class="form-control" name="billCreate[fill][price]" placeholder="Price">
			</div>
			<div class="form-group">
				<select class="form-control" name="billCreate[template]">
				@if(!empty(app('veeradmin')->billsTypes))
                @foreach(app('veeradmin')->billsTypes as $templ)
					<option value="{{ $templ }}">{{ $templ }}</option>
				@endforeach
				@endif
					<option value="">[empty]</option>
				</select>
			</div>
			<div class="checkbox">
				<input type="checkbox" name="billCreate[fill][sendTo]" value="1" data-on-text="On" date-off-text="Off" class="page-checkboxes"> &nbsp;Send to user
			</div>
			<button type="submit" class="btn btn-default" name="addNewBill" value="New">Submit</button> 
		</div>
	</div>
	
	{{ Form::close() }}	
</div>
@stop