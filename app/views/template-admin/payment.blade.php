@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li class="active">Payment methods</li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>	
</ol>
<h1>Payment methods</h1>
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
					<input type="text" class="form-control" name="InSite" placeholder="Sites ID" value="{{ $item->sites_id }}">
					<small>@if(is_object($item->site))~ {{ $item->site->configuration->first()->conf_val or $item->site->url; }} @endif</small>
				</div>
				<div class="form-group"><strong>
					<input type="text" class="form-control" name="InName" placeholder="Payment Method Name" value="{{ $item->name }}">
					</strong>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="InType" placeholder="Type (offline, online)"
						   value="{{ $item->type }}">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="InPayment" placeholder="Paying Time (now, later, upon-receive)"
						   value="{{ $item->paying_time }}">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="InPrice" placeholder="Commission (percent)"
						   value="{{ $item->commission }}">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<input type="text" class="form-control" name="InDiscount" placeholder="Discount (Percent)" value="{{ $item->discount_price  }}%">
				</div>
				<div class="checkbox">
					<input type="checkbox" name="OnDiscountEnable" data-on-text="On" date-off-text="Off"
						   @if($item->discount_enable == true) checked @endif class="page-checkboxes"> &nbsp;Discount
				</div>
				<div class="form-group">
					<label>Discount Conditions</label>
					<textarea class="form-control" name="InDiscountConditions" rows="5" placeholder="p:?
w:?
l:?
d:total">{{ $item->discount_conditions }}</textarea>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group"><strong>
					<input type="text" class="form-control" name="InFunc" 
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
					<span class="label label-info"><a href="{{ route("admin.show", array("orders", "filter" => "payment", "filter_id" => $item->id)) }}">{{ count($item->orders) }} orders</a></span>
					<span class="label label-info"><a href="{{ route("admin.show", array("bills", "filter" => "payment", "filter_id" => $item->id)) }}">{{ count($item->orders) }} bills</a></span>
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
	<label>Add new payment method</label>
	<div class="row">
        <div class="col-md-4">
			<div class="checkbox">
				<input type="checkbox" name="OnEnable" checked class="page-checkboxes">
			</div>			
			<div class="form-group">
                <input type="text" class="form-control" name="InSite" placeholder="Sites ID">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="InName" placeholder="Payment Method Name">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="InType" placeholder="Type (offline, online)">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="InPayment" placeholder="Paying Time (now, later, upon-receive)">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="InCommission" placeholder="Commission (percent)">
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
d:total"></textarea>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<input type="text" class="form-control has-warning" name="InFunc" placeholder="Class | function in ../Ecommerce/">
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