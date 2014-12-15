@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
	<li><strong>Elements</strong></li>
	<li><a href="{{ route("admin.show", "images") }}">Images</a></li>	
	<li><a href="{{ route("admin.show", "attributes") }}">Attributes</a></li>	
	<li><a href="{{ route("admin.show", "tags") }}">Tags</a></li>
	<li><a href="{{ route("admin.show", "downloads") }}">Downloads</a></li>
	<li class="active">Comments</li>		
		
</ol>
<h1>Comments: {{ array_pull($items, 'counted') }}</h1>
<br/>
<div class="container">

	
		@foreach ($items as $key => $item) 
		<div class="row bordered-row">
			<div class="col-md-2">
				<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				&nbsp;<small>{{ $item->id }}</small>
				&nbsp;
				@if($item->users_id > 0)
				<span class="label label-info">u</span>
				<a href="{{ route('admin.show', array("users", "id" => empty($item->users_id) ? '' : $item->users_id)) }}">
				<strong>{{ $item->author }}</strong>
				</a>
				@else
				<strong>{{ $item->author }}</strong>
				@endif
			</div>	
			<div class="col-md-4">{{ $item->txt }}</div>
			<div class="col-md-1">
				@if($item->rate > 0)
				<span class="label label-success">
				<span class="glyphicon glyphicon-star" aria-hidden="true"></span> {{ $item->rate }}
				</span>
				@else
				<span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>
				@endif
			</div>
			<div class="col-md-1">
				@if($item->vote_y > 0)
				<span class="label label-success">
				<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> {{ $item->vote_y }}</span>&nbsp;
				@endif
				@if($item->vote_n > 0)
				<span class="label label-danger">
				<span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span> {{ $item->vote_n }}</span>
				@endif
			</div>
			<div class="col-md-1">
				@if($item->hidden > 0)
				<button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></button>
				@else
				<button type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				@endif
			</div>
			<div class="col-md-2">
				<small>
				@if($item->elements_type == "Veer\Models\Product")
					<a href="{{ route("admin.show", array("products", "id" => $item->elements->id)) }}">{{ $item->elements->title }}</a>
				@elseif($item->elements_type == "Veer\Models\Page")
					<a href="{{ route("admin.show", array("pages", "id" => $item->elements->id)) }}">{{ $item->elements->title }}</a>
				@else
					<span class="text-muted">#{{ $item->elements->id }} ?</span>
				@endif			
				</small>
			</div>
			<div class="col-md-1">
				<small>			
				{{ $item->created_at }}		
				</small>
			</div>
		</div>
		@endforeach
	
		
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
</div>
@stop