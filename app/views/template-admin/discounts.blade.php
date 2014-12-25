@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li class="active">Discounts</li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
</ol>
<h1>Discounts</h1>
<br/>
<div class="container">
	<div class="row">
	@foreach($items as $key => $item)
	@if(round($key/4) == ($key/4)) <div class="clearfix"></div> @endif	
		<div class="col-lg-3 col-md-3 col-sm-6 text-center">
		<small>#{{ $item->id }}</small>
		@if($item->status == "wait")
		<span class="label label-info">waiting</span>
		@elseif($item->status == "active")
		<span class="label label-success">in use</span>
		@elseif($item->status == "expired")
		<span class="label label-warning">expired</span>
		@else
		<span class="label label-default">{{ $item->status }}</span>
		@endif
		@if(count($item->orders) > 0)
		<span class="label label-primary"><a href="{{ route("admin.show", array("orders", "filter" => "discounts", "filter_id" => $item->id)) }}">{{ count($item->orders) }} orders</a></span>
		@endif
		<p class="xs-rowdelimiter"></p>
		<div class="input-group">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			</span>
			<input type="text" class="form-control" value="{{ $item->users_id }}" placeholder="Users Id">
		</div>	
		@if(is_object($item->user))
		<small><a href="{{ route('admin.show', array('users', 'id' => $item->user->id)) }}">{{ '@'.$item->user->username }}</a></small>
		@endif			
		<p class="xs-rowdelimiter"></p>
		<div class="input-group">
			<span class="input-group-addon">
			  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
			</span>
			<input type="text" class="form-control" value="{{ $item->sites_id }}" placeholder="Sites Id">
		</div>
		<small>@if(is_object($item->site))~ {{ $item->site->configuration->first()->conf_val or $item->site->url; }} @endif</small>
		<p class="xs-rowdelimiter"></p>
		<strong>
			<input type="text" class="form-control input-lg" value="{{ $item->secret_code }}"  placeholder="Secret code">
		</strong>
		<small>secret code</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value="{{ $item->discount }}%"  placeholder="Discount">
		<p class="xs-rowdelimiter"></p>
		<strong>
			<select class="form-control">
				<option value="{{ $item->status }}">{{ $item->status }}</option>
				<option value="wait">Wait</option>
				<option value="active">Active</option>					
				<option value="expired">Expired</option>
				<option value="canceled">Canceled</option>
			</select>
		</strong>
		<small>current status</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control date-container" value="{{ $item->expiration_day }}">
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value="{{ $item->expiration_times }}" placeholder="Times">
		<p class="xs-rowdelimiter"></p>
		<input type="checkbox" name="OnDiscountExp" data-on-text="Expires" 
			   data-off-text="Permanent&nbsp;" class="page-checkboxes" @if($item->expires == true) checked @endif>
		<p class="xs-rowdelimiter"></p>
		<small>
		{{ $item->created_at }}
		@if($item->updated_at != $item->created_at)
			<br/>{{ $item->updated_at }}
		@endif
		</small>
		</div>		
	@endforeach
	
		<div class="col-lg-3 col-md-3 col-sm-6 text-center">
		<small>NEW</small>
		<p class="xs-rowdelimiter"></p>
		<div class="input-group">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			</span>
			<input type="text" class="form-control" value="" placeholder="Users Id (optional)">
		</div>	
		<p class="xs-rowdelimiter"></p>
		<div class="input-group">
			<span class="input-group-addon">
			  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
			</span>
			<input type="text" class="form-control" value="" placeholder="Sites Id">
		</div>
		<small>~</small>
		<p class="xs-rowdelimiter"></p>
		<strong>
			<input type="text" class="form-control input-lg" value="{{ str_random(18) }}"  placeholder="Secret code">
		</strong>
		<small>secret code</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value=""  placeholder="Discount (%)">
		<p class="xs-rowdelimiter"></p>
		<strong>
			<select class="form-control">
					<option value="wait">Wait (initial)</option>
					<option value="active">Active</option>					
					<option value="expired">Expired</option>
					<option value="canceled">Canceled</option>
			</select>
		</strong>
		<small>current status</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control date-container" value="" placeholder="Expiration Day">
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value="" placeholder="Times">
		<p class="xs-rowdelimiter"></p>
		<input type="checkbox" name="OnDiscountExp" data-on-text="Expires" 
			   data-off-text="Permanent&nbsp;" class="page-checkboxes">
		</div>
	</div>
	
			<p class="sm-rowdelimiter"></p>
		<button type="submit" class="btn btn-default">Update</button>
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
	
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	{{ Form::close() }}
</div>
@stop