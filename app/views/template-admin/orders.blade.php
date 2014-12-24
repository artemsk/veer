@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li class="active">Orders</a></li>
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
			
	@endforeach
	
	
</div>
@stop