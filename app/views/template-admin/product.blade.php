@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}"><strong>Products</strong></a></li>
</ol>
<h1>Product #{{ $items->id or '—' }} <small>
		&nbsp; <nobr><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $items->viewed or '—' }}</nobr>
		&nbsp; <nobr><span class="glyphicon glyphicon-fire danger-icon" aria-hidden="true"></span> {{ $items->ordered or '—' }}</nobr></small></h1>
<br/>
<div class="container">

	<div class="row">
		<div class="col-sm-6"><p><input type="text" class="form-control" placeholder="Clean Url" value="{{ $items->url or null }}"></p></div>
		<div class="col-sm-2 col-xs-6 text-center"><p>
				@if(isset($items->status))
				@if ($items->status == 'buy' || $items->status == 'on')
					<button type="button" class="btn btn-success" title="Current: ON (BUY)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span> On | Buy</button>
					@elseif ($items->status == 'hide')
					<button type="button" class="btn btn-default" title="Current: OFF (HIDE)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-stop" aria-hidden="true"></span> Hidden</button>
					@elseif ($items->status == 'sold')
					<button type="button" class="btn btn-warning" title="Current: SOLD OUT (SOLD)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span> Sold out</button>
					@else
					<button type="button" class="btn btn-danger" title="Current: UNKNOWN ({{ $items->status }})" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Unknown</button>
					@endif
				@endif	
			</p></div>		
		<div class="col-sm-2 col-xs-6"><p>created at<br/><strong>{{ !empty($items->created_at) ? 
			Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>
		<div class="col-sm-2 col-xs-12"><p>updated at<br/><strong>{{ !empty($items->created_at) ? Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>	
	</div>
	<div class="row">
		<div class="col-sm-12"><p><strong><input type="text" class="form-control input-lg" placeholder="Title" value="{{ $items->title or null }}"></strong></p></div>
	</div>
	<div class="row">
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Cur</span>
				<input type="text" class="form-control" placeholder="Currency" value="{{ $items->currency or null }}">
			</div>
		</div>
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">$</span>
				<input type="text" class="form-control" placeholder="Price" value="{{ $items->price or null }}">
			</div>
		</div>
		<div class="col-md-3"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Sales</span>
				<input type="text" class="form-control" placeholder="Sales" value="{{ $items->price_sales or null }}">
			</div>			

		</div>
		<div class="col-md-3"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Whole</span>
				<input type="text" class="form-control" placeholder="Whole" value="{{ $items->price_opt or null }}">
			</div>				
		</div>
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Base</span>
				<input type="text" class="form-control" placeholder="Base" value="{{ $items->price_base or null }}">
			</div>				
		</div>		
	</div>
	<div class="row">
		<div class="col-md-2"><p></p>
			<input type="checkbox" class="page-checkboxes" data-on-color="warning" 
				data-on-text='<span class="glyphicon glyphicon-heart warning-icon" 
					  aria-hidden="true" title="Star"></span>&nbsp; Highlighted!' 
				data-off-text='<span class=" glyphicon glyphicon-heart-empty warning-icon" 
					  aria-hidden="true" title="Star"></span>&nbsp;<br/>Regular' 
				   @if(isset($items->star) && $items->star == true) checked @endif>
		</div>
		<div class="col-md-2"><p></p>
			<input type="checkbox" class="page-checkboxes" data-on-color="info" 
				data-on-text='<span class="glyphicon glyphicon-floppy-save" 
					  aria-hidden="true" title="Star"></span><br/>Digital' 
				data-off-text='Is it digital?' 
				   @if(isset($items->download) && $items->download == true) checked @endif>			
		</div>
		<div class="col-md-3">
			<p></p>
			Sales price time range (m/d/y):
			<div class="input-daterange input-group" id="datepicker">
				<input type="text" class="input-sm form-control" name="start" 
					   value="{{ !empty($items->price_sales_on) ? Carbon\Carbon::parse($items->price_sales_on)->format('m/d/Y') : '' }}"/>
				<span class="input-group-addon">to</span>
				<input type="text" class="input-sm form-control" name="end" 
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
		<div class="col-md-5">
			<p></p>
			<strong>Visible since:</strong>
				<div class="row">
					<div class="col-xs-6">
				<input type="text" class="form-control date-container" 
					   placeholder="Month/Day/Year" value="{{ !empty($items->to_show) ? Carbon\Carbon::parse($items->to_show)->format('m/d/Y') : '' }}"/>
					</div>
					<div class="col-xs-3 narrower">
						<input type="text" class="form-control" placeholder="Hour" value="{{ !empty($items->to_show) ? Carbon\Carbon::parse($items->to_show)->format('H') : '' }}">
					</div>
					<div class="col-xs-3 narrower">
						<input type="text" class="form-control" placeholder="Minute" value="{{ !empty($items->to_show) ? Carbon\Carbon::parse($items->to_show)->format('i') : '' }}">
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
	<div class="row">
		<div class="col-sm-8"><p></p>
				<textarea class="form-control" rows="5" placeholder="Description">{{ $items->descr or null }}</textarea></div>
		<div class="col-sm-4"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Qty</span>
				<input type="text" class="form-control" placeholder="Qty" value="{{ $items->qty or null }}">
			</div>				
			<p></p>
			<div class="input-group">
				<span class="input-group-addon">Weight @if(!isset($items->weight) || $items->weight <= 0)
						&nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No weight!"></span>
						@endif</span>
				<input type="text" class="form-control" placeholder="Weight" value="{{ $items->weight or null }}">
			</div>	
			<p></p>
			<div class="input-group">
				<span class="input-group-addon">Production Code</span>
				<input type="text" class="form-control" placeholder="Production code" value="{{ $items->production_code or null }}">
			</div>	
			<p></p>
			<div class="input-group">
				<span class="input-group-addon">Score</span>
				<input type="text" class="form-control" placeholder="Score" value="{{ $items->score or null }}">
			</div>	
		</div>
	</div>
	<div class="row">
		<div class="col-sm-8"><p></p>	
			<h3><strong>Images</strong></h3>
			<div class="row">
				<div class="col-md-6">
					<input class="input-files-enhance" type="file" id="InFile1" name="InFile1" multiple=false>
				</div>
				<div class="col-md-6">
					<input class="form-control" placeholder=":Existing Images IDs[,]">
				</div>				
			</div>
			@if(isset($items->images) && count($items->images)>0)			
			<p></p>
			@include($template.'.lists.images', array('items' => $items->images))
			@endif
			<div class="rowdelimiter"></div>
			<h3><strong>Files</strong></h3>
			<div class="row">
				<div class="col-md-6">
					<input class="input-files-enhance" type="file" id="InFile2" name="InFile2" multiple=false>
				</div>
				<div class="col-md-6">
					<input class="form-control" placeholder=":Existing Files IDs[,]">
				</div>				
			</div>
			@if(isset($items->downloads) && count($items->downloads)>0)	
			<p></p>
			@include($template.'.lists.files', array('files' => $items->downloads))
			@endif
			<div class="rowdelimiter"></div>
			
		<h3><strong>Categories</strong> @if(!isset($items->categories) || count($items->categories) <= 0)
		<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
		@endif</h3>

		<ul class="list-group">
			@if(isset($items->categories) && count($items->categories)>0)	
			@foreach ($items->categories as $category)	
			<li class="list-group-item">
				<span class="badge">{{ $category->views }}</span>
				<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
				<a href="{{ route('admin.show', array('categories', 'category' => $category->id)) }}">{{ $category->title }}</a> 
				<small>{{ $category->remote_url }}</small>
			</li>	
			@endforeach
			@endif
			<li class="list-group-item">
					<input type="text" class="form-control" placeholder=":Existings IDs[,]" 
						   value="{{ !empty($items->fromCategory) ? ':'.$items->fromCategory : null }}">
			</li>
		</ul>
		<div class="rowdelimiter"></div>
		<div class="row">
			<div class="col-md-12">  
				<label>Connected Pages</label>
				<ul class="list-group">
					@if(isset($items->pages) && count($items->pages)>0)	
					@foreach ($items->pages as $page)	
					<li class="list-group-item">
						<span class="badge">{{ $page->views }}</span>
						<button type="button" class="btn btn-warning btn-xs">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
						<a href="{{ route('admin.show', array('pages', 'id' => $page->id)) }}">{{ $page->title }}</a> 
						<small>{{ Carbon\Carbon::parse($page->created_at)->format('d M Y'); }}</small>
					</li>	
					@endforeach
					@endif
					<li class="list-group-item">
							<input type="text" class="form-control" placeholder=":Existings IDs[,]">
					</li>
				</ul>				                  
			</div> 
		</div>
		<div class="row">
			<div class="col-md-6">                    
				<label>Parent products</label>
				<ul class="list-group">
					@if(isset($items->parentproducts) && count($items->parentproducts)>0)	
					@foreach ($items->parentproducts as $prd)	
					<li class="list-group-item">
						<span class="badge">{{ $prd->viewed }}</span>
						<button type="button" class="btn btn-warning btn-xs">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
						<a href="{{ route('admin.show', array('products', 'id' => $prd->id)) }}">{{ $prd->title }}</a> 
						<small>{{ app('veershop')->getPrice($prd, true) }}</small>
					</li>	
					@endforeach
					@endif
					<li class="list-group-item">
							<input type="text" class="form-control" placeholder=":Existings IDs[,]">
					</li>
				</ul>	
			</div>
			<div class="col-md-6"> 
				<label>Sub products</label>
				<ul class="list-group">
					@if(isset($items->subproducts) && count($items->subproducts)>0)	
					@foreach ($items->subproducts as $prd)	
					<li class="list-group-item">
						<span class="badge">{{ $prd->viewed }}</span>
						<button type="button" class="btn btn-warning btn-xs">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
						<a href="{{ route('admin.show', array('products', 'id' => $prd->id)) }}">{{ $prd->title }}</a> 
						<small>{{ app('veershop')->getPrice($prd, true) }}</small>
					</li>	
					@endforeach
					@endif
					<li class="list-group-item">
							<input type="text" class="form-control" placeholder=":Existings IDs[,]">
					</li>
				</ul>	 
			</div>			
		</div> 
		
		</div>	
		<div class="col-sm-4"><p></p>
			<textarea class="form-control" rows="5" placeholder="Tags (One per row)">@if(isset($items->tags))
@foreach($items->tags as $tag)
{{ $tag->name }}

@endforeach
@endif</textarea>
			<div class="rowdelimiter"></div>
			@if(isset($items->attributes))
			@foreach($items->attributes as $attribute)
			<div class="row">
				<div class="col-md-4">
					<select class="form-control input-sm">
						<option>{{ $attribute->type }}</option>
						<option>choose</option>
                        <option>descr</option>                        
                    </select>
					<p></p>
					<button type="button" class="hidden-xs hidden-sm btn btn-default btn-xs">Update</button>	
				</div>
				<div class="col-md-8"><strong><input type="text" class="form-control input-sm" value="{{ $attribute->name }}" placeholder="Name"></strong>
					<input type="text" class="form-control input-sm" value="{{ $attribute->val }}" placeholder="Value">
				<textarea class="form-control input-sm" placeholder="Description">{{ $attribute->descr }}</textarea>
				@if($attribute->type != 'descr')
				<strong><input type="text" class="form-control input-sm" 
							   value="{{ $attribute->pivot->product_new_price }}" placeholder="New price"></strong>
				@if($attribute->pivot->product_new_price > 0) 
				<span class="label-danger label"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					attribute changes price</span>
				@endif
				@endif
				<p></p><button type="button" class="visible-xs visible-sm btn btn-default btn-xs">Update</button>
				</div>				
			</div>
			<div class="rowdelimiter"></div>
			@endforeach
			@endif
			<div class="row">
				<div class="col-md-4">
					<select class="form-control input-sm">
						<option>choose</option>
                        <option>descr</option>                        
                    </select>
					<p></p>
					<button type="button" class="hidden-xs hidden-sm btn btn-default btn-xs">Update</button>	
				</div>
				<div class="col-md-8"><strong><input type="text" class="form-control input-sm" placeholder="Name"></strong>
					<input type="text" class="form-control input-sm"placeholder="Value">
				<textarea class="form-control input-sm" placeholder="Description"></textarea>
				<strong><input type="text" class="form-control input-sm" 
							   value="" placeholder="New price"></strong>
				<p></p><button type="button" class="visible-xs visible-sm btn btn-default btn-xs">Update</button></div>
			</div>
			<div class="rowdelimiter"></div>
			<label>Free form</label>
			<textarea class="form-control" rows="5" placeholder="[Tag:Ids,] [Attribute:Ids,]"></textarea>
		</div>
	</div>
	<p></p>
	@if(isset($items->id))
	<div class="row">
		<div class="col-sm-10 col-xs-6"><button type="button" class="btn btn-danger btn-lg btn-block">Update</button></div>
		<div class="col-sm-2 col-xs-6"><button type="button" class="btn btn-warning btn-lg btn-block">Save As</button></div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">
			<a class="btn btn-info" href="{{ route('admin.show', array('orders', 'product' => $items->id)) }}" 
			   role="button">{{ $items->orders()->count() }} orders</a>
			<a class="btn btn-info" href="{{ route('admin.show', array('lists', 'product' => $items->id)) }}" 
			   role="button">{{ count($items->basket) }} in baskets</a>
			<a class="btn btn-default" href="{{ route('admin.show', array('lists', 'product' => $items->id)) }}" 
			   role="button">{{ count($items->lists) }} in lists</a>			
			<a class="btn btn-default" href="{{ route('admin.show', array('comments', 'product' => $items->id)) }}"
			   role="button">{{ $items->comments()->count() }} comments</a>
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', 'product' => $items->id)) }}"
			   role="button">{{ $items->communications()->count() }} communications</a>
		</div>
	</div>
	@else
	<button type="button" class="btn btn-danger btn-lg btn-block">Add</button>
	@endif
<!--	
<p>$items->grp</p>
<p>$items->grp_ids</p>
// TODO: deprecated?
-->
</div>
@if(isset($items->id))
<div class="action-hover-box"><button type="button" class="btn btn-danger btn-lg btn-block">Update</button></div>
@endif
@stop