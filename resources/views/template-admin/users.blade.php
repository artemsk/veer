@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-user', array('place' => 'users'))

<h1>Users :{{ array_pull($items, 'counted') }} <small>
	@if(Input::get('filter',null) != null) 
	filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong> | 
	@endif		
		sort by <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "created_at", "direction" => "desc")) }}">created</a> | <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "lastname", "direction" => "asc")) }}">lastname</a> | <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "email", "direction" => "asc")) }}">email</a> | <a href="{{ route("admin.show", array("users", "filter" => Input::get('filter',null), "filter_id" => Input::get('filter_id',null), "sort" => "birth", "direction" => "desc")) }}">birthday</a></small> <a class="btn btn-default" 
									   href="{{ route("admin.show", array("users", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="row">
		@foreach($items as $key => $item)	
		@if(round($key/6) == ($key/6)) <div class="clearfix"></div> @endif		
		<div class="col-lg-2 col-md-3 col-sm-6 text-center">
			<div class="thumbnail thumbnail-user @if($item->banned == true) bg-muted @endif @if(count($item->administrator) >0) thumbnail-admin @endif ">
				@if(count($item->images)>0)
				<a href="{{ route('admin.show', array("users", "id" => $item->id)) }}" target="_blank">
					<img data-src="holder.js/100%x150/text:Not Found" 
						 src="{{ asset(config('veer.images_path').'/'.$item->images->first()->img) }}" class="img-responsive 
						 @if($item->banned == true) image-faded @endif"></a>
				@endif		 
				<div class="caption @if($item->banned == true) image-faded @endif">
					<strong>{{ $item->firstname }} {{ $item->lastname }} <small>{{ !empty($item->username) ? '@'.$item->username : '' }}</small></strong>
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
						@if(count($item->comments) > 0)
						&nbsp;<span class="glyphicon glyphicon-bullhorn" aria-hidden="true" title="Comments"></span> {{ count($item->comments) }}
						@endif
						@if(count($item->communications) > 0)
						&nbsp;<span class="glyphicon glyphicon-comment" aria-hidden="true" title="Communications"></span> 
						{{ count($item->communications) }}
						@endif
						@if(count($item->pages) > 0)
						&nbsp;<i class="fa fa-pencil" title="Pages"></i> {{ count($item->pages) }}
						@endif
						<br/>@if(is_object($item->site)) 
						<a href="{{ route('admin.show', array("users", "filter" => "site", "filter_id" => $item->site->id)) }}">
							{{ $item->site->configuration->first()->conf_val or $item->site->url; }} </a>@endif
						</small>
					</p>
					@if ($item->restrict_orders == false)
					<button type="submit" value="1" name="changeRestrictUser[{{ $item->id }}]" class="btn btn-success btn-xs" title="Current: Allow orders" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span></button>
					@else
					<button type="submit" value="0" name="changeRestrictUser[{{ $item->id }}]" class="btn btn-warning btn-xs" title="Current: Restrict orders" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-minus-sign " aria-hidden="true"></span></button>
					@endif					
					@if ($item->banned == false)
					&nbsp;<button type="submit" value="1" name="changeStatusUser[{{ $item->id }}]" class="btn btn-success btn-xs" title="Current: Active" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span></button>
					@else
					&nbsp;<button type="submit" value="0" name="changeStatusUser[{{ $item->id }}]" class="btn btn-warning btn-xs" title="Current: BANNED" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span></button>
					@endif
					&nbsp;<button type="submit" value="1" name="deleteUser[{{ $item->id }}]" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				</div>
			</div>
			<span class="text-muted"><small>{{ $item->created_at }}</small></span>
		</div>
		@endforeach	
	</div>
	</form>
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
					'sort' => Input::get('sort', null),
					'direction' => Input::get('direction', null)
				))->render() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	<label>Quick form: Add user</label>
	<div class="row">
		<div class="col-sm-4"><p><input type="email" class="form-control" placeholder="Email" name="email"></p></div>
		<div class="col-sm-4"><p><input type="password" class="form-control" placeholder="Password" name="password"></p></div>
		<div class="col-sm-4"><p><input type="siteId" class="form-control" placeholder="Site Id" name="siteId"></p></div>
	</div>	
	<div class="xs-rowdelimiter"></div>
	<div class="row">
		<div class="col-sm-6"><p>
			<textarea class="form-control" placeholder="Username |
Phone |
First name |
Last name |
Birth |
Gender |
Roles Id |
Newsletter |
Restrict orders |
Ban" rows="10" name="freeForm" title="Username |
Phone |
First name |
Last name |
Birth |
Gender |
Roles Id |
Newsletter |
Restrict orders |
Ban"></textarea></p>			
		</div>
		<div class="col-sm-6">
			<p><input class="form-control btn btn-danger" type="submit" name="action" value="Add"></p>
		</div>
	</div>
	</form>
	
</div>
@stop