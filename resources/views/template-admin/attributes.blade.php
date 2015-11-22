@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-elements', array('place' => 'attributes'))</div>
            
            <div class="row pages-column">
                <div class="col-md-12 col-sm-3">
                    <h1>Attributes</h1>

                    <p>                        
                    :{{ $items->total() }} values
                    <div class="sm-rowdelimiter"></div>
                </div>
            </div>
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block ajax-form-submit" data-replace-div=".ajax-form-submit">
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
            <input name="_method" type="hidden" value="PUT">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">           
            @foreach (null == data_get($items, 'grouped') ? array() : $items['grouped'] as $name => $item)
            <div class="row">
                <div class="col-sm-12">	
                        <p><strong><input type="text" class="form-control transparent-input" placeholder="Name" name="renameAttrName[{{ $name }}]" value="{{ $name }}"></strong></p>
                        <br/>
                        <div class="row">
                        @foreach ($item as $key => $val)	
                        <div class="col-lg-2 col-md-6 text-center">
                            <div class="thumbnail common-thumbnail">
                                <div class="caption">
                                    <input type="text" name="renameAttrValue[{{ $items[$val]->id }}]" 
                                           class="form-control admin-form text-center" value="{{ $items[$val]->val }}">
                                    <div data-toggle="collapse" data-target="#descr{{ $items[$val]->id }}" class="attribute-descr"
                                       aria-expanded="true" aria-controls="descr{{ $items[$val]->id }}">description</div>
                                    <div id="descr{{ $items[$val]->id }}" class="collapse @if(empty($items[$val]->descr)) out @else in @endif">
                                    <textarea name="descrAttrValue[{{ $items[$val]->id }}]" class="form-control input-sm">{{ $items[$val]->descr }}</textarea>
                                    </div>
                                    <p></p>
                                    <input class="page-checkboxes" data-on-text="descr" data-off-text="choose&nbsp;" data-size="mini" type="checkbox" value="1" name="attrType[{{ $items[$val]->id }}]" @if($items[$val]->type == "descr") checked @endif >
                                    <p></p>
                                    <button type="submit" name="action" value="deleteAttrValue.{{ $items[$val]->id }}" class="btn btn-link btn-xs" style="padding:0px;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                    <small>#{{ $items[$val]->id }}</small>
                                    <span class="label label-info"><a href="{{ route('admin.show', array('products', 'filter' => 'attributes', 'filter_id' => $items[$val]->id)) }}" target="_blank">{{ $items[$val]->products->count() }}</a></span>
                                    <span class="label label-success"><a href="{{ route('admin.show', array('pages', 'filter' => 'attributes', 'filter_id' => $items[$val]->id)) }}" target="_blank">{{ $items[$val]->pages->count() }}</a></span>                            
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-lg-2 col-md-6">
                            <input type="text" name="newAttrValue[{{ $name }}]" class="form-control transparent-input" placeholder="New [,] [:id:id]" value="">
                            <p></p><button type="submit" name="action" value="updateAttributes" class="btn btn-default">Update</button>
                        </div>
                        </div>
                <p><small>{{ $items['counted'][$name]['prd'] }} products, {{ $items['counted'][$name]['pg'] }} pages</small></p>
                </div>
            </div>
            <div class="rowdelimiter"></div>
            @endforeach            
            </form>            
            <div class="row">
                <div class="text-center">
                    {{ $items->render() }}
                </div>
            </div>
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">		
            <div class="col-sm-3">
                <div class="thumbnail common-thumbnail">
                    <div class="caption">
                        <p><strong><input type="text" class="form-control" name="newName" placeholder="New attribute name" value=""></strong></p>
                        <p><textarea class="form-control" rows="10" name="newValue" placeholder="Values [:id:id]" name=""></textarea></p>	
                        <button type="submit" name="action" value="newAttribute" class="btn btn-default">Add</button>
                    </div>
                </div>
            </div>
            @if(count($items['grouped'])>0) 
            <div class="col-sm-9">
                <div class="thumbnail common-thumbnail">
                    <div class="caption">
                        <small>		
                        @foreach ($items['grouped'] as $name => $item)	 
                        <p>{{ $name }} —  
                            @foreach ($item as $val)
                            <strong>{{ $items[$val]->val  }}</strong>
                            @endforeach
                        </p>
                        @endforeach
                        </small>
                    </div>
                </div>
            </div>
            @endif
            </div>
            </form>
        </div>
    </div>
</div>
@stop
