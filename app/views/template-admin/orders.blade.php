@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "orders") }}">Orders</a></strong></li>
		@else
		<li class="active">Orders</li>
		@endif		
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
	</ol> 
<h1>Orders <small>
	@if(Input::get('filter',null) != null) 
	filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong> | 
	@endif	
	sort by <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "created_at", "direction" => "desc")) }}">created</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "updated_at", "direction" => "desc")) }}">updated</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "status_id", "direction" => "asc")) }}">status</a> | <a href="{{ route("admin.show", array("orders", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "price", "direction" => "desc")) }}">price</a></small> <a class="btn btn-default" 
									   href="{{ route("admin.show", array("orders", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		@foreach($items as $key => $item)
		<div class="panel panel-default @if($item->pin == true) pinned @else not-pinned @endif">
			<div class="panel-heading" role="tab" data-toggle="collapse" data-target="#collapse{{ $key }}" data-parent="#accordion">
				<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp

				<small>#{{ $item->id }}</small>
				<a href="{{ route("admin.show", array("orders", "id" => $item->id)) }}"><span class="label label-yellow label-bg">
					ID {{ app('veershop')->getOrderId($item->cluster, $item->cluster_oid) }}</span></a>
				
				@if(is_object($item->site))
				<small>~ <a href="{{ route("admin.show", array("orders", "filter" => "site", "filter_id" => $item->sites_id)) }}">
						{{ $item->site->configuration->first()->conf_val or $item->site->url; }}</a></small>
				@endif

				&nbsp;
				<strong><a href="{{ route("admin.show", array("orders", "filter" => "type", "filter_id" => $item->type)) }}">
						{{ $item->type }}</a></strong>
				&nbsp;
				@if(is_object($item->status))<span class="label" style="background-color: {{ $item->status->color }}">
					<a href="{{ route("admin.show", array("orders", "filter" => "status", "filter_id" => $item->status->id)) }}">
						<strong>{{ $item->status->name or null }}</strong>
					</a></span>&nbsp; 
				@endif
					
				@if(!empty($item->user_type))
				<span class="label label-info">{{ $item->user_type or '' }}</span>
				@endif
				
				@if($item->userdiscount_id > 0 && is_object($item->userdiscount))
				<span class="label label-info">
					<a href="{{ route("admin.show", array("discounts", "filter" => "user", "filter_id" => $item->users_id)) }}">discount</a></span>
				@endif

				@if($item->free == true) 
				<span class="label label-primary">
					<a href="{{ route("admin.show", array("orders", "filter" => "free", "filter_id" => true)) }}">FREE</a></span> 
				@endif

				@if($item->delivery_free == true) 
				<span class="label label-primary">
					<a href="{{ route("admin.show", array("orders", "filter" => "delivery_free", "filter_id" => true)) }}">free shipping</a></span> 
				@endif

				@if($item->delivery_hold == true) 
				<span class="label label-danger">
					<a href="{{ route("admin.show", array("orders", "filter" => "delivery_hold", "filter_id" => true)) }}">hold shipping</a></span> 
				@endif

				@if($item->payment_hold == true) 
				<span class="label label-danger">
					<a href="{{ route("admin.show", array("orders", "filter" => "payment_hold", "filter_id" => true)) }}">hold payment</a></span> 
				@endif

				@if($item->payment_done == true) 
					<span class="label label-success">
						<a href="{{ route("admin.show", array("orders", "filter" => "payment_done", "filter_id" => true)) }}">paid</a></span> 
				@endif
				
				@if($item->close == true) 
				<span class="label label-success"><a href="{{ route("admin.show", array("orders", "filter" => "close", "filter_id" => true)) }}">close</a> 
					{{ \Carbon\Carbon::parse($item->close_time)->format('Y-m-d'); }}</span> @endif

				&nbsp; <small>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</small>
				
				@if($item->hidden == true) 
				&nbsp;<span class="label label-default">
					<a href="{{ route("admin.show", array("orders", "filter" => "hidden", "filter_id" => true)) }}">hidden</a></span>
				@endif
				
				@if($item->archive == true) 
				<span class="label label-default">archive</span>
				@endif
				
				<div class="pull-right">{{ app('veershop')->priceFormat($item->price) }}</div>
			</div>
			<div class="panel-collapse collapse @if($key == 0) in @else out @endif" id="collapse{{ $key }}">
				<div class="panel-body">
					Payer: <strong>{{ $item->name }}</strong>, <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>, <a 
						href="tel:{{ $item->phone }}">{{ $item->phone }}</a> |
					@if($item->users_id > 0 && is_object($item->user))
					<a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ "@".$item->user->username }}</a>
					@elseif($item->users_id > 0) 
					<del><a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">#{{ $item->users_id }}</a></del>
					@else
					@endif
				</div>
				<ul class="list-group">
					<li class="list-group-item">
						Shipping method: <strong>
						<a href="{{ route("admin.show", array("orders", "filter" => "delivery", "filter_id" => $item->delivery_method_id)) }}">
							{{ $item->delivery_method }}</a></strong><br/>
						Payment method: <strong>
						<a href="{{ route("admin.show", array("orders", "filter" => "payment", "filter_id" => $item->payment_method_id)) }}">
							{{ $item->payment_method }}</a></strong>
					</li>
					<li class="list-group-item">
						Shipping address: <strong>{{ $item->country }},	{{ $item->city }}, {{ $item->address }}</strong>
						@if(is_object($item->userbook))
						~ <a href="{{ route("admin.show", array("orders", "filter" => "userbook", "filter_id" => $item->userbook_id)) }}">
							all orders to address</a>
						@endif

						<div class="xs-rowdelimiter"></div>

						Shipping plan: 
						<span class="label label-default">
							{{ \Carbon\Carbon::parse($item->delivery_plan)->format("Y-m-d H:i") }}</span> 
						<small>~ real: </small><span class="label label-success">
							{{ \Carbon\Carbon::parse($item->delivery_real)->format("Y-m-d H:i") }}</span>	
					</li>
					<li class="list-group-item">
						Content price: <strong>{{ app('veershop')->priceFormat($item->content_price) }}</strong>
						@if($item->used_discount > 0) <small> ~ {{ app('veershop')->priceFormat($item->used_discount) }} discount</small>
						@endif
						<br/>
						Shipping price: <strong>{{ app('veershop')->priceFormat($item->delivery_price) }}</strong>
					</li>
					@if(is_object($item->bills) && count($item->bills) > 0)
					<li class="list-group-item">
							@foreach($item->bills as $bill)
								<a href="{{ route("admin.show", array("bills", "filter" => "order", "filter_id" => $item->id)) }}">Bill</a> #{{$bill->id}} {{$bill->payment_method}} {{ $bill->link }} 
								{{ $bill->price }} {{ $bill->status->name }} {{ $bill->sent }} {{ $bill->viewed }} {{ $bill->paid }} {{ $bill->canceled }}<br/>
							@endforeach	
					</li>
					@endif
					<li class="list-group-item">
						@if($item->scores > 0) Score: {{ $item->scores }} @endif
						<h3>{{ app('veershop')->priceFormat($item->price) }}</h3>
					</li>
				</ul>	
			</div>
		</div>
		@endforeach
	</div>
	
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
	
</div>
@stop