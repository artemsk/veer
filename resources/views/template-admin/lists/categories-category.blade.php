<ul class="list-group categories-group sortable" id="sortable{{ $siteid }}" data-parentid="{{ $siteid }}">	
@foreach ($categories as $category)	
<li class="list-group-item category-item-{{ $category->id }}">
    <span class="badge" title="views">{{ $category->views }}</span>
	<button type="button" class="btn btn-danger btn-xs category-delete" data-siteid="{{ $siteid }}" data-categoryid="{{ $category->id }}">
		<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
	<a href="{{ app('url')->current() }}?category={{ $category->id }}">{{ $category->title }}</a> 
	<small>{{ $category->remote_url }}
		<span class="additional-info">{{ count($category->pages) }} <span class="glyphicon glyphicon-file" aria-hidden="true" title="pages"></span> &nbsp; {{ count($category->products) }} <i class="fa fa-gift" title="products"></i> &nbsp; {{ count($category->subcategories) }} <i class="fa fa-bookmark-o" title="child categories"></i></span>
	</small>
</li>
@endforeach
{{ $child or null }}
</ul>