@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li class="active">Statuses</li>
</ol>
<h1>Statuses</h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<ul class="list-group">
	@foreach($items as $item)
		<div class="row">
		<div class="col-md-12">
			<div class="input-group dynamic-input-group">
				<span class="input-group-btn dynamic-input-group-btn">
					<button type="button" class="btn btn-danger" >
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
						<small>#{{ $item->id }}</small></button>
				</span>
				<input type="text" class="form-control limited-size-input-50 dynamic-input-group-input" name="InName" placeholder="Name" value="{{ $item->name }}">
				<input type="color" class="form-control limited-size-input-10 dynamic-input-group-input" name="InColor" 
					   placeholder="Color" value="{{ $item->color }}">
				<input type="text" class="form-control limited-size-input-10 dynamic-input-group-input" name="InOrder" placeholder="Order" value="{{ $item->manual_order }}">
				<select class="form-control limited-size-input-30 dynamic-input-group-input">
					@if($item->flag_first == true) <option value="flag_first">First Status</option> @endif
					@if($item->flag_unreg == true) <option value="flag_unreg">Unregistered Order Status</option> @endif
					@if($item->flag_error == true) <option value="flag_error">Error Status</option> @endif
					@if($item->flag_payment == true) <option value="flag_payment">Payment Type Status</option> @endif
					@if($item->flag_delivery == true) <option value="flag_delivery">Delivery Type Status</option> @endif
					@if($item->flag_close == true) <option value="flag_close">Close Status</option> @endif
					@if($item->secret == true) <option value="secret">Secret Status (hidden from user)</option> @endif
					<option value=""></option>
					<option value="flag_first">First Status</option>
					<option value="flag_unreg">Unregistered Order Status</option>
					<option value="flag_error">Error Status</option>
					<option value="flag_payment">Payment Type Status</option>
					<option value="flag_delivery">Delivery Type Status</option>
					<option value="flag_close">Close Status</option>
					<option value="secret">Secret Status (hidden from user)</option>
				</select>
				<span class="input-group-btn dynamic-input-group-btn">
					<button type="submit" class="btn btn-default">Update</button> 
				</span>
			</div>
		</div>
	</div>	
	<div class="xs-rowdelimiter"></div>
	<span class="label label-info">{{ $item->orders->count() }} orders</span> 
	<span class="label label-info">{{ $item->orders_with_history->count() }} in orders history</span>
	<span class="label label-info">{{ $item->bills->count() }} bills</span>
	<div class="sm-rowdelimiter"></div>
	@endforeach
	</ul>
	{{ Form::close() }}
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add statuses</label>
	@for ($i = 0; $i < 5; $i++)    
	<div class="row">
		<div class="col-md-12">
			<div class="input-group dynamic-input-group">
				<span class="input-group-addon dynamic-input-group-addon">
					#
				</span>
				<input type="text" class="form-control dynamic-input-group-input limited-size-input-50" name="InName[{{ $i }}]" placeholder="Name">
				<input type="color" class="form-control dynamic-input-group-input limited-size-input-10 " name="InColor[{{ $i }}]" placeholder="Color">
				<input type="text" class="form-control dynamic-input-group-input limited-size-input-10 " name="InOrder[{{ $i }}]" placeholder="Order">

				<select class="form-control dynamic-input-group-input limited-size-input-30">
					<option value=""></option>
					<option value="flag_first">First Status</option>
					<option value="flag_unreg">Unregistered Order Status</option>
					<option value="flag_error">Error Status</option>
					<option value="flag_payment">Payment Type Status</option>
					<option value="flag_delivery">Delivery Type Status</option>
					<option value="flag_close">Close Status</option>
					<option value="secret">Secret Status (hidden from user)</option>
				</select>
				<span class="input-group-addon dynamic-input-group-addon">
				</span>
			</div>
		</div>	
	</div>
	<p></p>
    @endfor
    <button type="submit" class="btn btn-default">Submit</button> 
	{{ Form::close() }}
</div>
@stop