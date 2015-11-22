@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2 pages-breadcrumb">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-elements', array('place' => 'tags'))</div>
            
            <div class="row pages-column">
                <div class="col-md-12 col-sm-3">
                    <h1>Tags</h1>

                    <p><strong>:{{ $items->total() }}</strong>
                    <div class="sm-rowdelimiter"></div>
                </div>
            </div>
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block ajax-form-submit" data-replace-div=".ajax-form-submit">
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-sm-12">	
                        <div class="row">
                @foreach($items as $key => $item)	
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="thumbnail common-thumbnail">
                        <div class="caption">
                            <input type="text" name="renameTag[{{ $item->id }}]" class="form-control admin-form text-center" value="{{ $item->name }}">
                            <div class="text-center">
                                <button type="submit" name="action" value="deleteTag.{{ $item->id }}" class="btn btn-link btn-xs" style="padding: 0px;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                <small>#{{ $item->id }}</small>
                                <span class="label label-info"><a href="{{ route('admin.show', array('products', 'filter' => 'tags', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->products->count() }}</a></span>
                                <span class="label label-success"><a href="{{ route('admin.show', array('pages', 'filter' => 'tags', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->pages->count() }}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="thumbnail common-thumbnail">
                        <div class="caption">
                            <input type="text" name="newTag" class="form-control admin-form" placeholder="New tag [,] [:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Several tags comma separated.<br/>IDs of products & pages — :1,2,3:5,6" value="">
                            <br/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <button type="submit" name="action" value="updateTags" class="btn btn-primary">Update</button>
                </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="text-center">
                        {{ $items->render() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>   
@stop
