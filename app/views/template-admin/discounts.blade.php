@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-order', array('place' => 'discounts'))

<h1>Discounts <small>
		@if(Input::get('filter',null) != null) 
			filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
		@endif
		| <a href="{{ route("admin.show", array("discounts", "filter" => "status", "filter_id" => "wait")) }}">wait</a> <a href="{{ route("admin.show", array("discounts", "filter" => "status", "filter_id" => "active")) }}">active</a> <a href="{{ route("admin.show", array("discounts", "filter" => "status", "filter_id" => "expired")) }}">expired</a> <a href="{{ route("admin.show", array("discounts", "filter" => "status", "filter_id" => "canceled")) }}">canceled</a></small></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<div class="row">
	@foreach($items as $key => $item)
	@if(round($key/4) == ($key/4)) <div class="clearfix"></div> @endif	
		<div class="col-lg-3 col-md-3 col-sm-6 text-center">
		<button type="submit" name="deleteDiscount" value="{{ $item->id }}" class="btn btn-danger btn-xs btn-block"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
		<p class="xs-rowdelimiter"></p>
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
		<span class="label label-primary"><a href="{{ route("admin.show", array("orders", "filter" => "userdiscount", "filter_id" => $item->id)) }}">{{ count($item->orders) }} orders</a></span>
		@endif
		<p class="xs-rowdelimiter"></p>
		<div class="input-group">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			</span>
			<input type="text" class="form-control" value="{{ $item->users_id }}" name="discount[{{ $item->id }}][fill][users_id]" placeholder="Users Id">
		</div>	
		@if(is_object($item->user))
		<small><a href="{{ route('admin.show', array('users', 'id' => $item->user->id)) }}">{{ '@'.$item->user->username }}</a></small>
		@endif			
		<p class="xs-rowdelimiter"></p>
		<div class="input-group">
			<span class="input-group-addon">
			  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
			</span>
			<input type="text" class="form-control" value="{{ $item->sites_id }}" name="discount[{{ $item->id }}][fill][sites_id]" placeholder="Sites Id">
		</div>
		<small>@if(is_object($item->site))~ {{ $item->site->configuration->first()->conf_val or $item->site->url; }} @endif</small>
		<p class="xs-rowdelimiter"></p>
		<strong>
			<input type="text" class="form-control input-lg" value="{{ $item->secret_code }}" name="discount[{{ $item->id }}][fill][secret_code]" placeholder="Secret code">
		</strong>
		<small>secret code</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value="{{ $item->discount }}%" name="discount[{{ $item->id }}][fill][discount]" placeholder="Discount">
		<p class="xs-rowdelimiter"></p>
		<strong>
			<select class="form-control" name="discount[{{ $item->id }}][fill][status]">
				<option value="{{ $item->status }}">{{ $item->status }}</option>
				<option value="wait">Wait</option>
				<option value="active">Active</option>					
				<option value="expired">Expired</option>
				<option value="canceled">Canceled</option>
			</select>
		</strong>
		<small>current status</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control date-container" name="discount[{{ $item->id }}][fill][expiration_day]" value="{{ $item->expiration_day }}">
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value="{{ $item->expiration_times }}" name="discount[{{ $item->id }}][fill][expiration_times]" placeholder="Times">
		<p class="xs-rowdelimiter"></p>
		<input type="checkbox" name="discount[{{ $item->id }}][fill][expires]" data-on-text="Expires" value="1" 
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
			<input type="text" class="form-control" placeholder="Users Id (optional)" name="discount[new][fill][users_id]"
				   @if(Input::get('filter',null) == "user" && Input::has('filter_id')) value="{{ Input::get("filter_id") }}" @endif>
		</div>	
		<p class="xs-rowdelimiter"></p>
		<div class="input-group">
			<span class="input-group-addon">
			  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
			</span>
			<input type="text" class="form-control" value="" placeholder="Sites Id" name="discount[new][fill][sites_id]">
		</div>
		<small>~</small>
		<p class="xs-rowdelimiter"></p>
		<strong>
			<input type="text" class="form-control input-lg" value="{{ str_random(18) }}" name="discount[new][fill][secret_code]" placeholder="Secret code">
		</strong>
		<small>secret code</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value="" name="discount[new][fill][discount]" placeholder="Discount (%)">
		<p class="xs-rowdelimiter"></p>
		<strong>
			<select class="form-control" name="discount[new][fill][status]">
					<option value="wait">Wait (initial)</option>
					<option value="active">Active</option>					
					<option value="expired">Expired</option>
					<option value="canceled">Canceled</option>
			</select>
		</strong>
		<small>current status</small>
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control date-container" value="" name="discount[new][fill][expiration_day]" placeholder="Expiration Day">
		<p class="xs-rowdelimiter"></p>
		<input type="text" class="form-control" value="" name="discount[new][fill][expiration_times]" placeholder="Times">
		<p class="xs-rowdelimiter"></p>
		<input type="checkbox" name="discount[new][fill][expires]" value="1" data-on-text="Expires" 
			   data-off-text="Permanent&nbsp;" class="page-checkboxes">
		</div>
	</div>
	
			<p class="sm-rowdelimiter"></p>
		<button type="submit" name="updateGlobalDiscounts" value="update" class="btn btn-default">Update</button>
	{{ Form::close() }}
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->links() }}
		</div>
	</div>
</div>
@stop