@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>		
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Users :{{ array_pull($items, 'counted') }} <small>sort by created | name</small></h1>
<br/>
<div class="container">
	@foreach($items as $user)
	
	{{ $user->id }} <br/>
	{{ $user->sites_id }} <br/>
	{{ $user->email }} <br/>
	{{ $user->password }} <br/>
	{{ $user->roles_id }} <br/>
	{{ $user->gender }} <br/>
	{{ $user->firstname }} <br/>
	{{ $user->lastname }} <br/>
	{{ $user->birth }} <br/>
	{{ $user->phone }} <br/>
	{{ $user->logons_count }} <br/>
	{{ $user->orders_count }} <br/>
	{{ $user->newsletter }} <br/>
	{{ $user->banned }} <br/>
	{{ $user->restrict_orders }} <br/>
	{{ $user->remember_token }} <br/>
	{{ $user->created_at }} <br/>
	{{ $user->updated_at }} <br/>
	
	@endforeach
	
	
	<div class="row">
		@foreach($items as $key => $item)	
		@if(round($key/6) == ($key/6)) <div class="clearfix"></div> @endif		
		<div class="col-lg-2 col-md-3 col-sm-6 text-center">
			<div class="thumbnail @if($item->banned == true)
				 bg-muted
				 @endif ">
				@if(count($item->images)>0)
				<a href="{{ asset(config('veer.images_path').'/'.$item->images[0]->img) }}" target="_blank">
					<img data-src="holder.js/100%x150/text:Not Found" 
						 src="{{ asset(config('veer.images_path').'/'.$item->images[0]->img) }}" class="img-responsive 
						 @if($item->hidden == true) image-faded @endif"></a>
				@else
				<img data-src="holder.js/100%x150/vine/text:{{ $item->title }}" 
						 class="img-responsive @if($item->hidden == true) image-faded @endif">
				@endif		 
				<div class="caption @if($item->hidden == true) image-faded @endif">
					<a href="{{ route('admin.show', array("pages", "id" => $item->id)) }}"><strong>{{ $item->title }}</strong></a>
					<p>{{ Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}<Br/>
						<small>#{{$item->id}} 
						@if(count($item->comments) > 0)
						&nbsp;<span class="glyphicon glyphicon-comment" aria-hidden="true" title="Comments"></span> {{ count($item->comments) }}
						@endif
						@if(count($item->subpages) > 0)
						&nbsp;<span class="glyphicon glyphicon-asterisk" aria-hidden="true" title="Sub pages"></span> {{ count($item->subpages) }}
						@endif
						@if(count($item->categories) <= 0)
						&nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
						@else
						&nbsp;<span class="glyphicon glyphicon-heart warning-icon" aria-hidden="true" title="Categories"></span> {{ 
							count($item->categories) }}
						@endif
						@if(is_object($item->user))
						<br/><a href="{{ route('admin.show', array('users', 'id' => $item->user->id)) }}">{{ '@'.$item->user->firstname }}</a>
						@endif
						</small></p>
					@if ($item->hidden == false)
					<button type="submit" name="action" value="changeStatusPage.{{ $item->id }}" class="btn btn-success btn-xs" title="Current: ON (SHOW)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
					@else
					<button type="submit" name="action" value="changeStatusPage.{{ $item->id }}" class="btn btn-warning btn-xs" title="Current: OFF (HIDDEN)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span></button>
					@endif
					@if(!isset($denyDelete) || !$denyDelete)
					&nbsp;<button type="submit" name="action" value="deletePage.{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					@else
					&nbsp;<button type="submit" name="action" value="removePage.{{ $item->id }}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					@endif
				</div>
			</div>
			@if($item->original == true || (File::exists( config('veer.htmlpages_path') . '/' . $item->id . '.html'))) 
			<div class="top-panel">
				@if($item->original == true)
					<span class="glyphicon glyphicon-th" aria-hidden="true"></span>
				@else
					<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
				@endif
			</div>
			@endif
			<div class="top-panel-right">
					<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $item->views }}&nbsp;
			</div>
		</div>
		@endforeach	
	</div>
	
</div>
@stop