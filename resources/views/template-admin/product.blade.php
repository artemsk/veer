@extends($template.'.layout.base')

@section('body')

<?php event('lock.for.edit'); ?>
  
<div class="container-fluid ajax-form-submit" data-replace-div=".ajax-form-submit">
<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
<input name="_method" type="hidden" value="PUT">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="row">
    <div class="col-md-1 pages-breadcrumb">
        <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'product'))</div>

        <div class="row pages-column">
            <div class="col-md-12 col-sm-12">
                <h1>Product <small>#{{ $items->id or '—' }}</small></h1>

                <small><nobr><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $items->viewed or '—' }}</nobr>
                <nobr><span class="glyphicon glyphicon-fire danger-icon" aria-hidden="true"></span> {{ $items->ordered or '—' }}</nobr><div class="visible-xs-inline visible-sm-inline">
                | created at <strong>{{ !empty($items->created_at) ?
        Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') : '—' }}</strong>

                | updated at <strong>{{ !empty($items->created_at) ? Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') : '—' }}</strong></div>
                </small>
                @if(veer_get('event.lock-for-edit') == true)
                <div class="xs-rowdelimiter"></div>
                <span class="label label-danger">locked</span>
                @endif
                <div class="sm-rowdelimiter"></div>
                <p>
            @if(isset($items->status))
				@if ($items->status == 'buy' || $items->status == 'on')
				<button type="submit" name="action" value="updateStatus.{{ $items->id }}" class="btn btn-success" title="Current: ON (BUY)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span> On | Buy</button>
                @elseif ($items->status == 'hide')
                <button type="submit" name="action" value="updateStatus.{{ $items->id }}" class="btn btn-default" title="Current: OFF (HIDE)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-stop" aria-hidden="true"></span> Hidden</button>
                @elseif ($items->status == 'sold')
                <button type="submit" name="action" value="updateStatus.{{ $items->id }}" class="btn btn-warning" title="Current: SOLD OUT (SOLD)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span> Sold out</button>
                @else
                <button type="submit" name="action" value="updateStatus.{{ $items->id }}" class="btn btn-danger" title="Current: UNKNOWN ({{ $items->status }})" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Unknown</button>
				@endif
            @endif	
                <div class="sm-rowdelimiter"></div>
                <div class="hidden-sm hidden-xs">
                <p>created at<br/><strong>{{ !empty($items->created_at) ?
        Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') : '—' }}</strong></p>

                <p>updated at<br/><strong>{{ !empty($items->created_at) ? Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>
            </div>
        </div>
    </div>
    <div class="col-md-11 main-content-block pages-main">
        <div class="row">
            <div class="col-lg-8 col-md-7 col-sm-7">
                <div class="pages-page">
                    <div class="row">
                        <div class="col-sm-12"><p><input type="text" class="form-control transparent-input" name="fill[url]" placeholder="Clean Url" value="{{ $items->url or null }}"></p></div>
                    </div>
                    <div class="xs-rowdelimiter"></div>
                    <div class="row">
                        <div class="col-sm-12"><p><strong><input type="text" class="form-control input-lg transparent-input" placeholder="Title" name="fill[title]" value="{{ $items->title or null }}"></strong></p></div>
                    </div>
                    <div class="rowdelimiter-20"></div>
                    <textarea class="form-control" rows="5" name="fill[descr]" @if(veer_get('event.lock-for-edit') == true) disabled @endif placeholder="Description">{{ $items->descr or null }}</textarea>
                </div>
                <div class="rowdelimiter-20 pages-page-wider"></div>
                <div class="pages-page">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">Cur</span>
                                <input type="text" class="form-control" placeholder="Currency" name="fill[currency]" value="{{ $items->currency or null }}">
                            </div>
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="text" class="form-control" placeholder="Price" name="fill[price]" value="{{ $items->price or null }}">
                            </div>
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="input-group">
                                <span class="input-group-addon">Base</span>
                                <input type="text" class="form-control" placeholder="Base" name="fill[price_base]" value="{{ $items->price_base or null }}">
                            </div>
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="input-group">
                                <span class="input-group-addon">Whole</span>
                                <input type="text" class="form-control" placeholder="Whole" name="fill[price_opt]" value="{{ $items->price_opt or null }}">
                            </div>	
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="input-group">
                                <span class="input-group-addon">Sales</span>
                                <input type="text" class="form-control" placeholder="Sales" name="fill[price_sales]" value="{{ $items->price_sales or null }}">
                            </div>	
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            Sales price time range (m/d/y):
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control" name="fill[price_sales_on]" 
                                       value="{{ !empty($items->price_sales_on) ? Carbon\Carbon::parse($items->price_sales_on)->format('m/d/Y') : '' }}"/>
                                <span class="input-group-addon">to</span>
                                <input type="text" class="input-sm form-control" name="fill[price_sales_off]" 
                                       value="{{ !empty($items->price_sales_off) ? Carbon\Carbon::parse($items->price_sales_off)->format('m/d/Y') : '' }}"/>
                            </div>
                            @if(isset($items->price_sales_on) && isset($items->price_sales_off)) 
                            @if(now() >= ($items->price_sales_on) && now() <= ($items->price_sales_off))
                            <span class="label label-success">currently active</span>
                            @elseif (now() > ($items->price_sales_on) && now() > ($items->price_sales_off))
                            <span class="label label-default">past</span>
                            @elseif (now() < ($items->price_sales_on) && now() < ($items->price_sales_off))
                            <span class="label label-warning">pending</span>
                            @else
                            <span class="label label-danger">unknown</span>
                            @endif
                            @endif
                        </div>
                        <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                        <div class="col-md-6">
                            <div class="page-checkboxes-box">
                                <input type="checkbox" class="page-checkboxes" name="fill[star]" data-on-color="warning" 
                                data-on-text='<span class="glyphicon glyphicon-heart warning-icon" aria-hidden="true" title="Star"></span> Star'  data-off-text='<span class=" glyphicon glyphicon-heart-empty warning-icon" aria-hidden="true" title="Star"></span>' @if(isset($items->star) && $items->star == true) checked @endif>
                            </div>
                            <div class="page-checkboxes-box">
                                <input type="checkbox" class="page-checkboxes" name="fill[download]" data-on-color="info" 
                                data-on-text='<span class="glyphicon glyphicon-floppy-save" aria-hidden="true" title="Star"></span>&nbsp;Digital' data-off-text='Not digital' @if(isset($items->download) && $items->download == true) checked @endif>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">Qty</span>
                                <input type="text" class="form-control" name="fill[qty]" placeholder="Qty" value="{{ $items->qty or null }}">
                            </div>				
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="input-group">
                                <span class="input-group-addon">Weight @if(!isset($items->weight) || $items->weight <= 0)
                                        &nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No weight!"></span>
                                        @endif</span>
                                <input type="text" class="form-control" name="fill[weight]" placeholder="Weight" value="{{ $items->weight or null }}">
                            </div>	
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="input-group">
                                <span class="input-group-addon">Production Code</span>
                                <input type="text" class="form-control" name="fill[production_code]" 
                                       placeholder="Production code" value="{{ $items->production_code or null }}">
                            </div>	
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="input-group">
                                <span class="input-group-addon">Score</span>
                                <input type="text" class="form-control" name="fill[score]" placeholder="Score" value="{{ $items->score or null }}">
                            </div>
                            <p></p>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <strong>Visible since:</strong>
                                <div class="row">
                                    <div class="col-xs-6">
                                <input type="text" class="form-control date-container" name="fill[to_show]"
                                       placeholder="Month/Day/Year" value="{{ !empty($items->to_show) ? Carbon\Carbon::parse($items->to_show)->format('m/d/Y') : '' }}"/>
                                    </div>
                                    <div class="col-xs-3 narrower">
                                        <input type="text" class="form-control" name="to_show_hour" placeholder="Hour" value="{{ !empty($items->to_show) ? Carbon\Carbon::parse($items->to_show)->format('H') : '' }}">
                                    </div>
                                    <div class="col-xs-3 narrower">
                                        <input type="text" class="form-control" name="to_show_minute" placeholder="Minute" value="{{ !empty($items->to_show) ? Carbon\Carbon::parse($items->to_show)->format('i') : '' }}">
                                    </div>
                                </div>
                            @if(isset($items->to_show))
                            @if(now() >= ($items->to_show))
                            <span class="label label-success">visible</span>
                            @else
                            <span class="label label-warning">pending</span>
                            @endif
                            @endif                            
                        </div>
                    </div>                    
                </div>
                <div class="rowdelimiter-20 pages-page-wider"></div>
                <div class="pages-page">
                    <div class="row">
                        <div class="col-lg-6">
                            <label>Parent products</label>
                            <ul class="list-group">
                                @if(isset($items->parentproducts) && count($items->parentproducts)>0)	
                                @foreach ($items->parentproducts as $prd)	
                                <li class="list-group-item">
                                    <span class="badge">{{ $prd->viewed }}</span>
                                    <button type="submit" name="action" value="removeParentProduct.{{ $prd->id }}" class="btn btn-warning btn-xs">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
                                    <a href="{{ route('admin.show', array('products', 'id' => $prd->id)) }}">{{ $prd->title }}</a> 
                                    <small>{{ app('veershop')->getPrice($prd, true, array('forced_currency' => 1)) }}</small>
                                </li>	
                                @endforeach
                                @endif
                                <li class="list-group-item suggestions-in-list-2">
                                    <input type="text" class="form-control input-no-borders" placeholder=":Existings IDs[,]" name="attachParentProducts">
                                </li>
                            </ul>	
                        </div>
                        <div class="col-lg-6">
                            <label>Sub products</label>
                            <ul class="list-group">
                                @if(isset($items->subproducts) && count($items->subproducts)>0)	
                                @foreach ($items->subproducts as $prd)	
                                <li class="list-group-item">
                                    <span class="badge">{{ $prd->viewed }}</span>
                                    <button type="submit" name="action" value="removeChildProduct.{{ $prd->id }}" class="btn btn-warning btn-xs">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
                                    <a href="{{ route('admin.show', array('products', 'id' => $prd->id)) }}">{{ $prd->title }}</a> 
                                    <small>{{ app('veershop')->getPrice($prd, true, array('forced_currency' => 1)) }}</small>
                                </li>	
                                @endforeach
                                @endif
                                <li class="list-group-item suggestions-in-list-2">
                                    <input type="text" data-type="product" class="form-control input-no-borders show-list-of-items suggestions-product" placeholder=":Existings IDs[,]" name="attachChildProducts">
                                </li>
                                <div id="loadedSuggestions-product"></div>
                            </ul>
                        </div>
                    </div>
                    <div class="rowdelimiter-20 visible-lg-block"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Connected Pages</label>
                            <ul class="list-group">
                                @if(isset($items->pages) && count($items->pages)>0)	
                                @foreach ($items->pages as $page)	
                                <li class="list-group-item">
                                    <span class="badge">{{ $page->views }}</span>
                                    <button type="submit" name="action" value="removePage.{{ $page->id }}" class="btn btn-warning btn-xs">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
                                    <a href="{{ route('admin.show', array('pages', 'id' => $page->id)) }}">{{ $page->title }}</a> 
                                    <small>{{ Carbon\Carbon::parse($page->created_at)->format('d M Y'); }}</small>
                                </li>	
                                @endforeach
                                @endif
                                <li class="list-group-item suggestions-in-list-2">
                                    <input type="text" data-type="page" class="form-control input-no-borders show-list-of-items suggestions-page" placeholder=":Existings IDs[,]" name="attachPages">
                                </li>
                                <div id="loadedSuggestions-page"></div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sm-rowdelimiter visible-xs-block"></div>
            <div class="col-lg-4 col-md-5 col-sm-5">
                <div class="pages-page">
                    <label>Categories</label> @if(!isset($items->categories) || count($items->categories) <= 0)
                    <span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
                    @endif

                    <ul class="list-group">
                        @if(isset($items->categories) && count($items->categories)>0)
                        @foreach ($items->categories as $category)
                        <li class="list-group-item">
                            <span class="badge">{{ $category->views }}</span>
                            <button type="submit" name="action" value="removeCategory.{{ $category->id }}" class="btn btn-warning btn-xs">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
                            <a href="{{ route('admin.show', array('categories', 'category' => $category->id)) }}">{{ $category->title }}</a>
                            <small>{{ $category->remote_url }}</small>
                        </li>
                        @endforeach
                        @endif
                        <li class="list-group-item suggestions-in-list-2">
                            <input type="text" name="attachCategories" data-type="category" class="form-control input-no-borders show-list-of-items suggestions-category" placeholder=":Existings IDs[,]" value="{{ null != (Input::get('category')) ? ':'.Input::get('category') : null }}">
                        </li>
                        <div id="loadedSuggestions-category"></div>     
                    </ul>
                </div>
                <div class="rowdelimiter-20 pages-page-wider"></div>
                <div class="pages-page pages-page-attributes">
                    <label>Attributes</label>
			@if(isset($items->attributes))                        
			@foreach($items->attributes as $key => $attribute)
                <div class="row">
                    <div class="col-xs-12">
                        <strong><input type="text" name="attribute[{{ $key }}][name]" class="form-control input-sm attributes-input" value="{{ $attribute->name }}" placeholder="Name" size="15"></strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                            <input type="text" name="attribute[{{ $key }}][val]" class="form-control input-sm attributes-input-val" value="{{ $attribute->val }}" placeholder="Value" size="14">
                            @if($attribute->type != 'descr')
                            <input type="text" class="form-control input-sm attributes-input-val" name='attribute[{{ $key }}][price]' 
                                           value="{{ $attribute->pivot->product_new_price }}" placeholder="New price">
                            @if($attribute->pivot->product_new_price > 0) 
                            <span class="label-danger label"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                attribute changes price</span>
                            @endif
                            @endif 
                        
                        <div class="sm-rowdelimiter"></div>
                    </div>
                    <div class="col-xs-6">
                        <select class="form-control input-sm attributes-input-val" name='attribute[{{ $key }}][type]'>
                            <option>{{ $attribute->type }}</option>
                            <option>choose</option>
                            <option>descr</option>                        
                        </select>
                        <div class="row">
                            <div class="col-sm-12">
                                <div style="padding-left:3px;">
                                    <div class="collapse" id="descriptionAttributes{{ $key }}" ><p></p><textarea class="form-control input-sm" name="attribute[{{ $key }}][descr]" placeholder="Description">{{ $attribute->descr }}</textarea></div>
                                    <a href="#descriptionAttributes{{ $key }}" data-toggle="collapse"><small>description</small></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
			@endforeach                        
			@endif
                        <div class="new-attribute-block">                        
			<div class="row">
				<div class="col-md-12">
                        <strong><input type="text" name="attribute[new][name]" data-type="attribute" class="form-control input-sm show-list-of-items suggestions-attribute" placeholder="Name" autocomplete="off" id="attributes-suggestions-id"></strong>
                        <div id="loadedSuggestions-attribute"></div>
				<p></p><input type="text" name="attribute[new][val]" class="form-control input-sm" placeholder="Value">
                <p></p><textarea  name="attribute[new][descr]" class="form-control input-sm" placeholder="Description"></textarea>
                <p></p><select class="form-control input-sm" name='attribute[new][type]'>
						<option>choose</option>
                        <option>descr</option>                        
                    </select>
                <p></p><strong><input type="text" class="form-control input-sm" value="" placeholder="New price" name='attribute[new][price]'></strong>
				</div>
			</div>
                    <div class="sm-rowdelimiter"></div>
                    </div>
                    <div class="new-attributes-added"></div>
                    <a class="add-more-attributes"><small>more attributes</small></a>
                </div>
                <div class="rowdelimiter-20"></div>
                <div class="pages-page">
                    <label>Tags</label>
                    <textarea class="form-control" rows="5" name="tags" placeholder="Tags (One per row)">@if(isset($items->tags))
@foreach($items->tags as $tag)
{{ $tag->name }}
@endforeach
@endif</textarea>
                </div>
                <div class="rowdelimiter-20"></div>
                <a href="#freeForm" data-toggle="collapse"><small>free form</small></a>
                <div class="collapse" id="freeForm">
                    <div class="pages-page">
                    <textarea class="form-control" name="freeForm" rows="5" placeholder="[Tag:Ids,] [Attribute:Ids,]"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="rowdelimiter-20"></div>
    <div class="row">
        <div class="col-xs-12 categories-connections-block">
            <div class="categories-connections-name">Images</div><div class="categories-connections"></div>
        </div>
    </div>
    <div class="row">
        @if(isset($items->images) && count($items->images)>1)
        <div class="col-lg-2 col-md-2 col-sm-2">
            <p><button type="submit" class="btn btn-default btn-block" name="action" value="removeAllImages"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span class="hidden-sm">Remove All</span></button>
        </div>
        @endif
        <div class="col-lg-5 col-md-4 col-sm-4 color-upload-form">
            <p><input class="input-files-enhance" type="file" id="InFile1" name="uploadImage[]" multiple=true></p>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3">

            <p><input class="form-control show-list-of-items suggestions-image categories-page-input" data-type="image" name="attachImages" placeholder=":Existing Images IDs[,]"></p>
            <div id="loadedSuggestions-image"></div>
        </div>
    </div>
    <p></p>
    @if(isset($items->images) && count($items->images)>0)
    <div class="rowdelimiter-20"></div>
    <div class="row">
        <div class="col-md-12">
	@include($template.'.lists.images', array('items' => $items->images, 'denyDelete' => true))
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12 categories-connections-block">
            <div class="categories-connections-name">Files</div><div class="categories-connections"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-5 col-md-4 col-sm-4 color-upload-form">
            <p><input class="input-files-enhance" type="file" id="InFile2" name="uploadFiles[]" multiple=true></p>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3">
            <p><input class="form-control categories-page-input" name="attachFiles" placeholder=":Existing Files IDs[,]"></p>
        </div>
    </div>
    <p></p>
    @if(isset($items->downloads) && count($items->downloads)>0)
    <div class="rowdelimiter-20"></div>
    <div class="row">
        <div class="col-md-12">
	@include($template.'.lists.files', array('files' => $items->downloads, 'denyDelete' => true))
        </div>
    </div>
    @endif
    <div class="rowdelimiter-20"></div>    
	@if(isset($items->id))
	<div class="row">
		<div class="col-sm-2 col-xs-6"><button type="submit" name="action" value="saveAs" class="btn btn-warning btn-lg btn-block submit-skip-ajax">Save As</button></div>
		<div class="col-sm-10 col-xs-6"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block" @if(veer_get('event.lock-for-edit') == true) disabled >Update [locked] @else >Update @endif</button></div>
	</div>
    <div class="rowdelimiter-20"></div>
	<hr class="hr-darker">
	<div class="row">
		<div class="col-xs-12 page-stats">
			<a class="btn btn-info" href="{{ route('admin.show', array('orders', "filter" => "products", "filter_id" => $items->id)) }}" 
			   role="button">{{ $items->orders()->count() }} orders</a>
			@if(isset($items['basket']))
			<a class="btn btn-info" href="{{ route('admin.show', array('lists', "filter" => "products", "filter_id" => $items->id)) }}" 
			   role="button">{{ $items['basket'] }} in baskets</a>
			@endif
			@if(isset($items['lists']))
			<a class="btn btn-default" href="{{ route('admin.show', array('lists', "filter" => "products", "filter_id" => $items->id)) }}" 
			   role="button">{{ $items['lists'] }} in lists</a>		
			@endif
			<a class="btn btn-default" href="{{ route('admin.show', array('comments', "filter" => "products", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->comments()->count() }} comments</a>
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "products", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->communications()->count() }} communications</a>
		</div>
	</div>
	@else
	<button type="submit" name="action" value="add" class="btn btn-danger btn-lg btn-block submit-skip-ajax">Add</button>
	@endif

@if(isset($items->id) && veer_get('event.lock-for-edit') != true)
<div class="action-hover-box"><button type="submit" name="action" value="update" class="btn btn-danger btn-block">Update</button></div>
@endif  
</form>
</div>
@stop