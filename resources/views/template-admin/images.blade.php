@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2 pages-breadcrumb">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-elements', array('place' => 'images'))</div>
            
            <div class="row pages-column">
                <div class="col-md-12 col-sm-3">
                    <h1>Images</h1>

                    <p>                        
                    @if(Input::get('filter', null) == 'unused')
                    <strong>{{ $items->count() }}</strong> <small>unused</small>
                    @else
                    <strong>:{{ $items->total() }}</strong> <small>| <a href="{{ route("admin.show", array("images", "filter" => "unused")) }}">unused</a></small>
                    @endif
                    <div class="sm-rowdelimiter"></div>
                </div>
            </div>
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block ajax-form-submit" data-replace-div=".ajax-form-submit">
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PUT">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @include($template.'.lists.images', array('items' => $items))
	
                <div class="row">
                    <div class="text-center">
                        {{ $items->appends(array('filter' => Input::get('filter', null)))->render() }}
                    </div>
                </div>

                <hr class="hr-darker">
                <label>Upload Images</label>
                <div class="row">
                    <div class="col-sm-4 color-upload-form">
                        <p><input class="input-files-enhance transparent-input" type="file" id="InFile1" name="uploadImages1" multiple=false></p>
                        <p><input class="input-files-enhance transparent-input" type="file" id="InFile2" name="uploadImages2" multiple=false></p>
                        <p><input class="input-files-enhance transparent-input" type="file" id="InFile3" name="uploadImages3" multiple=false></p></div>
                </div>	
                <div class="row">
                    <div class="col-sm-4">
                        <textarea class="form-control transparent-input" name="attachImages" placeholder="ID|NEW [:id:id:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Connect existing|new images with products, pages, categories, users. Example: 4:2,3:1 or NEW:1:4,5,6 "></textarea>
                        <p></p>
                        <p><input class="form-control btn btn-primary" type="submit" value="Update"></p>
                    </div>
                </div>	
                </form>
        </div>
    </div>
</div>
@stop