@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "users") }}">Users</a></strong></li>
		@else
		<li><strong>Users</strong></li>
		@endif
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>		
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Users :{{ array_pull($items, 'counted') }} <small>
	@if(Input::get('filter',null) != null) 
	filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong> | 
	@endif		
		sort by <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "created_at", "direction" => "desc")) }}">created</a> | <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "lastname", "direction" => "asc")) }}">lastname</a> | <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "email", "direction" => "asc")) }}">email</a> | <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "birth", "direction" => "desc")) }}">birthday</a></small></h1>
<br/>
<div class="container">
	
	<div class="row">
		@foreach($items as $key => $item)	
		@if(round($key/6) == ($key/6)) <div class="clearfix"></div> @endif		
		<div class="col-lg-2 col-md-3 col-sm-6 text-center">
			<div class="thumbnail thumbnail-user @if($item->banned == true) bg-muted @endif ">
				@if(count($item->images)>0)
				<a href="{{ asset(config('veer.images_path').'/'.$item->images->first()->img) }}" target="_blank">
					<img data-src="holder.js/100%x150/text:Not Found" 
						 src="{{ asset(config('veer.images_path').'/'.$item->images->first()->img) }}" class="img-responsive 
						 @if($item->banned == true) image-faded @endif"></a>
				@endif		 
				<div class="caption @if($item->banned == true) image-faded @endif">
					<strong>{{ $item->firstname }} {{ $item->lastname }}</strong>
					<p><a href="{{ route('admin.show', array("users", "id" => $item->id)) }}">{{ str_limit($item->email,25) }}</a><br/>
						<small>
						{{ !empty($item->phone) ? str_limit($item->phone,25)."<br/>" : null }}	
						@if($item->gender == "f")
						<i class="fa fa-female"></i>
						@elseif($item->gender == "m")
						<i class="fa fa-male"></i>
						@else
						@endif
						{{ Carbon\Carbon::parse($item->birth)->format('Y') }}
						@if($item->roles_id > 0)
						<strong>&nbsp;<a href="{{ route('admin.show', array("users", "filter" => "role", "filter_id" => $item->roles_id)) }}">{{ $item->role->role }}</a></strong>
						@endif
						<br/>
						#{{$item->id}} 
						@if($item->restrict_orders == true)
						&nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="Restrict orders!"></span>
						@endif
						@if($item->newsletter == true)
						&nbsp;<i class="fa fa-file-text-o" title="Subscribed to newsletter"></i>
						@endif
						@if($item->logons_count > 0)
						&nbsp;<span class="glyphicon glyphicon-asterisk" aria-hidden="true" title="Logins count"></span> {{ count($item->logons_count) }}
						@endif
						@if($item->orders_count > 0)
						&nbsp;<span class="glyphicon glyphicon-glass" aria-hidden="true" title="Orders count"></span> {{ count($item->orders_count) }}
						@endif	
						<br/><a href="{{ route('admin.show', array("users", "filter" => "site", "filter_id" => $item->site->id)) }}">
							{{ $item->site->configuration->first()->conf_val or $item->site->url; }}</a>
						</small>
					</p>
					@if ($item->restrict_orders == false)
					<button type="submit" name="action" value="changeRestrictUser.{{ $item->id }}" class="btn btn-success btn-xs" title="Current: Allow orders" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span></button>
					@else
					<button type="submit" name="action" value="changeRestrictUser.{{ $item->id }}" class="btn btn-warning btn-xs" title="Current: Restrict orders" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-minus-sign " aria-hidden="true"></span></button>
					@endif					
					@if ($item->banned == false)
					&nbsp;<button type="submit" name="action" value="changeStatusUser.{{ $item->id }}" class="btn btn-success btn-xs" title="Current: Active" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span></button>
					@else
					&nbsp;<button type="submit" name="action" value="changeStatusUser.{{ $item->id }}" class="btn btn-warning btn-xs" title="Current: BANNED" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span></button>
					@endif
					&nbsp;<button type="submit" name="action" value="deleteUser.{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				</div>
			</div>
			<span class="text-muted"><small>{{ $item->created_at }}</small></span>
		</div>
		@endforeach	
	</div>
	
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
					'sort' => Input::get('sort', null),
					'direction' => Input::get('direction', null)
				))->links() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put', 'files' => true)); }}
	<label>Quick form: Add page</label>
	<div class="row">
		<div class="col-sm-4"><p><input type="text" class="form-control" placeholder="Title" name="title"></p></div>
		<div class="col-sm-4"><p><input type="text" class="form-control" placeholder="Categories Id [,]" name="categories"></p></div>
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="attachImage" multiple=false>Image</p></div>
	</div>	
	<div class="xs-rowdelimiter"></div>
	<div class="row">
		<div class="col-sm-8"><p><input type="text" class="form-control" placeholder="[Url]" name="url"></p></div>
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="attachFile" multiple=false>Attach file (*.html for full replacement)</p></div>
	</div>	
	<div class="row">
		<div class="col-sm-6"><p>
			<textarea class="form-control" placeholder="@{{Small txt}} Txt" rows="10" name="txt"></textarea></p>			
		</div>
		<div class="col-sm-6">
			<p>{{ Form::submit('Add', array('class' => 'form-control btn btn-danger')); }}</p>
		</div>
	</div>
	{{ Form::close() }}	
	
</div>
@stop