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
			
	@endforeach
	
	
</div>
@stop