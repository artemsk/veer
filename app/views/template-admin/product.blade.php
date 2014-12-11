@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}"><strong>Products</strong></a></li>
</ol>
<h1>Product #{{ $items->id }} <small>
		&nbsp; <nobr><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $items->viewed }}</nobr>
		&nbsp; <nobr><span class="glyphicon glyphicon-fire danger-icon" aria-hidden="true"></span> {{ $items->ordered }}</nobr></small></h1>
<br/>
<div class="container">

	<div class="row">
		<div class="col-sm-6"><p><input type="text" class="form-control" placeholder="Clean Url" value="{{ $items->url }}"></p></div>
		<div class="col-sm-2 col-xs-6 text-center"><p>
				@if ($items->status == 'buy' || $items->status == 'on')
					<button type="button" class="btn btn-success" title="Current: ON (BUY)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span> On | Buy</button>
					@elseif ($items->status == 'hide')
					<button type="button" class="btn btn-default btn-xs" title="Current: OFF (HIDE)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-stop" aria-hidden="true"></span> Hidden</button>
					@elseif ($items->status == 'sold')
					<button type="button" class="btn btn-warning btn-xs" title="Current: SOLD OUT (SOLD)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span> Sold out</button>
					@else
					<button type="button" class="btn btn-danger btn-xs" title="Current: UNKNOWN ({{ $items->status }})" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Unknown</button>
					@endif
			</p></div>		
		<div class="col-sm-2 col-xs-6"><p>created at<br/><strong>{{ Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') }}</strong></p></div>
		<div class="col-sm-2 col-xs-12"><p>updated at<br/><strong>{{ Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') }}</strong></p></div>	
	</div>
	<div class="row">
		<div class="col-sm-12"><p><strong><input type="text" class="form-control input-lg" placeholder="Title" value="{{ $items->title }}"></strong></p></div>
	</div>
	<div class="row">
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Cur</span>
				<input type="text" class="form-control" placeholder="Currency" value="{{ $items->currency }}">
			</div>
		</div>
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">$</span>
				<input type="text" class="form-control" placeholder="Price" value="{{ $items->price }}">
			</div>
		</div>
		<div class="col-md-3"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Sales</span>
				<input type="text" class="form-control" placeholder="Sales" value="{{ $items->price_sales }}">
			</div>			

		</div>
		<div class="col-md-3"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Whole</span>
				<input type="text" class="form-control" placeholder="Whole" value="{{ $items->price_opt }}">
			</div>				
		</div>
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Base</span>
				<input type="text" class="form-control" placeholder="Base" value="{{ $items->price_base }}">
			</div>				
		</div>		
	</div>
	<div class="row">
		<div class="col-md-2"><p></p>
			@if($items->star == true)
			<button type="button" class="btn btn-warning" title="Make regular" data-toggle="tooltip" data-placement="bottom">
				<span class="glyphicon glyphicon-heart warning-icon" 
					  aria-hidden="true" title="Star"></span>&nbsp; Highlighted</button>		
			@else
			<button type="button" class="btn btn-default" title="Highlight!" data-toggle="tooltip" data-placement="bottom">
				<span class=" glyphicon glyphicon-heart-empty warning-icon" 
					  aria-hidden="true" title="Star"></span>&nbsp; Regular</button>	
			@endif
		</div>
		<div class="col-md-2"><p></p>
			@if($items->download == true)
			<button type="button" class="btn btn-info" title="Digital product" data-toggle="tooltip" data-placement="bottom">
				<span class="glyphicon glyphicon-floppy-save" 
					  aria-hidden="true" title="Star"></span>&nbsp; Digital</button>		
			@else
			<button type="button" class="btn btn-default" title="Make downloadable" data-toggle="tooltip" data-placement="bottom">
				It it digital?</button>	
			@endif
		</div>
		<div class="col-md-3">
			<p></p>
			Sales price time range (m/d/y):
			<div class="input-daterange input-group" id="datepicker">
				<input type="text" class="input-sm form-control" name="start" 
					   value="{{ Carbon\Carbon::parse($items->price_sales_on)->format('m/d/Y') }}"/>
				<span class="input-group-addon">to</span>
				<input type="text" class="input-sm form-control" name="end" 
					   value="{{ Carbon\Carbon::parse($items->price_sales_off)->format('m/d/Y') }}"/>
			</div>
			@if(now() >= ($items->price_sales_on) && now() <= ($items->price_sales_off))
			<span class="label label-success">currently active</span>
			@elseif (now() > ($items->price_sales_on) && now() > ($items->price_sales_off))
			<span class="label label-default">past</span>
			@elseif (now() < ($items->price_sales_on) && now() < ($items->price_sales_off))
			<span class="label label-warning">pending</span>
			@else
			<span class="label label-danger">unknown</span>
			@endif
		</div>
		<div class="col-md-5">
			<p></p>
			<strong>Visible since:</strong>
				<div class="row">
					<div class="col-xs-6">
				<input type="text" class="form-control date-container" 
					   placeholder="Month/Day/Year" value="{{ Carbon\Carbon::parse($items->to_show)->format('m/d/Y') }}"/>
					</div>
					<div class="col-xs-3 narrower">
						<input type="text" class="form-control" placeholder="Hour" value="{{ Carbon\Carbon::parse($items->to_show)->format('H') }}">
					</div>
					<div class="col-xs-3 narrower">
						<input type="text" class="form-control" placeholder="Min" value="{{ Carbon\Carbon::parse($items->to_show)->format('i') }}">
					</div>
				</div>
			@if(now() >= ($items->to_show))
			<span class="label label-success">visible</span>
			@else
			<span class="label label-warning">pending</span>
			@endif
		</div>
	</div>
	<div class="row">
		<div class="col-sm-8"><p></p>
				<textarea class="form-control" rows="5" placeholder="Description">{{ $items->descr }}</textarea></div>
		<div class="col-sm-4"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Qty</span>
				<input type="text" class="form-control" placeholder="Qty" value="{{ $items->qty }}">
			</div>				
			<p></p>
			<div class="input-group">
				<span class="input-group-addon">Weight @if($items->weight <= 0)
						&nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No weight!"></span>
						@endif</span>
				<input type="text" class="form-control" placeholder="Weight" value="{{ $items->weight }}">
			</div>	
			<p></p>
			<div class="input-group">
				<span class="input-group-addon">Production Code</span>
				<input type="text" class="form-control" placeholder="Production code" value="{{ $items->production_code }}">
			</div>	
			<p></p>
			<div class="input-group">
				<span class="input-group-addon">Score</span>
				<input type="text" class="form-control" placeholder="Score" value="{{ $items->score }}">
			</div>	
		</div>
	</div>
	<div class="row">
		<div class="col-sm-8"><p></p>
		@if(count($items->images)>0)	
			<h3><strong>Images</strong></h3>
			<div class="row">
				<div class="col-md-6">
					<input class="input-files-enhance" type="file" id="InFile1" name="InFile1" multiple=false>
				</div>
				<div class="col-md-6">
					<input class="form-control" placeholder=":Existing Images IDs[,]">
				</div>				
			</div>
			<p></p>
			@include($template.'.lists.images', array('items' => $items->images))
			<div class="rowdelimiter"></div>
		@endif
		@if(count($items->downloads)>0)	
			<h3><strong>Files</strong></h3>
			<div class="row">
				<div class="col-md-6">
					<input class="input-files-enhance" type="file" id="InFile2" name="InFile2" multiple=false>
				</div>
				<div class="col-md-6">
					<input class="form-control" placeholder=":Existing Files IDs[,]">
				</div>				
			</div>
			<p></p>
			<ul class="list-group">
			@foreach($items->downloads as $file)
			<li class="list-group-item">
				<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
				<small>#{{ $file->id }}:</small>
				@if($file->original == true) 
				<span class="label label-default">original</span>
				@else
				<span class="label label-success">copy</span>
				@endif
				<strong> {{ $file->fname }} </strong>
				@if(!empty($file->secret))
				<span class="label label-success"><a href='{{ asset('/download/'.$file->secret) }}'>download link</a></span>
				@endif
				<span class="label label-success"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>{{ $file->downloads }}</span>
				<span class="label label-info">
					lefts {{ $file->expires }}</span>
				@if(Carbon\Carbon::parse($file->expiration_day)->timestamp > 0)
				<span class="label label-default">expiration {{ Carbon\Carbon::parse($file->expiration_day)->format('d M Y, H:i'); }}</span>
				@endif
				</small>				
			</li>
			@endforeach
			</ul>
			<div class="rowdelimiter"></div>
		@endif
		
		<h3><strong>Categories</strong> @if(count($items->categories) <= 0)
		<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
		@endif</h3>

		<ul class="list-group">
			@foreach ($items->categories as $category)	
			<li class="list-group-item">
				<span class="badge">{{ $category->views }}</span>
				<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
				<a href="{{ route('admin.show', array('categories', 'category' => $category->id)) }}">{{ $category->title }}</a> 
				<small>{{ $category->remote_url }}</small>
			</li>	
			@endforeach
			<li class="list-group-item">
					<input type="text" class="form-control" placeholder=":Existings IDs[,]">
			</li>
		</ul>
		<div class="rowdelimiter"></div>
		<div class="row">
			<div class="col-md-12">  
				<label>Connected Pages</label>
				<ul class="list-group">
					@foreach ($items->pages as $page)	
					<li class="list-group-item">
						<span class="badge">{{ $page->views }}</span>
						<button type="button" class="btn btn-warning btn-xs">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
						<a href="{{ route('admin.show', array('pages', 'id' => $page->id)) }}">{{ $page->title }}</a> 
						<small>{{ Carbon\Carbon::parse($page->created_at)->format('d M Y'); }}</small>
					</li>	
					@endforeach
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
					@foreach ($items->parentproducts as $prd)	
					<li class="list-group-item">
						<span class="badge">{{ $prd->viewed }}</span>
						<button type="button" class="btn btn-warning btn-xs">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
						<a href="{{ route('admin.show', array('products', 'id' => $prd->id)) }}">{{ $prd->title }}</a> 
						<small>{{ app('veershop')->getPrice($prd, true) }}</small>
					</li>	
					@endforeach
					<li class="list-group-item">
							<input type="text" class="form-control" placeholder=":Existings IDs[,]">
					</li>
				</ul>	
			</div>
			<div class="col-md-6"> 
				<label>Sub products</label>
				<ul class="list-group">
					@foreach ($items->subproducts as $prd)	
					<li class="list-group-item">
						<span class="badge">{{ $prd->viewed }}</span>
						<button type="button" class="btn btn-warning btn-xs">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
						<a href="{{ route('admin.show', array('products', 'id' => $prd->id)) }}">{{ $prd->title }}</a> 
						<small>{{ app('veershop')->getPrice($prd, true) }}</small>
					</li>	
					@endforeach
					<li class="list-group-item">
							<input type="text" class="form-control" placeholder=":Existings IDs[,]">
					</li>
				</ul>	 
			</div>			
		</div> 
		
		</div>	
		<div class="col-sm-4"><p></p>
			<textarea class="form-control" rows="5" placeholder="Tags (One per row)">@foreach($items->tags as $tag)
{{ $tag->name }}

@endforeach</textarea>
			<div class="rowdelimiter"></div>
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
							   value="{{ $attribute->pivot->product_new_price }}" placeholder="New price"></strong>
				<p></p><button type="button" class="visible-xs visible-sm btn btn-default btn-xs">Update</button></div>
			</div>
		</div>
	</div>
	<p></p>
	<button type="button" class="btn btn-danger btn-lg btn-block">Update</button>

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
	
<!--	
<p>{{ $items->grp }}</p>
<p>{{ $items->grp_ids }}</p>
// TODO: deprecated?
-->
</div>
@stop