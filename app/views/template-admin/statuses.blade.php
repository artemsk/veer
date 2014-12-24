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
	<li class="list-group-item bordered-row">
		<div class="row form-inline">
		<div class="col-sm-5">
			<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
			<div class="input-group">
				<span class="input-group-addon"><small>#{{ $item->id }}</small></span>
				<input type="text" class="form-control" name="InName" placeholder="Name" value="{{ $item->name }}">
			</div> 
		</div>
		<div class="form-group"><input type="color" class="form-control" name="InColor" placeholder="Color" value="{{ $item->color }}"></div>
		<div class="form-group"><input type="text" class="form-control" name="InOrder" placeholder="Order" value="{{ $item->manual_order }}"></div>
		<div class="form-group">
			<select class="form-control">
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
		</div>
	</div>	

	</li>
	@endforeach
	</ul>
	<button type="submit" class="btn btn-default">Update</button> 
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
		<div class="col-sm-5"><p><input type="text" class="form-control" name="InName[{{ $i }}]" placeholder="Name"></p></div>
		<div class="col-sm-2"><p><input type="color" class="form-control" name="InColor[{{ $i }}]" placeholder="Color"></p></div>
		<div class="col-sm-2"><p><input type="text" class="form-control" name="InOrder[{{ $i }}]" placeholder="Order"></p></div>
		<div class="col-sm-3"><p>
			<select class="form-control">
				<option value=""></option>
				<option value="flag_first">First Status</option>
				<option value="flag_unreg">Unregistered Order Status</option>
				<option value="flag_error">Error Status</option>
				<option value="flag_payment">Payment Type Status</option>
				<option value="flag_delivery">Delivery Type Status</option>
				<option value="flag_close">Close Status</option>
				<option value="secret">Secret Status (hidden from user)</option>
			</select>
			</p>
		</div>
	</div>	
	<p></p>
    @endfor
    <button type="submit" class="btn btn-default">Submit</button> 
	{{ Form::close() }}
</div>
@stop