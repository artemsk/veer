@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li class="active">Orders</li>
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
	</ol> 
<h1>Orders <small>sort by created | updated | status | price</small> <a class="btn btn-default" 
									   href="{{ route("admin.show", array("orders", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	@foreach($items as $item)
	
		{{ $item->id }}<br/>
		{{ $item->hash }}<br/>
		{{ $item->sites_id }}<br/>
		{{ $item->cluster }}<br/>
		{{ $item->cluster_oid }}<br/>
		{{ $item->type }}<br/>
		{{ $item->users_id }}<br/>
		{{ $item->user_type }}<br/>
		{{ $item->name }}<br/>
		{{ $item->email }}<br/>
		{{ $item->phone }}<br/>
		{{ $item->delivery_method }}<br/>
		{{ $item->delivery_method_id }}<br/>
		{{ $item->userbook_id }}<br/>
		{{ $item->country }}<br/>
		{{ $item->city }}<br/>
		{{ $item->address }}<br/>
		{{ $item->weight }}<br/>
		{{ $item->delivery_plan }}<br/>
		{{ $item->delivery_real }}<br/>
		{{ $item->delivery_hold }}<br/>
		{{ $item->delivery_price }}<br/>
		{{ $item->delivery_free }}<br/>
		{{ $item->content_price }}<br/>
		{{ $item->price }}<br/>
		{{ $item->used_discount }}<br/>
		{{ $item->userdiscount_id }}<br/>
		{{ $item->free }}<br/>
		{{ $item->payment_method }}<br/>
		{{ $item->payment_method_id }}<br/>
		{{ $item->payment_hold }}<br/>
		{{ $item->payment_done }}<br/>
		{{ $item->status_id }}<br/>
		{{ $item->close }}<br/>
		{{ $item->close_time }}<br/>
		{{ $item->scores }}<br/>
		{{ $item->hidden }}<br/>
		{{ $item->pin }}<br/>
		{{ $item->archive }}<br/>
		{{ $item->created_at }}<br/>
		{{ $item->updated_at }}<br/>
		{{ $item->deleted_at }}<br/>

			
	@endforeach
	
	
</div>
@stop