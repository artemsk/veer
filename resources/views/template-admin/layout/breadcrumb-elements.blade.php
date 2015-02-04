<ol class="breadcrumb">
	<li><strong>Elements</strong></li>
	@if(Input::get('filter',null) != null && $place == "images") 
	<li><strong><a href="{{ route("admin.show", "images") }}">Images</a></strong></li>
	@elseif($place == "images")
	<li class="active">Images</li>	
	@else
	<li><a href="{{ route("admin.show", "images") }}">Images</a></li>
	@endif		
	@if($place == "attributes")
	<li class="active">Attributes</li>	
	@else
	<li><a href="{{ route("admin.show", "attributes") }}">Attributes</a></li>	
	@endif
	@if($place == "tags")
	<li class="active">Tags</li>	
	@else
	<li><a href="{{ route("admin.show", "tags") }}">Tags</a></li>
	@endif	
	@if($place == "downloads")
	<li class="active">Downloads</li>	
	@else
	<li><a href="{{ route("admin.show", "downloads") }}">Downloads</a></li>	
	@endif
	<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
</ol>