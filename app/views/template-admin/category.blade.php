@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
</ol>
<h1>Category</h1>
<br/>
<div class="container">
	<div class="row">
	<div class="col-md-8">
		<ol class="breadcrumb">
			<li>{{ link_to_route("admin.show", empty($items->site_title) ? "Categories" : $items->site_title, array("categories","#site".$items->sites_id)) }}</li>
			@if (count($items->parentcategories)<=0) 
			<li><button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Parent category" 
						data-content='
						<div class="form-inline">
						<input type="text" class="form-control" placeholder="Id" size=2>
						<button class="btn btn-info" type="button"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
						</div>
						' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>
			</li>
			@endif
			@foreach ($items->parentcategories as $category)
			<li><button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Replace parent category" 
						data-content='
						<div class="form-inline">
						<input type="text" class="form-control" placeholder="Id" size=2 value="{{ $category->id }}">
						<button class="btn btn-info" type="button"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
						<button class="btn btn-warning" type="button"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
						</div>
						' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>&nbsp;
				{{ link_to_route("admin.show", $category->title, array("categories", "category=".$category->id)) }}
			</li>
			@endforeach
		</ol>

		<div class="row">
			<div class="col-sm-9">
				<h2><input type="text" class="form-control admin-form" placeholder="Title" value="{{ $items->title }}"></h2>
			</div>
			<div class="col-sm-3 text-right">
				<h2>
				<span class="badge">{{ count($items->subcategories) }} sub</span>
				<span class="badge">{{ $items->views }} views</span></h2>
			</div>	
		</div>
		<input type="text" class="form-control admin-form" placeholder="Remote Url if exists" value="{{ $items->remote_url }}">
		<textarea class="form-control" placeholder="Description">{{ $items->description }}</textarea>
		<p><div class="text-right"><button type="button" class="btn btn-default">Update</button> 
			&nbsp;<button type="button" class="btn btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></div></p>
    <br/>		
		
	<ul class="list-group">
	@foreach ($items->subcategories as $category)	
	<li class="list-group-item">
		<span class="badge">{{ $category->views }}</span>
		<button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Replace parent category" data-content='
						<div class="form-inline">
						<input type="text" class="form-control" placeholder="Id" size=2 value="{{ $items->id }}">
						<button class="btn btn-info" type="button"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
						<button class="btn btn-warning" type="button"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
						</div>
						' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>&nbsp;
		<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
		<a href="{{ app('url')->current() }}?category={{ $category->id }}">{{ $category->title }}</a> 
		<small>{{ $category->remote_url }}
			<span class="additional-info">{{ $category->products()->count() }} <i class="fa fa-gift"></i>, {{ $category->pages()->count() }} <span class="glyphicon glyphicon-file" aria-hidden="true"></span></span> 
		</small>
		</li>	
	@endforeach
	<li class="list-group-item">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Title or :Existing-category-ID">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button">Add</button>
			</span>
		</div>
	</li>
	</ul>	
	</div>
	</div>

	@if(count($items->images)>0)
	<div class="rowdelimiter"></div>
	<h3>Images</h3>
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.images', array('items' => $items->images))
		</div>
	</div>	
	@endif
	
	@if(count($items->products)>0)
	<div class="rowdelimiter"></div>
	<h3>Products</h3>
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.products', array('items' => $items->products))
		</div>
	</div>
	@endif
	
	@if(count($items->pages)>0)
	<div class="rowdelimiter"></div>
	<h3>Pages</h3>
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.pages', array('pages' => $items->pages))
		</div>
	</div>
	@endif
	
	@if(count($items->communications)>0)
	<div class="rowdelimiter"></div>
	<h3>Communications</h3>
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.communications', array('communications' => $items->communications))
		</div>
	</div>
	@endif
	
</div>

@stop