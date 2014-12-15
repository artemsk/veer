@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		<li><a href="{{ route("admin.show", "pages") }}"><strong>Pages</strong></a></li>
		<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
</ol>
<h1>Page #{{ $items->id or '—' }} <small>
		&nbsp; <nobr><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $items->views or '—' }}</nobr></small></h1>
<br/>
<div class="container">

	<div class="row">
		<div class="col-sm-6"><p><input type="text" class="form-control" placeholder="Clean Url" value="{{ $items->url or null }}"></p></div>
		<div class="col-sm-2 col-xs-6 text-center"><p>
				@if(isset($items->hidden))
					@if ($items->hidden == false)
					<button type="button" class="btn btn-success" title="Current: ON (SHOW)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span> Showing</button>
					@else
					<button type="button" class="btn btn-warning" title="Current: OFF (HIDDEN)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span> Hidden</button>
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
				<span class="input-group-addon"><span class="glyphicon glyphicon-sort" aria-hidden="true"></span></span>
				<input type="text" class="form-control" placeholder="Sort" value="{{ $items->manual_order or null }}">
			</div>
		</div>
		<div class="col-md-10"><p></p>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" data-on-color="warning" data-on-text="Original" data-off-text="Regular" 
				   @if(isset($items->original) && $items->original == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes"  data-on-color="info" data-on-text="Intro" data-off-text="No&nbsp;Intro" 
					   @if(isset($items->show_small) && $items->show_small == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes"  data-on-color="info" data-on-text="Comments" data-off-text="No&nbsp;Comments" 
					   @if(isset($items->show_comments) && $items->show_comments == true) checked @endif></div>			
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes"  data-on-color="info" data-on-text="Title" data-off-text="No&nbsp;Title" 
					   @if(isset($items->show_title) && $items->show_title == true) checked @endif></div>	
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes"  data-on-color="info" data-on-text="Date" data-off-text="No&nbsp;Date" 
					   @if(isset($items->show_date) && $items->show_date == true) checked @endif></div>
			<div class="page-checkboxes-box page-checkboxes-box-last">
			<input type="checkbox" class="page-checkboxes"  data-on-color="info" data-on-text="Listed" data-off-text="No&nbsp;Lists" 
					   @if(isset($items->in_list) && $items->in_list == true) checked @endif></div>						   
		</div>			
	</div>
	<div class="row">		
		<div class="col-md-2">
			<p></p>
			User 
			<input type="text" class="form-control" placeholder="User id" value="{{ $items->users_id or null }}">
			@if(isset($items->users_id) && $items->users_id > 0)
			@if(is_object($items->user))
			<a href="{{ route('admin.show', array('users', 'id' => $items->user->id)) }}">{{ '@'.$items->user->firstname }}</a>
			@else
			<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="bad user!"></span>
			@endif
			@endif
		</div>		
		<div class="col-md-6"><p></p>
			@if(isset($items->id) && File::exists( config('veer.htmlpages_path') . '/' . $items->id . '.html'))
			<div class="alert alert-success" role="alert">
				<strong>{{ $items->id }}.html</strong> file exists in "{{ config('veer.htmlpages_path') }}/.." folder.
			</div>
			@endif
		</div>
	</div>
	
	<div class="rowdelimiter"></div>
	
	<div class="row">		
		<div class="col-md-3">			
			<textarea class="form-control" rows="5" placeholder="Tags (One per row)">@if(isset($items->tags))
@foreach($items->tags as $tag)
{{ $tag->name }}

@endforeach
@endif</textarea>
			
			<div class="rowdelimiter"></div>
			@if(isset($items->attributes))
			@foreach($items->attributes as $attribute)
			<div class="row">
				<div class="col-md-12">
					<strong><input type="text" class="form-control input-sm" value="{{ $attribute->name }}" placeholder="Name"></strong>
					<p></p><input type="text" class="form-control input-sm" value="{{ $attribute->val }}" placeholder="Value">
				<p></p><textarea class="form-control input-sm" placeholder="Description">{{ $attribute->descr }}</textarea>
				<p></p><button type="button" class="btn btn-default btn-xs">Update</button>
				</div>				
			</div>
			<div class="rowdelimiter"></div>
			@endforeach
			@endif
			<div class="row">
				<div class="col-md-12">
					<strong><input type="text" class="form-control input-sm" placeholder="Name"></strong>
				<p></p><input type="text" class="form-control input-sm"placeholder="Value">
				<p></p><textarea class="form-control input-sm" placeholder="Description"></textarea>
				<p></p><button type="button" class="btn btn-default btn-xs">Update</button></div>
			</div>
			
			<div class="rowdelimiter"></div>
			
			<label>Free form</label>
			<textarea class="form-control" rows="5" placeholder="[Tag:Ids,] [Attribute:Ids,]"></textarea>
			<div class="rowdelimiter"></div>
		</div>
		<div class="col-md-9">
			<textarea class="form-control" rows="5" placeholder="Introduction text">{{ $items->small_txt or null }}</textarea>
			
			<div class="rowdelimiter"></div>
			
			<textarea class="form-control" rows="15" placeholder="Text">{{ $items->txt or null }}</textarea>
			
			<div class="rowdelimiter"></div>
			
			<div class="row">
			<div class="col-sm-12"><p></p>	
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
					<button type="button" class="btn btn-warning btn-xs">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
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
					<label>Connected Products</label>
					<ul class="list-group">
						@if(isset($items->products) && count($items->products)>0)	
						@foreach ($items->products as $prd)	
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
			<div class="row">
				<div class="col-md-6">                    
					<label>Parent pages</label>
					<ul class="list-group">
						@if(isset($items->parentpages) && count($items->parentpages)>0)	
						@foreach ($items->parentpages as $p)	
						<li class="list-group-item">
							<span class="badge">{{ $p->views }}</span>
							<button type="button" class="btn btn-warning btn-xs">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
							<a href="{{ route('admin.show', array('pages', 'id' => $p->id)) }}">{{ $p->title }}</a> 
							<small>{{ Carbon\Carbon::parse($p->created_at)->format('d M Y'); }}</small>
						</li>	
						@endforeach
						@endif
						<li class="list-group-item">
								<input type="text" class="form-control" placeholder=":Existings IDs[,]">
						</li>
					</ul>	
				</div>
				<div class="col-md-6"> 
					<label>Sub pages</label>
					<ul class="list-group">
						@if(isset($items->subpages) && count($items->subpages)>0)	
						@foreach ($items->subpages as $p)	
						<li class="list-group-item">
							<span class="badge">{{ $p->views }}</span>
							<button type="button" class="btn btn-warning btn-xs">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
							<a href="{{ route('admin.show', array('pages', 'id' => $p->id)) }}">{{ $p->title }}</a> 
							<small>{{ Carbon\Carbon::parse($p->created_at)->format('d M Y'); }}</small>
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
			</div>
		</div>
	</div>

	<div class="rowdelimiter"></div>
	@if(isset($items->id))
	<div class="row">
		<div class="col-sm-2 col-xs-6"><button type="button" class="btn btn-warning btn-lg btn-block">Save As</button></div>
		<div class="col-sm-10 col-xs-6"><button type="button" class="btn btn-danger btn-lg btn-block">Update</button></div>		
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">
			@if(isset($items['lists']))
			<a class="btn btn-default" href="{{ route('admin.show', array('lists', 'page' => $items->id)) }}" 
			   role="button">{{ $items['lists'] }} lists</a>
			@endif						
			<a class="btn btn-default" href="{{ route('admin.show', array('comments', 'page' => $items->id)) }}"
			   role="button">{{ $items->comments()->count() }} comments</a>
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', 'page' => $items->id)) }}"
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