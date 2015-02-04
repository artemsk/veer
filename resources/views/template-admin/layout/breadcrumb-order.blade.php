<ol class="breadcrumb">
	<li><strong>E-commerce</strong></li>
	@if((Input::get('filter',null) != null && $place == "orders") || $place == "order") 
	<li><strong><a href="{{ route("admin.show", "orders") }}">Orders</a></strong></li>
	@elseif($place == "orders")
	<li class="active">Orders</li>	
	@else
	<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
	@endif	
	@if(Input::get('filter',null) != null && $place == "bills")
	<li><strong><a href="{{ route("admin.show", "bills") }}">Bills</a></strong></li>
	@elseif($place == "bills")
	<li class="active">Bills</li>	
	@else
	<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
	@endif	
	@if(Input::get('filter',null) != null && $place == "discounts")
	<li><strong><a href="{{ route("admin.show", "discounts") }}">Discounts</a></strong></li>
	@elseif($place == "discounts")
	<li class="active">Discounts</li>	
	@else
	<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
	@endif	
	@if(Input::get('filter',null) != null && $place == "shipping")
	<li><strong><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></strong></li>
	@elseif($place == "shipping")
	<li class="active">Shipping methods</li>	
	@else
	<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>
	@endif	
	@if(Input::get('filter',null) != null && $place == "payment") 
	<li><strong><a href="{{ route("admin.show", "payment") }}">Payment methods</a></strong></li>
	@elseif($place == "payment")
	<li class="active">Payment methods</li>	
	@else
	<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>
	@endif	
	@if(Input::get('filter',null) != null && $place == "statuses") 
	<li><strong><a href="{{ route("admin.show", "statuses") }}">Statuses</a></strong></li>
	@elseif($place == "statuses")
	<li class="active">Statuses</li>	
	@else
	<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
	@endif
</ol>