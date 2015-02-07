<ol class="breadcrumb">
	<li><strong>Users</strong></li>
	@if((Input::get('filter',null) != null && $place == "users") || $place == "user") 
	<li><strong><a href="{{ route("admin.show", "users") }}">Users</a></strong></li>
	@elseif($place == "users")
	<li class="active">Users</li>
	@else
	<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
	@endif
	@if((Input::get('filter',null) != null && $place == "books")) 
	<li><strong><a href="{{ route("admin.show", "books") }}">Books</a></strong></li>
	@elseif($place == "books")
	<li class="active">Books</li>
	@else
	<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
	@endif
	@if((Input::get('filter',null) != null && $place == "lists")) 
	<li><strong><a href="{{ route("admin.show", "lists") }}">Lists</a></strong></li>
	@elseif($place == "lists")
	<li class="active">Lists</li>
	@else
	<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
	@endif
	@if((Input::get('filter',null) != null && $place == "searches")) 
	<li><strong><a href="{{ route("admin.show", "searches") }}">Searches</a></strong></li>
	@elseif($place == "searches")
	<li class="active">Searches</li>
	@else
	<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>	
	@endif	
	@if((Input::get('filter',null) != null && $place == "comments")) 
	<li><strong><a href="{{ route("admin.show", "comments") }}">Comments</a></strong></li>
	@elseif($place == "comments")
	<li class="active">Comments</li>
	@else
	<li><a href="{{ route("admin.show", "comments") }}">Comments</a>
		<span class="badge">{{ unread('comment') }}</span>
	</li>
	@endif
	@if((Input::get('filter',null) != null && $place == "communications")) 
	<li><strong><a href="{{ route("admin.show", "communications") }}">Communications</a></strong></li>
	@elseif($place == "communications")
	<li class="active">Communications</li>
	@else
	<li><a href="{{ route("admin.show", "communications") }}">Communications</a>
		<span class="badge">{{ unread('communication') }}</span>
	</li>
	@endif	
	@if((Input::get('filter',null) != null && $place == "roles")) 
	<li><strong><a href="{{ route("admin.show", "roles") }}">Roles</a></strong></li>
	@elseif($place == "roles")
	<li class="active">Roles</li>
	@else
	<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	@endif
</ol>