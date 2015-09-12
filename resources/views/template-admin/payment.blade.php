@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-order', array('place' => 'payment'))

<h1>Payment methods @if(Input::get('filter',null) != null) 
		<small>filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong></small>
		@endif</h1>
<br/>
<div class="container">
	@foreach($items as $item)
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	<h3>#{{ $item->id }}</h3>
	<ul class="list-group">
		<div class="row list-group-item">
			<div class="col-md-4">
				<div class="checkbox">
					<input type="checkbox" name="payment[fill][enable]" @if($item->enable == true) checked @endif 
						   value="1" class="page-checkboxes">
				</div>			
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
						  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
						</span>
					<input type="text" class="form-control" name="payment[fill][sites_id]" placeholder="Sites ID" value="{{ $item->sites_id }}">
					</div>
					<small>@if(is_object($item->site))~ {{ $item->site->configuration->first()->conf_val or $item->site->url; }} @endif</small>
				</div>
				<div class="form-group"><strong>
					<input type="text" class="form-control input-lg" name="payment[fill][name]" placeholder="Payment Method Name" value="{{ $item->name }}">
					</strong>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="payment[fill][type]" placeholder="Type (offline, online)"
						   value="{{ $item->type }}">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="payment[fill][paying_time]" placeholder="Paying Time (now, later, upon-receive)"
						   value="{{ $item->paying_time }}">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="payment[fill][commission]" placeholder="Commission (percent)"
						   value="{{ $item->commission }}%">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<input type="text" class="form-control" name="payment[fill][discount_price]" placeholder="Discount (Percent)" value="{{ $item->discount_price  }}%">
				</div>
				<div class="checkbox">
					<input type="checkbox" name="payment[fill][discount_enable]" value="1" data-on-text="On" data-off-text="Off"
						   @if($item->discount_enable == true) checked @endif class="page-checkboxes"> &nbsp;Discount
				</div>
				<div class="form-group">
					<label>Discount Conditions</label>
					<textarea class="form-control" name="payment[fill][discount_conditions]" rows="5" placeholder="p:?
w:?
l:?
d:total">{{ $item->discount_conditions }}</textarea>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group"><strong>
					<input type="text" class="form-control input-lg" name="payment[fill][func_name]" 
						   placeholder="Class | function in ../Ecommerce/ or vendor/" value="{{ $item->func_name }}">
					</strong>
					@if(!empty($item->func_name) && !class_exists(
                        starts_with($item->func_name, "\\") ? $item->func_name : "\\Veer\\Components\\Ecommerce\\" . $item->func_name
                    )) 
					<span class='label label-danger'>Class doesn't exists</span>
					@endif
				</div>
				<div class="form-group">
					<textarea class="form-control" name="payment[fill][other_options]" rows="2" 
							  placeholder="Other options (used in functions)">{{ $item->other_options }}</textarea>
				</div>
				<hr>
				<div class="form-group">
					<label>Manual Order</label>
					<input type="text" class="form-control" name="payment[fill][manual_order]" placeholder="Manual Order" value="{{ $item->manual_order }}">
				</div>
				<button type="submit" class="btn btn-info" name="updatePaymentMethod" value="{{ $item->id }}">Update #{{ $item->id }}</button>&nbsp; 
				<button type="submit" class="btn btn-danger" name="deletePaymentMethod" value="{{ $item->id }}"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
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
	</form>
	@endforeach
	
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->render() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	<label>Add new payment method</label>
	<div class="row">
        <div class="col-md-4">
			<div class="checkbox">
				<input type="checkbox" name="payment[fill][enable]" checked class="page-checkboxes" value="1">
			</div>			
			<div class="form-group">
                <input type="text" class="form-control" name="payment[fill][sites_id]" placeholder="Sites ID">
			</div>
			<div class="form-group">
                <input type="text" class="form-control input-lg" name="payment[fill][name]" placeholder="Payment Method Name">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="payment[fill][type]" placeholder="Type (offline, online)">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="payment[fill][paying_time]" placeholder="Paying Time (now, later, upon-receive)">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="payment[fill][commission]" placeholder="Commission (percent)">
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
                <input type="text" class="form-control" name="payment[fill][discount_price]" placeholder="Discount (Percent)">
			</div>
			<div class="checkbox">
				<input type="checkbox" name="payment[fill][discount_enable]" value="1" data-on-text="On" date-off-text="Off" class="page-checkboxes"> &nbsp;Discount
			</div>
			<div class="form-group">
				<label>Discount Conditions</label>
				<textarea class="form-control" name="payment[fill][discount_conditions]" rows="5" placeholder="p:?
w:?
l:?
d:total"></textarea>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="payment[fill][func_name]" placeholder="Class | function in ../Ecommerce/ or vendor/">
			</div>
			<div class="form-group">
				<textarea class="form-control" name="payment[fill][other_options]" rows="2" placeholder="Other options (used in functions)"></textarea>
			</div>
			<hr>
			<div class="form-group">
                <input type="text" class="form-control" name="payment[fill][manual_order]" placeholder="Manual Order">
			</div>
			<button type="submit" class="btn btn-default" name="addPaymentMethod" value="New">Submit</button> 
        </div> 
	</div>
	
	</form>
</div>
@stop