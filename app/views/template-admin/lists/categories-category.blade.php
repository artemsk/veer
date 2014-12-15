<ul class="list-group categories-group sortable" id="sortable{{ $siteid }}" data-parentid="{{ $siteid }}">	
@foreach ($categories as $category)	
<li class="list-group-item category-item-{{ $category->id }}">
	<span class="badge">{{ $category->views }}</span>
	<button type="button" class="btn btn-danger btn-xs category-delete" data-siteid="{{ $siteid }}" data-categoryid="{{ $category->id }}">
		<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
	<a href="{{ app('url')->current() }}?category={{ $category->id }}">{{ $category->title }}</a> 
	<small>{{ $category->remote_url }}
		<span class="additional-info">{{ count($category->subcategories) }} <i class="fa fa-bookmark-o"></i>, {{ count($category->products) }} <i class="fa fa-gift"></i>, {{ count($category->pages) }} <span class="glyphicon glyphicon-file" aria-hidden="true"></span></span> 
	</small>
</li>
@endforeach
{{ $child or null }}
</ul>