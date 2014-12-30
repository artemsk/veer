@extends($template.'.layout.base')

@section('body')			
	<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><strong><a href="{{ route("admin.show", "orders") }}">Orders</a></strong></li>	
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
	</ol> 
<h1>Order #{{ $items->id or '—' }} <small>
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
				<input class="form-control" type="tel" name="fill[phone]" value="{{ $items->phone or null }}" placeholder="Phone">
			</div>
		</div>			
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">Gender</span>
				<select class="form-control" name="fill[gender]">
					<option value="{{ $items->gender or null }}">
						@if(isset($items->gender) && $items->gender == "m") Male @elseif(isset($items->gender) && $items->gender == "f") Female @else
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
			<input type="checkbox" class="page-checkboxes" name="fill[restrict_orders]" data-on-color="warning" data-on-text="Restrict&nbsp;orders&nbsp;&nbsp;" data-off-color="info" data-off-text="Allow&nbsp;orders" @if(isset($items->restrict_orders) && $items->restrict_orders == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[newsletter]" data-on-color="info" data-on-text="Newsletter" data-off-text="No&nbsp;subscriptions&nbsp;&nbsp;" @if(isset($items->newsletter) && $items->newsletter == true) checked @endif></div>							   
		</div>			
	</div>
	<div class="row">	
		<div class="col-md-4"><p></p>
			<select class="form-control" name="fill[roles_id]" placeholder="Role">
				@if(isset($items->role) && is_object($items->role))
					<option value="{{ $items->roles_id }}">{{ $items->role->role }}</option>
				@endif
					<option value=""></option>
				@if(isset($items->site) && is_object($items->site) && count($items->site->roles) > 0)		
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
				<input type="text" name="fill[sites_id]" class="form-control" placeholder="Sites Id" value="{{ $items->sites_id or null }}">
			</div>
		<small>@if(isset($items->site) && is_object($items->site))~ {{ $items->site->configuration->first()->conf_val or $items->site->url; }} @endif</small>
		</div>
		<div class="col-md-4"><p></p>
			<input type="text" name="fill[password]" class="form-control" placeholder="New password" value="">
		</div>			
	</div>
	
	@if(isset($items->administrator) && is_object($items->administrator))
	<h3><strong>Administrator</strong> <small> logins: {{ $items->administrator->logons_count or '—' }} &nbsp; <span class="label label-default">{{ $items->administrator->updated_at }}</span></small></h3>
	<div class="row">
		<div class="col-md-4">
			<input type="text" class="form-control" name="administrator[description]" value="{{ $items->administrator->description }}" placeholder="Description">
		</div>	
		<div class="col-md-4">
			<textarea class="form-control" name="administrator[access]" placeholder="Access parameters">{{ $items->administrator->access_parameters }}</textarea>
		</div>	
		<div class="col-md-2">
			<textarea class="form-control" name="administrator[sites]" placeholder="Sites watch">{{ $items->administrator->sites_watch }}</textarea>
		</div>			
		<div class="col-md-2">
			<div class="page-checkboxes-box">
				<input type="checkbox" class="page-checkboxes" name="administrator[banned]" data-on-color="warning" data-on-text="Banned<br/>Admin" data-off-text="Active<br/>Admin" data-off-color="info" @if(isset($items->administrator->banned) && $items->administrator->banned == true) checked @endif></div>
		</div>
	</div>
	@else
	<div class="rowdelimiter"></div>
	<div class="page-checkboxes-box">
		<input type="checkbox" class="page-checkboxes" name="addAsAdministrator" data-on-color="info" data-on-text="User will be administrator<br/> after save | update" data-off-text="Turn on to add user as administrator"></div>
	@endif
	
	<div class="rowdelimiter"></div>
	
	<div class="row">
		<div class="col-sm-12">
			<p></p>	
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
		</div>
	</div>
	
	
	<h3><strong>Pages</strong></h3>
	<ul class="list-group">
		@if(isset($items->pages) && count($items->pages)>0)	
		@foreach ($items->pages as $p)	
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
	<div class="rowdelimiter"></div>
	
	<h3><strong>Books <small>addresses</small></strong></h3>
	@if(isset($items->books) && count($items->books)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.books', array('items' => $items->books, 'skipUser' => true))
		</div>
	</div>
	@endif
	
	<h3><strong>Discounts</strong></h3>
	@if(isset($items->discounts) && count($items->discounts)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.discounts', array('items' => $items->discounts))
		</div>
	</div>
	@endif
	
	<h3><strong>Orders</strong></h3>
	@if(isset($items->orders) && count($items->orders)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.orders', array('items' => $items->orders, 'skipUser' => true))
		</div>
	</div>
	@endif
	
	<h3><strong>Bills</strong></h3>
	@if(isset($items->bills) && count($items->bills)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.bills', array('items' => $items->bills, 'skipUser' => true))
		</div>
	</div>
	@endif	

	@if(isset($items->id) && isset($items['files']) && count($items['files'])>0)	
	<h3><strong>Files</strong></h3>
	<div class="row">
		<div class="col-sm-12">
		@include($template.'.lists.files', array('files' => $items['files'], 'skipUser' => true))
		</div>
	</div>
	@endif

	<div class="rowdelimiter"></div>
	@if(isset($items->id))
	<div class="row">
		<div class="col-sm-12"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">	
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "order", "filter_id" => $items->id)) }}"
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