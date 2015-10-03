@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-1 pages-breadcrumb">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'pages'))</div>

            <div class="row pages-column">
                <div class="col-md-12 col-sm-3">
                    <h1>Pages</h1>

                    <p><strong>:{{ $items->total() }}</strong>

                    <div class="hidden-xs hidden-sm"><p>
                    @include($template.'.layout.pages-left-column-filter')
                        <p></p>
                    @include($template.'.layout.pages-left-column-sort')
                    <div class="sm-rowdelimiter"></div>
                    </div>
                    <p><a class="btn btn-default" href="{{ route("admin.show", array("pages", "id" => "new")) }}" role="button">Add</a>
                    <div class="sm-rowdelimiter"></div>
                </div>
                <div class="col-sm-3 visible-sm-block visible-xs-block">

                    @include($template.'.layout.pages-left-column-filter')

                </div>
                <div class="col-sm-6 visible-sm-block visible-xs-block">

                    @include($template.'.layout.pages-left-column-sort')

                </div>
            </div>
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-md-11 main-content-block pages-main ajax-form-submit" data-replace-div=".ajax-form-submit">

            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                @include($template.'.lists.'.(Input::get('sort') == 'manual_order' ? 'pages-li' : 'pages'), array('items' => $items))

            </form>
            <div class="row">
                <div class="text-center">
                    {{ $items->appends(array('filter' => Input::get('filter', null), 'filter_id' => Input::get('filter_id', null), 'sort' => Input::get('sort'), 'sort_direction' => Input::get('sort_direction')))->render() }}
                </div>
            </div>
            @if(count($items)>0)
            <div class='rowdelimiter'></div>
            <hr class="hr-darker">
            @endif
	
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PUT">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <label>Quick form: Add page</label>
            <div class="row">
                <div class="col-sm-4"><p><input type="text" class="form-control transparent-input" placeholder="Title" name="title"></p></div>
                <div class="col-sm-4"><p><input type="text" class="form-control transparent-input" placeholder="Categories Id [,]" name="categories"></p></div>
                <div class="col-sm-4 color-upload-form"><p><input class="input-files-enhance transparent-input" type="file" id="InFile1" name="uploadImage[]" multiple=true>Image</p></div>
            </div>
            <div class="xs-rowdelimiter"></div>
            <div class="row">
                <div class="col-sm-8"><p><input type="text" class="form-control transparent-input" placeholder="[Url]" name="url"></p></div>
                <div class="col-sm-4 color-upload-form"><p><input class="input-files-enhance transparent-input" type="file" id="InFile1" name="uploadFile[]" multiple=true>Attach file (*.html for full replacement)</p></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><p>
                    <textarea class="form-control transparent-input" placeholder="@{{Small txt}} Txt" rows="10" name="txt"></textarea></p>
                </div>
                <div class="col-sm-6">
                    <p><input class="form-control btn btn-primary submit-skip-ajax" type="submit" value="Add"></p>
                </div>
            </div>
            </form>        
        </div>
    </div>
</div>

@stop
