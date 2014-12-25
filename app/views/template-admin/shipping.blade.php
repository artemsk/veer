@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li class="shipping">Shipping methods</li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
</ol>
<h1>Shipping methods</h1>
<br/>
<div class="container">
	
	@foreach($items as $item)
	<h3>#{{ $item->id }}</h3>
	<ul class="list-group">
		<div class="row list-group-item">
			<div class="col-md-4">
				<div class="checkbox">
					<input type="checkbox" name="OnEnable" @if($item->enable == true) checked @endif class="page-checkboxes">
				</div>			
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
						  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
						</span>
					<input type="text" class="form-control" name="InSite" placeholder="Sites ID" value="{{ $item->sites_id }}">
					</div>
					<small>@if(is_object($item->site))~ {{ $item->site->configuration->first()->conf_val or $item->site->url; }} @endif</small>
				</div>
				<div class="form-group"><strong>
					<input type="text" class="form-control input-lg" name="InName" placeholder="Shipping Method Name" value="{{ $item->name }}">
					</strong>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="InDelivery" placeholder="Shipping Type (delivery, pickup, no-delivery etc.)"
						   value="{{ $item->delivery_type }}">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="InPayment" placeholder="Payment Type (fix, calculator, free)"
						   value="{{ $item->payment_type }}">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="InPrice" placeholder="Price (if fix | if failed calculation)"
						   value="{{ $item->price }}">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<input type="text" class="form-control" name="InDiscount" placeholder="Discount (Percent)" value="{{ $item->discount_price  }}%">
				</div>
				<div class="checkbox">
					<input type="checkbox" name="OnDiscountEnable" data-on-text="On" data-off-text="Off" class="page-checkboxes"
						   @if($item->discount_enable == true) checked @endif > &nbsp;Discount
				</div>
				<div class="form-group">
					<label>Discount Conditions</label>
					<textarea class="form-control" name="InDiscountConditions" rows="5" placeholder="p:?
w:?
l:?
d:total|delivery">{{ $item->discount_conditions }}</textarea>
				</div>
				<div class="form-group">
					<textarea class="form-control" name="InAddress" rows="3" 
							  placeholder="Address (if it's pickup and known addresses)">{{ $item->address }}</textarea>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group"><strong>
					<input type="text" class="form-control input-lg" name="InFunc" 
						   placeholder="Class | function in ../Ecommerce/" value="{{ $item->func_name }}">
					</strong>
					@if(!class_exists('\\Veer\\Ecommerce\\' . $item->func_name)) 
					<span class='label label-danger'>Class doesn't exists</span>
					@endif
				</div>
				<div class="form-group">
					<textarea class="form-control" name="InOther" rows="2" 
							  placeholder="Other options (used in functions)">{{ $item->other_options }}</textarea>
				</div>
				<hr>
				<div class="form-group">
					<label>Manual Order</label>
					<input type="text" class="form-control" name="InOrder" placeholder="Manual Order" value="{{ $item->manual_order }}">
				</div>
				<button type="submit" class="btn btn-info">Update #{{ $item->id }}</button>&nbsp; 
				<button type="button" class="btn btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				<p></p>
				<small>
					{{ $item->created_at }}<br/>
					{{ $item->updated_at }}<br/>
					<span class="label label-info"><a href="{{ route("admin.show", array("orders", "filter" => "shipping", "filter_id" => $item->id)) }}">{{ count($item->orders) }} orders</a></span>
				</small>
			</div> 
		</div>
	</ul>	
	@endforeach
	
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add new shipping method</label>
	<div class="row">
        <div class="col-md-4">
			<div class="checkbox">
				<input type="checkbox" name="OnEnable" checked class="page-checkboxes">
			</div>			
			<div class="form-group">
                <input type="text" class="form-control" name="InSite" placeholder="Sites ID">
			</div>
			<div class="form-group">
                <input type="text" class="form-control input-lg" name="InName" placeholder="Shipping Method Name">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="InDelivery" placeholder="Shipping Type (delivery, pickup, no-delivery etc.)">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="InPayment" placeholder="Payment Type (fix, calculator, free)">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="InPrice" placeholder="Price (if fix | if failed calculation)">
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
                <input type="text" class="form-control" name="InDiscount" placeholder="Discount (Percent)">
			</div>
			<div class="checkbox">
				<input type="checkbox" name="OnDiscountEnable" data-on-text="On" date-off-text="Off" class="page-checkboxes"> &nbsp;Discount
			</div>
			<div class="form-group">
				<label>Discount Conditions</label>
				<textarea class="form-control" name="InDiscountConditions" rows="5" placeholder="p:?
w:?
l:?
d:total|delivery"></textarea>
			</div>
			<div class="form-group">
				<textarea class="form-control" name="InAddress" rows="3" placeholder="Address (if it's pickup and known addresses)"></textarea>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="InFunc" placeholder="Class | function in ../Ecommerce/">
			</div>
			<div class="form-group">
				<textarea class="form-control" name="InOther" rows="2" placeholder="Other options (used in functions)"></textarea>
			</div>
			<hr>
			<div class="form-group">
                <input type="text" class="form-control" name="InOrder" placeholder="Manual Order">
			</div>
			<button type="submit" class="btn btn-default">Submit</button> 
        </div> 
	</div>
	
	{{ Form::close() }}
	
</div>
@stop