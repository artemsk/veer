@extends($template.'.layout.base')

@section('body')			

	@include($template.'.layout.breadcrumb-user', array('place' => 'user'))
	
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
					<button type="submit" value="1" name="changeStatusUser[{{ $items->id }}]" class="btn btn-success" title="Current: ON (Active)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Active</button>
					@else
					<button type="submit" value="0" name="changeStatusUser[{{ $items->id }}]" class="btn btn-warning" title="Current: BANNED " data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Banned</button>
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
			<input type="password" name="fill[password]" class="form-control" placeholder="New password" value="">
		</div>			
	</div>
	
	@if(isset($items->administrator) && is_object($items->administrator))
	<h3>Administrator <small> logins: {{ $items->administrator->logons_count or '—' }} &nbsp; <span class="label label-default">{{ $items->administrator->updated_at }}</span></small></h3>
	<div class="row">
		<div class="col-md-4">
			<input type="text" class="form-control" name="administrator[description]" value="{{ $items->administrator->description }}" placeholder="Description">
		</div>	
		<div class="col-md-4">
			<textarea class="form-control" name="administrator[access_parameters]" placeholder="Access parameters">{{ $items->administrator->access_parameters }}</textarea>
		</div>	
		<div class="col-md-2">
			<textarea class="form-control" name="administrator[sites_watch]" placeholder="Sites watch">{{ $items->administrator->sites_watch }}</textarea>
		</div>			
		<div class="col-md-2">
			<div class="page-checkboxes-box">
				<input type="checkbox" class="page-checkboxes" name="administrator[banned]" data-on-color="warning" data-on-text="Banned<br/>Admin" data-off-text="Active<br/>Admin" data-off-color="info" @if(isset($items->administrator->banned) && $items->administrator->banned == true) checked @endif></div>
		</div>
	</div>
	@else
	<div class="sm-rowdelimiter"></div>
	<div class="page-checkboxes-box">
		<input type="checkbox" class="page-checkboxes" name="addAsAdministrator" data-on-color="info" data-on-text="User will be administrator<br/> after save | update" data-off-text="Turn on to add user as administrator"></div>
	@endif
	
	<div class="rowdelimiter"></div>
	
	<div class="row">
		<div class="col-sm-12">
			<p></p>	
			<h3>Images</h3>
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
	<div class="rowdelimiter"></div>
	
	<h3>Pages</h3>
	<ul class="list-group">
		@if(isset($items->pages) && count($items->pages)>0)	
		@foreach ($items->pages as $p)	
		<li class="list-group-item">
			<span class="badge">{{ $p->views }}</span>
			<button type="submit" name="action" value="deletePage.{{ $p->id }}" class="btn btn-danger btn-xs">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
			<button type="submit" name="action" value="removePage.{{ $p->id }}" class="btn btn-warning btn-xs">
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
			<a href="{{ route('admin.show', array('pages', 'id' => $p->id)) }}">{{ $p->title }}</a> 
			<small>{{ Carbon\Carbon::parse($p->created_at)->format('d M Y'); }}</small>
		</li>	
		@endforeach
		@endif
		<li class="list-group-item">
			<input type="text" name="attachPages" class="form-control input-no-borders" placeholder=":Existings IDs[,]">	
		</li>
	</ul>
	@if(isset($items->id))
	<a class="btn btn-default" href="{{ route("admin.show", array("pages", 
				"id" => "new", "user" => isset($items->id) ? $items->id : null)) }}" role="button" target="_blank">New page</a>
	@endif
	<div class="rowdelimiter"></div>
	
	@if(isset($items->id))
	<h3>Books <small>addresses | <a href="#" data-toggle="modal" data-target="#bookModalNew">new book</a></small></h3>
	@if(isset($items->books) && count($items->books)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.books', array('items' => $items->books, 'skipUser' => true))
		</div>
	</div>
	@endif
	<div class="modal fade" id="bookModalNew">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">And new user's book</h4>
				</div>
				<div class="modal-body">
					@include($template.'.layout.form-userbook', array('item' =>array(), 'skipSubmit' => true, 'UsersId' => $items->id))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" name="action" value="addUserbook" class="btn btn-primary">Save changes</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="rowdelimiter"></div>
	@endif
	
@if(isset($items->id))
	
	<h3>Discounts <small><a href="{{ route("admin.show", array("discounts", "filter" => "user", "filter_id" => $items->id)) }}" target="_blank">create</a></small></h3>
	<ul class="list-group">
	@if(isset($items->discounts) && count($items->discounts)>0)
		@include($template.'.lists.discounts', array('items' => $items->discounts))
	@endif
	<li class="list-group-item">
		<input type="text" name="attachDiscounts" class="form-control input-no-borders" placeholder=":Existings IDs[,]">	
	</li>
	</ul>	
	
	<div class="rowdelimiter"></div>
	
	<h3>Orders</h3>
	@if(isset($items->orders) && count($items->orders)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.orders', array('items' => $items->orders, 'skipUser' => true))
		</div>
	</div>
	@endif
	<a class="btn btn-default" href="{{ route("admin.show", array("orders", 
				"id" => "new", "user" => isset($items->id) ? $items->id : null)) }}" role="button" target="_blank">New order</a>
	
	<div class="rowdelimiter"></div>
	<h3>Bills</h3>
	@if(isset($items->bills) && count($items->bills)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.bills', array('items' => $items->bills, 'skipUser' => true))
		</div>
	</div>
	@endif	
	<a class="btn btn-default" href="#" data-toggle="modal" data-target="#billModalNew">New bill</a>
	<div class="modal fade" id="billModalNew">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Create new bill</h4>
				</div>
				<div class="modal-body">
					@include($template.'.layout.form-bill', array('skipSubmit' => true))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" name="addNewBill" value="New" class="btn btn-primary">Save changes</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	@if(isset($items->id) && isset($items['files']) && count($items['files'])>0)
	<div class="rowdelimiter"></div>
	<h3>Files</h3>
	<div class="row">
		<div class="col-sm-12">
		@include($template.'.lists.files', array('files' => $items['files'], 'skipUser' => true))
		</div>
	</div>
	@endif

	<div class="rowdelimiter"></div>

	<div class="row">
		<div class="col-sm-2"><button type="button" class="btn btn-default btn-lg btn-block"  data-toggle="modal" data-target="#communicationModal">Send message</button></div>
		<div class="col-sm-10"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">
			@if(isset($items['basket']))
			<a class="btn btn-info" href="{{ route('admin.show', array('lists', "filter" => "user", "filter_id" => $items->id)) }}" 
			   role="button">{{ $items['basket'] }} in baskets</a>
			@endif
			@if(isset($items['lists']))
			<a class="btn btn-default" href="{{ route('admin.show', array('lists', "filter" => "user", "filter_id" => $items->id)) }}" 
			   role="button">{{ $items['lists'] }} in lists</a>		
			@endif	
			<a class="btn btn-default" href="{{ route('admin.show', array('searches', "filter" => "users", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->searches()->count() }} searches</a>
			<a class="btn btn-default" href="{{ route('admin.show', array('comments', "filter" => "user", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->comments()->count() }} comments</a>
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "user", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->communications()->count() }} communications</a>
		</div>
	</div>
	<div class="modal fade" id="communicationModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Send message</h4>
				</div>	
				<div class="modal-body">
					@include($template.'.layout.form-communication', array('send2UserId' => $items->id, 
						'send2Username' => $items->username, 'emailOn' => true))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" value="{{ $items->id }}" name="sendMessageToUser" class="btn btn-primary">Send message</button>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
@else
	<button type="submit" name="action" value="add" class="btn btn-danger btn-lg btn-block">Add</button>
@endif

</div>
@if(isset($items->id))
<div class="action-hover-box"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
@endif
</form>
@stop