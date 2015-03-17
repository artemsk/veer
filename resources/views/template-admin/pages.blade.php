@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-1 pages-breadcrumb">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'pages'))</div>

            <h1>Pages</h1>

            <p><strong>:{{ $items->total() }}</strong>

            <p>
        <small>
            <p><u>filter</u><br/>
            @if(!empty(veer_get('filtered_id')))
            <mark>filtered by {{ veer_get('filtered') }} <a href="{{ route("admin.show", array(veer_get('filtered'))) }}">
                    #{{ veer_get('filtered_id') }}</a></mark>
            <br><a href="{{ route("admin.show", "pages") }}" class="">&times; reset</a>
            @elseif(veer_get('filtered') == 'unused')
            unused
            <br><a href="{{ route("admin.show", "pages") }}" class="">&times; reset</a>
            @else
            <a href="{{ route("admin.show", array("pages", "filter" => "unused")) }}">unused</a>
            @endif
            <p><u>sort</u><br/>
            @if(null != (\Input::get('sort')))<mark>sorted by {{ \Input::get('sort') }}</mark><br/><a href="{{ route("admin.show", "pages") }}" class="">&times; reset</a><p></p>
            @endif
            <a href="{{ route("admin.show", array("pages", "sort" => "created_at", "sort_direction" => "desc")) }}">created</a><br/>
            <a href="{{ route("admin.show", array("pages", "sort" => "updated_at", "sort_direction" => "desc")) }}">updated</a><br/>
            <a href="{{ route("admin.show", array("pages", "sort" => "manual_order", "sort_direction" => "desc")) }}">order</a><br/>
            <a href="{{ route("admin.show", array("pages", "sort" => "views", "sort_direction" => "desc")) }}">views</a><br/>
            <a href="{{ route("admin.show", array("pages", "sort" => "hidden", "sort_direction" => "desc")) }}">hidden</a><br/>
            <a href="{{ route("admin.show", array("pages", "sort" => "original", "sort_direction" => "desc")) }}">original</a>
        </small>
            <div class="sm-rowdelimiter"></div>
            <p><a class="btn btn-default" href="{{ route("admin.show", array("pages", "id" => "new")) }}" role="button">Add</a>
            
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-md-11 main-content-block categories-page pages-main">

            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
	<input name="_method" type="hidden" value="PUT">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	@include($template.'.lists.pages', array('items' => $items))

	</form>
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array('filter' => Input::get('filter', null), 'filter_id' => Input::get('filter_id', null)))->render() }}
		</div>
	</div>
        @if(count($items)>0)
        <div class='rowdelimiter'></div>
        <hr>
        @endif
	
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
	<input name="_method" type="hidden" value="PUT">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<label>Quick form: Add page</label>
	<div class="row">
		<div class="col-sm-4"><p><input type="text" class="form-control" placeholder="Title" name="title"></p></div>
		<div class="col-sm-4"><p><input type="text" class="form-control" placeholder="Categories Id [,]" name="categories"></p></div>
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="attachImage[]" multiple=true>Image</p></div>
	</div>
	<div class="xs-rowdelimiter"></div>
	<div class="row">
		<div class="col-sm-8"><p><input type="text" class="form-control" placeholder="[Url]" name="url"></p></div>
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="attachFile[]" multiple=true>Attach file (*.html for full replacement)</p></div>
	</div>
	<div class="row">
		<div class="col-sm-6"><p>
			<textarea class="form-control" placeholder="@{{Small txt}} Txt" rows="10" name="txt"></textarea></p>
		</div>
		<div class="col-sm-6">
			<p><input class="form-control btn btn-danger" type="submit" value="Add"></p>
		</div>
	</div>
	</form>
        
        </div>
    </div>
</div>

@stop
