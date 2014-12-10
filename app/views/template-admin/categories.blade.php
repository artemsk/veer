@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		@if(!empty($items['filtered'])) 
		<li><a href="{{ route("admin.show", "categories") }}"><strong>Categories</strong></a></li>
		@else
		<li class="active">Categories</li>
		@endif
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
</ol>
<h1>Categories 
@if(!empty($items['filtered'])) 
: filtered by {{ $items['filtered'] }} <a href="{{ route("admin.show", array(array_pull($items, 'filtered'))) }}">
	#{{ array_pull($items, 'filtered_id') }}</a>
@endif
</h1>
<br/>
<div class="container">
	<div class="col-md-8">
	@foreach ($items as $item)
	
	<h2 id="site{{ $item->id }}">{{ $item->configuration()->where('conf_key','=','SITE_TITLE')->pluck('conf_val'); }} <small>{{ $item->url }}
		&nbsp;:{{ count($item->categories) }}</small></h2>

	<ul class="list-group">
	@foreach ($item->categories as $category)	
	<li class="list-group-item">
		<span class="badge">{{ $category->views }}</span>
		<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
		<a href="{{ app('url')->current() }}?category={{ $category->id }}">{{ $category->title }}</a> 
		<small>{{ $category->remote_url }}
			<span class="additional-info">{{ $category->products()->count() }} <i class="fa fa-gift"></i>, {{ $category->pages()->count() }} <span class="glyphicon glyphicon-file" aria-hidden="true"></span></span> 
		</small>
	</li>	
	@endforeach
	<li class="list-group-item">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Title">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button">Add</button>
			</span>
		</div>
	</li>
	</ul>
	<div class="rowdelimiter"></div>
	
	@endforeach
	
	</div>
</div>
@stop