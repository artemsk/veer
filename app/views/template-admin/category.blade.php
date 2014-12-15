@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}"><strong>Categories</strong></a></li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
</ol>
<h1>Category</h1>
<br/>
<div class="container">
	<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li>{{ link_to_route("admin.show", empty($items->site_title) ? "Categories" : $items->site_title, array("categories","#site".$items->sites_id)) }}</li>
			@if (count($items->parentcategories)<=0) 
			<li><button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Parent category" 
						data-content='
						<div class="form-inline">
						{{ Form::open(array('url' => URL::full(), 'method' => 'put')); }}
						<input type="text" class="form-control" placeholder="Id" size=2 name="parentId">
						<button class="btn btn-info" type="submit" name="action" value="saveParent"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
						{{ Form::close() }}
						</div>
						' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>
			</li>
			@endif
			@foreach ($items->parentcategories as $category)
			<li><button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Replace parent category" 
						data-content='
						<div class="form-inline">
						{{ Form::open(array('url' => URL::full(), 'method' => 'put')); }}
						<input type="text" class="form-control" placeholder="Id" size=2 name="parentId" value="{{ $category->id }}">
						<button class="btn btn-info" type="submit" name="action" value="updateParent">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
						<button class="btn btn-warning" type="submit" name="action" value="removeParent">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
						<input type="hidden" name="lastCategoryId" value="{{ $category->id }}">
						{{ Form::close() }}
						</div>
						' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>&nbsp;
				{{ link_to_route("admin.show", $category->title, array("categories", "category=".$category->id)) }}
			</li>
			@endforeach
		</ol>

		{{ Form::open(array('url' => URL::full(), 'method' => 'put')); }}
		<div class="row">
			<div class="col-sm-9">
				<h2><input type="text" class="form-control admin-form" placeholder="Title" name="title" value="{{ $items->title }}"></h2>
			</div>
			<div class="col-sm-3 text-right">
				<h2>
				<span class="badge">{{ count($items->subcategories) }} sub</span>
				<span class="badge">{{ $items->views }} views</span></h2>
			</div>	
		</div>
		<input type="url" class="form-control admin-form" placeholder="Remote Url if exists" name="remoteUrl" value="{{ $items->remote_url }}">
		<textarea class="form-control" placeholder="Description" name="description">{{ $items->description }}</textarea>
		<p><div class="text-right"><button type="submit" class="btn btn-default" name="action" value="updateCurrent">Update</button> 
			&nbsp;<button type="submit" class="btn btn-danger" name="action" value="deleteCurrent">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></div></p>
		{{ Form::close() }}
    <br/>		
	<div class="testajax"></div>	
	<ul class="list-group sortable" data-parentid="{{ $items->id }}">
	@foreach ($items->subcategories as $category)	
	<li class="list-group-item category-item-{{ $category->id }}">
		<span class="badge">{{ $category->views }}</span>
		<button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Replace parent category" data-content='
						<div class="form-inline">
						{{ Form::open(array('url' => URL::full(), 'method' => 'put')); }}
						<input type="text" class="form-control" placeholder="Id" size=2 name="parentId" value="{{ $items->id }}">
						<button class="btn btn-info" type="submit" name="action" value="updateInChild">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
						<button class="btn btn-warning" type="submit" name="action" value="removeInChild">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
						<input type="hidden" name="lastCategoryId" value="{{ $items->id }}">
						<input type="hidden" name="currentChildId" value="{{ $category->id }}">
						{{ Form::close() }}
						</div>
						' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>&nbsp;
		<button type="button" class="btn btn-danger btn-xs category-delete" data-categoryid="{{ $category->id }}">
			<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
		<a href="{{ app('url')->current() }}?category={{ $category->id }}">{{ $category->title }}</a> 
		<small>{{ $category->remote_url }}
			<span class="additional-info">{{ count($category->subcategories) }} <i class="fa fa-bookmark-o"></i>, {{ count($category->products) }} <i class="fa fa-gift"></i>, {{ count($category->pages) }} <span class="glyphicon glyphicon-file" aria-hidden="true"></span></span> 
		</small>
		</li>	
	@endforeach
	{{ Form::open(array('url' => URL::full(), 'method' => 'put')); }}
	<li class="list-group-item">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Title or :Existing-category-ID" name="child">
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit" name="action" value="addChild">Add</button>
			</span>
		</div>
	</li>
	{{ Form::close() }}
	
	</ul>	
	</div>
	</div>

	{{ Form::open(array('url' => URL::full(), 'files' => true, 'method' => 'put')); }}
	<div class="rowdelimiter"></div>
	<h3>Images</h3>
	<div class="row">
		<div class="col-md-5">
			<p><input class="input-files-enhance" type="file" id="InFile1" name="uploadImage" multiple=false></p>
		</div>
		<div class="col-md-5">
			<p><input class="form-control" name="attachImages" placeholder=":Existing Images IDs[,]"></p>
		</div>	
		<div class="col-md-2">
			<p><button class="btn btn-default btn-block" type="submit" name="action" value="updateImages">Upload | Update</button></p>
		</div>
	</div>	
	<p></p>
	@if(count($items->images)>0)	
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.images', array('items' => $items->images, 'denyDelete' => true))
		</div>
	</div>	
	@endif
	{{ Form::close() }}
	
	{{ Form::open(array('url' => URL::full(), 'files' => false, 'method' => 'put')); }}	
	<div class="rowdelimiter"></div>
	<h3>Products</h3>
	<div class="row">
		<div class="col-sm-3 col-md-2">
			<a class="btn btn-default btn-block" href="{{ route("admin.show", array("products", 
						"id" => "new", "category" => $items->id)) }}" role="button">New product</a>
		</div>
		<div class="col-sm-9 col-md-10">
			<div class="input-group">
				<input type="text" class="form-control" name="attachProducs" placeholder=":Existing IDs">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit" name="action" value="updateProducts">Add</button>
				</span>
			</div>			
		</div>		
	</div>
	<p></p>
	@if(count($items->products)>0)	
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.products', array('items' => $items->products, 'denyDelete' => true))
		</div>
	</div>
	@endif
	{{ Form::close() }}
	
	{{ Form::open(array('url' => URL::full(), 'files' => false, 'method' => 'put')); }}	
	<div class="rowdelimiter"></div>
	<h3>Pages</h3>
	<div class="row">
		<div class="col-sm-3 col-md-2">
			<a class="btn btn-default btn-block" href="{{ route("admin.show", array("pages", 
				"id" => "new", "category" => $items->id)) }}" role="button">New page</a>
		</div>
		<div class="col-sm-9 col-md-10">
			<div class="input-group">
				<input type="text" class="form-control" name="attachPages" placeholder=":Existing IDs">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit" name="action" value="updatePages">Add</button>
				</span>
			</div>			
		</div>		
	</div>
	<p></p>	
	@if(count($items->pages)>0)	
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.pages', array('items' => $items->pages, 'denyDelete' => true))
		</div>
	</div>
	@endif
	{{ Form::close() }}
	
	@if(count($items->communications)>0)
	<div class="rowdelimiter"></div>
	<h3>Communications</h3>
	<div class="row">
		<div class="col-md-12">
			@include($template.'.lists.communications', array('items' => $items->communications))
		</div>
	</div>
	@endif
	
</div>

@stop