@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>			
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li class="active">Communications</li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Communications :{{ array_pull($items, 'counted', 0) }} <small>| emails | ims | etc.</small></h1>
<br/>
<div class="container">
	<ul class="list-group">
	@foreach($items as $key => $item)
		@if($key !== 'recipients')
		<div class="panel @if($item->intranet == true) panel-info @else panel-default @endif">
		<div class="panel-heading">
			<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
			&nbsp;
			@if($item->hidden > 0)
			<button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></button>
			@else
			<button type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
			@endif
			&nbsp;
			<small>#{{ $item->id }}</small>
			~ {{ $item->site->configuration->first()->conf_val or $item->site->url; }}
			<strong>:
			@if(!empty($item->url))
			{{ $item->url }}
			@endif
			
			@if($item->elements_type == "Veer\Models\Product")
				<a href="{{ route("admin.show", array("products", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@elseif($item->elements_type == "Veer\Models\Page")
				<a href="{{ route("admin.show", array("pages", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@elseif($item->elements_type == "Veer\Models\Category")
				<a href="{{ route("admin.show", array("categories", "category" => $item->elements_id)) }}">
				{{ $item->elements->title or '[?] Unknown' }}</a>	
			@else
			@endif
			</strong>
			@if($item->email_notify == true)
			&nbsp;
			<span class="label label-warning">
				<span class="glyphicon glyphicon-send text-danger" aria-hidden="true" title="Email notified"></span>
				&nbsp;Notified
			</span>
			@endif
			&nbsp;
			<span class="label @if($item->type == 'email') label-primary @else label-info @endif">{{ $item->type }}</span>
			&nbsp;
			<span class="badge" title="views">{{ $item->views }}</span>
			<span class="badge">{{ $item->created_at }}</span>
			@if($item->updated_at != $item->created_at)
			<span class="badge">{{ $item->updated_at }}</span>
			@endif
		</div>
		<div class="panel-body">
			<strong>
				@if(!empty($item->sender))
				{{ $item->sender }}
				@endif
				@if(!empty($item->sender_email))
				| {{ $item->sender_email }}
				@endif
				@if(!empty($item->sender_phone))
				| {{ $item->sender_phone }}
				@endif
				@if($item->users_id > 0)
				<a href="{{ route('admin.show', array("users", "id" => empty($item->users_id) ? '' : $item->users_id)) }}">
					{{ '@'.$item->user->username }}</a>
				@endif			
			</strong>{{ empty($item->theme)? null : ": ".$item->theme }}
		</div>
		<ul class="list-group">
			<li class="list-group-item">
				@if(isset($items['recipients'][$key])) 
					@foreach($items['recipients'][$key] as $r)
						@if($r == "all") <span class="badge">public</span> @else
					<a href="{{ route('admin.show', array("users", "id" => empty($r->id) ? '' : $r->id)) }}">
						{{ '@' }}{{ $r->username or '?' }}</a>
						@endif
					@endforeach
				@endif
				{{ $item->message }}	
			</li>
		</ul>
		</div>
		@endif
	@endforeach
	</ul>
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>	
	
</div>
@stop