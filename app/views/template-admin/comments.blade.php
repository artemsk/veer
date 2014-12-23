@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>			
		<li class="active">Comments</li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Comments: {{ array_pull($items, 'counted') }}</h1>
<br/>
<div class="container">

	<div class="list-group">
		@foreach ($items as $key => $item) 
		<div class="list-group-item bordered-row">
			<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
			&nbsp;
			@if($item->hidden > 0)
			<button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></button>
			@else
			<button type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
			@endif
			&nbsp;
			<small>#{{ $item->id }}</small>
			&nbsp;
			@if($item->users_id > 0)
			<a href="{{ route('admin.show', array("users", "id" => empty($item->users_id) ? '' : $item->users_id)) }}">
			<strong>{{ '@'.$item->author }}</strong>
			</a>
			@else
			<strong>{{ $item->author }}</strong>
			@endif
			&nbsp;
			@if($item->rate > 0)
			<span class="label label-success">
			<span class="glyphicon glyphicon-star" aria-hidden="true"></span> {{ $item->rate }}
			</span>
			@else
			<span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>
			@endif
			@if($item->vote_y > 0)
			&nbsp;
			<span class="label label-success">
			<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> {{ $item->vote_y }}</span>&nbsp;
			@endif
			@if($item->vote_n > 0)
			&nbsp;
			<span class="label label-danger">
			<span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span> {{ $item->vote_n }}</span>
			@endif
			&nbsp;
			<small>
			@if($item->elements_type == "Veer\Models\Product")
				<a href="{{ route("admin.show", array("products", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@elseif($item->elements_type == "Veer\Models\Page")
				<a href="{{ route("admin.show", array("pages", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@else
				<span class="text-muted">#{{ $item->elements_id }} Empty</span>
			@endif	
			&nbsp;
			{{ $item->created_at }}	
			</small>
			<p></p>
			<p>{{ $item->txt }}</p>
		</div>
		@endforeach
	</div>
		
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
</div>
@stop