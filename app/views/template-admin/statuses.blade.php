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
	@foreach($items as $item)
	
		{{ $item->id }}<br/>
			
	@endforeach
	
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
		<div class="col-sm-3"><input type="text" class="form-control" name="InName[{{ $i }}]" placeholder="Name"></div>
		<div class="col-sm-3"><input type="color" class="form-control" name="InColor[{{ $i }}]" placeholder="Color"></div>
		<div class="col-sm-3"><input type="text" class="form-control" name="InOrder[{{ $i }}]" placeholder="Order"></div>
		<div class="col-sm-3">
			<select class="form-control">
				<option value=""></option>
				<option value="first">First Status</option>
				<option value="unreg">Unregistered Order Status</option>
				<option value="error">Error Status</option>
				<option value="payment">Payment Type Status</option>
				<option value="delivery">Delivery Type Status</option>
				<option value="close">Close Status</option>
				<option value="secret">Secret Status (hidden from user)</option>
			</select>
		</div>
	</div>	
	<p></p>
    @endfor
    <button type="submit" class="btn btn-default">Submit</button> 
	{{ Form::close() }}
</div>
@stop