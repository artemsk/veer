<ol class="breadcrumb">
	<li><strong>Structure</strong></li>
	@if($place == "sites")
	<li class="active">Sites</li>
	@else
	<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
	@endif
	@if($place == "categories" && null != veer_get('filtered') || $place == "category")
	<li><strong><a href="{{ route("admin.show", "categories") }}">Categories</a></strong></li>
	@elseif($place == "categories")
	<li class="active">Categories</li>	
	@else
	<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
	@endif	
	@if($place == "pages" && null != veer_get('filtered') || $place == "page")
	<li><strong><a href="{{ route("admin.show", "pages") }}">Pages</a></strong></li>
	@elseif($place == "pages")
	<li class="active">Pages</li>
	@else
	<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
	@endif
	@if($place == "products" && null != veer_get('filtered') || $place == "product")
	<li><strong><a href="{{ route("admin.show", "products") }}">Products</a></strong></li>
	@elseif($place == "products")
	<li class="active">Products</li>
	@else
	<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
	@endif
</ol>