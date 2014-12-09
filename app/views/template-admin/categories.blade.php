@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li class="active">Categories</li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
</ol>
<h1>Categories</h1>
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
		<small>{{ $category->remote_url }}</small></li>	
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
	
	@endforeach
	
	</div>
</div>
@stop