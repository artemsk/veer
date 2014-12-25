@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "searches") }}">Searches</a></strong></li>
		@else
		<li class="active">Searches</li>
		@endif			
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Searches :{{ array_pull($items, 'counted', 0) }}
			@if(Input::get('filter',null) != null) 
			<small> | filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong></small>
			@endif </h1>
<br/>
<div class="container">
	<ul class="list-group">
	@foreach($items as $item)
	
		<li class="list-group-item bordered-row">
			<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
			<strong>{{ $item->q }}</strong>
			@if(count($item->users) > 0)
				<br/>
				<small>
				@foreach($item->users as $user)
				<a href="{{ route("admin.show", array("users", "id" => $user->id)) }}">{{ '@'.$user->username }}</a> 
				@endforeach
				</small>
			@endif
			<span class="badge">{{ $item->times }}</span>
		</li>
			
	@endforeach
	</ul>
	
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->links() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add search</label>
	<div class="row">
        <div class="col-md-6">             
            <div class="form-group">
                <input type="text" class="form-control" name="InSearch" placeholder="Search">
			</div>
            <div class="form-group">
                <input type="text" class="form-control" name="InUsers" placeholder="Users ID [:ids]">
			</div>
			<button type="submit" class="btn btn-default">Add</button>
        </div>  
    </div>
	{{ Form::close() }}	
</div>
@stop