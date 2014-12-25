@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		<li class="active">Bills</li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
</ol>
<h1>Bills <small>sort by created | name</small></h1>
<br/>
<div class="container">
	@foreach($items as $item)
	
		{{ $item->id }}<br/>
		{{ $item->orders_id }}<br/>
		{{ $item->users_id }}<br/>
		{{ $item->status_id }}<br/>
		{{ $item->payment_method }}<br/>
		{{ $item->payment_method_id }}<br/>
		{{ $item->link }}<br/>
		{{ $item->content }}<br/>
		{{ $item->price }}<br/>
		{{ $item->sent }}<br/>
		{{ $item->viewed }}<br/>
		{{ $item->paid }}<br/>
		{{ $item->canceled }}<br/>
		{{ $item->created_at }}<br/>
		{{ $item->updated_at }}<br/>
		
	@endforeach
	
	
</div>
@stop