@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li class="active">Lists</li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>		
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Lists <small>| lists:{{ array_pull($items, 'lists', 0) }} carts:{{ array_pull($items, 'basket', 0) }}</small></h1>
<br/>
<div class="container">
	@foreach(array_get($items, 'regrouped', array()) as $user => $itemGroup)
	<ul class="list-group">
		<li class="list-group-item list-group-item-info"><strong>
			@if($user > 0) {{ $items['users'][$user]->firstname }} {{ $items['users'][$user]->lastname }} 
			<a href="{{ route("admin.show", array("users", "id" => $items['users'][$user]->id)) }}">{{ $items['users'][$user]->email }}</a> 
			| <a href="tel:{{ $items['users'][$user]->phone }}">{{ $items['users'][$user]->phone }}</a> 
			@else Guest @endif</strong>
			@if($user > 0) 
			~ {{ $items[head($itemGroup)]->site->configuration->first()->conf_val or $items[head($itemGroup)]->site->url; }}
			@endif
		</li>
		@foreach($itemGroup as $item)

		<li class="list-group-item bordered-row">
			<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
			@if($items[$item]->name == "[basket]")
			<span class="label label-primary">cart</span>
			@else
			<span class="label label-success">{{ $items[$item]->name }}</span>
			@endif
			@if($items[$item]->elements_type == "Veer\Models\Product")
				{{ $items[$item]->quantity }} x 
				<a href="{{ route("admin.show", array("products", "id" => $items[$item]->elements_id)) }}">
					{{ $items[$item]->elements->title or '[?] Unknown' }}</a>
				@if(!empty($items[$item]->attributes))
				<span class="label label-info">+ attributes</span>
				@endif
			@elseif($items[$item]->elements_type == "Veer\Models\Page")
				<a href="{{ route("admin.show", array("pages", "id" => $items[$item]->elements_id)) }}">
					{{ $items[$item]->elements->title or '[?] Unknown' }}</a>
			@else
				<span class="text-muted">#{{ $items[$item]->elements_id }} ?</span>
			@endif
			@if($user <= 0)
			<small>~ {{ $items[$item]->site->configuration->first()->conf_val or $items[$item]->site->url; }}</small>
			@endif
		</li>
		
		@endforeach		
	</ul>		
	@endforeach
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>		
</div>
@stop