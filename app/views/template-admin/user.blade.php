@extends($template.'.layout.base')

@section('body')			
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><strong><a href="{{ route("admin.show", "users") }}">Users</a></strong></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>		
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>User #{{ $items->id or '—' }} <small>
		&nbsp; <nobr><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span> {{ $items->logons_count or '—' }}</nobr>
		&nbsp; <nobr><span class="glyphicon glyphicon-glass" aria-hidden="true"></span> {{ $items->orders_count or '—' }}</nobr></small></h1>
<br/>
{{ Form::open(array('url' => URL::full(), 'files' => true, 'method' => 'put')); }}
<div class="container">

	<div class="row">
		<div class="col-sm-6"><p><input type="text" class="form-control" name="fill[email]" placeholder="Email" value="{{ $items->email or null }}"></p></div>
		<div class="col-sm-2 col-xs-6 text-center"><p>
				@if(isset($items->banned))
					@if ($items->banned == false)
					<button type="submit" name="action" value="changeStatusUser.{{ $items->id }}" class="btn btn-success" title="Current: ON (Active)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Active</button>
					@else
					<button type="submit" name="action" value="changeStatusUser.{{ $items->id }}" class="btn btn-warning" title="Current: BANNED " data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Banned</button>
					@endif
				@endif	
			</p></div>		
		<div class="col-sm-2 col-xs-6"><p>created at<br/><strong>{{ !empty($items->created_at) ? 
			Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>
		<div class="col-sm-2 col-xs-12"><p>updated at<br/><strong>{{ !empty($items->created_at) ? Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>	
	</div>
	<div class="row">
		<div class="col-sm-4"><p><strong><input type="text" class="form-control input-lg" placeholder="Username" name="fill[username]" value="{{ $items->username or null }}"></strong></p></div>
		<div class="col-sm-4"><p><strong><input type="text" class="form-control input-lg" placeholder="Firstname" name="fill[firstname]" value="{{ $items->firstname or null }}"></strong></p></div>
		<div class="col-sm-4"><p><strong><input type="text" class="form-control input-lg" placeholder="Lastname" name="fill[lastname]" value="{{ $items->lastname or null }}"></strong></p></div>
	</div>
	<div class="row">
		<div class="col-md-3"><p></p>
			<div class="input-group">
				<span class="input-group-addon"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span></span>
				<input class="form-control" type="tel" name="fill[phone]" value="{{ $items->phone }}" placeholder="Phone">
			</div>
		</div>			
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Gender</span>
				<select class="form-control" name="fill[gender]">
					<option value="{{ $items->gender}}">
						@if($items->gender == "m") Male @elseif($items->gender == "f") Female @else
						— @endif</option>
					<option value="">—</option>
					<option value="m">m</option>
					<option value="f">f</option>
				</select>
			</div>
		</div>
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Birth</span>
				<input type="text" class="form-control date-container" name="fill[birth]"
					   placeholder="Month/Day/Year" value="{{ !empty($items->birth) ? Carbon\Carbon::parse($items->birth)->format('m/d/Y') : '' }}"/>
			</div>
		</div>
		<div class="col-md-5"><p></p>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[restrict_orders]" data-on-color="warning" data-on-text="Restrict&nbsp;orders&nbsp;&nbsp;" data-off-text="Allow&nbsp;orders" @if(isset($items->restrict_orders) && $items->restrict_orders == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[newsletter]" data-on-color="info" data-on-text="Newsletter" data-off-text="No&nbsp;subscriptions&nbsp;&nbsp;" @if(isset($items->newsletter) && $items->newsletter == true) checked @endif></div>							   
		</div>			
	</div>
	<div class="row">	
		<div class="col-md-4"><p></p>
			<select class="form-control" name="fill[roles_id]" placeholder="Role">
				@if(is_object($items->role))
					<option value="{{ $items->roles_id }}">{{ $items->role->role }}</option>
				@endif
					<option value=""></option>
				@if(is_object($items->site) && count($items->site->roles) > 0)		
					@foreach($items->site->roles as $role)
						<option value="{{ $role->id }}">{{ $role->role }}</option>
					@endforeach
				@endif
			</select>
		</div>
		<div class="col-md-4"><p></p>
			<div class="input-group">
				<span class="input-group-addon">
				  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
				</span>
				<input type="text" name="fill[sites_id]" class="form-control" placeholder="Sites Id" value="{{ $items->sites_id }}">
			</div>
		<small>@if(is_object($items->site))~ {{ $items->site->configuration->first()->conf_val or $items->site->url; }} @endif</small>
		</div>
		<div class="col-md-4"><p></p>
			<input type="text" name="fill[password]" class="form-control" placeholder="New password" value="">
		</div>			
	</div>
	
	<div class="rowdelimiter"></div>
	
	<div class="row">		
		<div class="col-md-3">			
			<label>Free form</label>
			<textarea class="form-control" name="freeForm" rows="5" placeholder="[Tag:Ids,] [Attribute:Ids,]"></textarea>
			<div class="rowdelimiter"></div>
		</div>
		<div class="col-md-9">

			<div class="row">
			<div class="col-sm-12"><p></p>	
				<h3><strong>Images</strong></h3>
				<div class="row">
					<div class="col-md-6">
						<input class="input-files-enhance" type="file" id="InFile1" name="uploadImage" multiple=false>
					</div>
					<div class="col-md-6">
						<input class="form-control" name="attachImages" placeholder=":Existing Images IDs[,]">
					</div>				
				</div>
				@if(isset($items->images) && count($items->images)>0)			
				<p></p>
				@include($template.'.lists.images', array('items' => $items->images, 'denyDelete' => true))
				@endif
				<div class="rowdelimiter"></div>
				<h3><strong>Files</strong></h3>
				<div class="row">
					<div class="col-md-6">
						<input class="input-files-enhance" type="file" id="InFile2" name="uploadFiles" multiple=false>
					</div>
					<div class="col-md-6">
						<input class="form-control" name="attachFiles" placeholder=":Existing Files IDs[,]">
					</div>				
				</div>
				@if(isset($items->downloads) && count($items->downloads)>0)	
				<p></p>
				@include($template.'.lists.files', array('files' => $items->downloads, 'denyDelete' => true))
				@endif
				<div class="rowdelimiter"></div>


			<div class="row">
				<div class="col-md-6"> 
					<label>Sub pages</label>
					<ul class="list-group">
						@if(isset($items->subpages) && count($items->subpages)>0)	
						@foreach ($items->subpages as $p)	
						<li class="list-group-item">
							<span class="badge">{{ $p->views }}</span>
							<button type="submit" name="action" value="removeChildPage.{{ $p->id }}" class="btn btn-warning btn-xs">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
							<a href="{{ route('admin.show', array('pages', 'id' => $p->id)) }}">{{ $p->title }}</a> 
							<small>{{ Carbon\Carbon::parse($p->created_at)->format('d M Y'); }}</small>
						</li>	
						@endforeach
						@endif
						<li class="list-group-item">
								<input type="text" name="attachChildPages" class="form-control" placeholder=":Existings IDs[,]">
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
		<div class="col-sm-12"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">
			@if(isset($items['lists']))
			<a class="btn btn-default" href="{{ route('admin.show', array('lists', "filter" => "pages", "filter_id" => $items->id)) }}" 
			   role="button">{{ $items['lists'] }} lists</a>
			@endif						
			<a class="btn btn-default" href="{{ route('admin.show', array('comments', "filter" => "pages", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->comments()->count() }} comments</a>
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "pages", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->communications()->count() }} communications</a>
		</div>
	</div>
	@else
	<button type="submit" name="action" value="add" class="btn btn-danger btn-lg btn-block">Add</button>
	@endif
<!--	
<p>$items->grp</p>
<p>$items->grp_ids</p>
// TODO: deprecated?
-->
</div>
@if(isset($items->id))
<div class="action-hover-box"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
@endif
{{ Form::close() }}
@stop