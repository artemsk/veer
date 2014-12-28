@foreach($items as $item)
	<ul class="list-group">
		<li class="list-group-item bordered-row">
		<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp
		#{{ $item->id }}
		@if(is_object($item->payment))
		<strong><a href="{{ route("admin.show", array("bills", "filter" => "payment", "filter_id" => $item->payment->id)) }}">
				{{ $item->payment_method }}</a></strong>
		@else
		<del>&nbsp;{{ $item->payment_method }}&nbsp;</del>
		@endif
		&nbsp;
		@if(is_object($item->status)) <span class="label" style="background-color: {{ $item->status->color }}">
			<a href="{{ route("admin.show", array("bills", "filter" => "status", "filter_id" => $item->status->id)) }}">
			<strong>{{ $item->status->name or null }}</strong>
			</a></span>@endif
		&nbsp;
		@if($item->sent == true) <span class="label label-info"><a href="{{ route("admin.show", array("bills", "filter" => "sent", "filter_id" => 1)) }}">sent</a></span> @else <span class="label label-default"><a href="{{ route("admin.show", array("bills", "filter" => "sent", "filter_id" => 0)) }}">waiting</a></span> @endif
		@if($item->viewed == true) <span class="label label-primary"><a href="{{ route("admin.show", array("bills", "filter" => "viewed", "filter_id" => 1)) }}">viewed</a></span> @else <span class="label label-default"><a href="{{ route("admin.show", array("bills", "filter" => "viewed", "filter_id" => 0)) }}">not seen</a></span> @endif
		@if($item->paid == true) <span class="label label-success"><a href="{{ route("admin.show", array("bills", "filter" => "paid", "filter_id" => 1)) }}">paid</a></span> @else <span class="label label-default"><a href="{{ route("admin.show", array("bills", "filter" => "paid", "filter_id" => 0)) }}">unpaid</a></span> @endif
		@if($item->canceled == true) <span class="label label-danger"><a href="{{ route("admin.show", array("bills", "filter" => "canceled", "filter_id" => 1)) }}">canceled</a></span> @endif
		<span class="badge">{{ $item->created_at }}</span>
		@if($item->updated_at != $item->created_at)
		<span class="badge">{{ $item->updated_at }}</span>
		@endif
		</li>
		<li class="list-group-item">
		Order: @if(is_object($item->order)) 
		<a href="{{ route("admin.show", array("orders", "id" => $item->orders_id)) }}">
			#{{ app('veershop')->getOrderId($item->order->cluster, $item->order->cluster_oid) }}</a>
			<br/>Payer: <strong>{{ $item->order->name }}</strong>, <a href="mailto:{{ $item->order->email }}">{{ $item->order->email }}</a>, <a 
				href="tel:{{ $item->order->phone }}">{{ $item->order->phone }}</a> |
		@else <del><a href="{{ route("admin.show", array("orders", "id" => $item->orders_id)) }}">#{{ $item->orders_id }}</a></del> @endif
		@if($item->users_id > 0 && is_object($item->user))
		<a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ "@".$item->user->username }}</a>
		@endif
		<br/>
		Bill link: <a href="{{ route("order.bills", array($item->id, $item->link)) }}" target="_blank">{{ $item->link }}</a>
		<br/>
		<h3>{{ app('veershop')->priceFormat($item->price) }}</h3>
		{{-- Currency used when creating bill --}}
		</li>
	</ul>	
	@endforeach