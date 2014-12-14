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

	<div class="categories-list-{{ $item->id}} ">			
			@include($template.'.lists.categories-category', array('categories' => $item->categories, 'siteid' => $item->id))				
	</div>
	{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'category-add', 'data-siteid' => $item->id)); }}	
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Title" name="newcategory">
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit" name="add2site" value="{{ $item->id }}">Add</button>
			</span>
		</div>
		<input type="hidden" name="siteid" value="{{ $item->id }}">
	{{ Form::close() }}
	
	<div class="rowdelimiter"></div>
	
	@endforeach
	
	</div>
</div>
@stop