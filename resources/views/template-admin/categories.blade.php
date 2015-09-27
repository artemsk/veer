@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'categories'))</div>

            <h1>Categories</h1>
            @if(!empty(veer_get('filtered')))
            <small>
            filtered by {{ veer_get('filtered') }} <a href="{{ route("admin.show", array(veer_get('filtered'))) }}">
                    #{{ veer_get('filtered_id') }}</a>
            </small>
            @endif
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block">

            <div class="row">
                <div class="col-lg-9"><div class="categories-page categories-page-general">
	@foreach ($items as $item)

	<h2 id="site{{ $item->id }}">{{ $item->configuration()->where('conf_key','=','SITE_TITLE')->value('conf_val'); }} <small>{{ $item->url }}
		&nbsp;:{{ count($item->categories) }}</small></h2>

	<div class="categories-lists categories-list-{{ $item->id}} ">
        <ul class="list-group categories-group sortable" id="sortable{{ $item->id }}" data-parentid="{{ $item->id }}">	
			@include($template.'.lists.categories-category', array('categories' => $item->categories, 'siteid' => $item->id))
        </ul>
	</div>
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="category-add" data-siteid="{{ $item->id }}">
	<input name="_method" type="hidden" value="PUT">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Title" name="newcategory">
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit" name="add2site" value="{{ $item->id }}">Add</button>
			</span>
		</div>
		<input type="hidden" name="siteid" value="{{ $item->id }}">
	</form>

	<div class="rowdelimiter"></div>

	@endforeach

	</div>
        </div>
</div>
            
        </div>
    </div>
</div>
        

@stop