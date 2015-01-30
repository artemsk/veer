@extends($template.'.layout.base')

@section('body')
	
	@include($template.'.layout.breadcrumb-user', array('place' => 'searches'))

<h1>Searches :{{ array_pull($items, 'counted', 0) }}
			@if(Input::get('filter',null) != null) 
			<small> | filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong></small>
			@endif </h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<ul class="list-group">
	@foreach($items as $item)
	
		<li class="list-group-item bordered-row">
			<button type="submit" name="deleteSearch[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
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
	</form>
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
                <input type="text" class="form-control" name="search" placeholder="Search">
			</div>
            <div class="form-group">
                <input type="text" class="form-control" name="users" placeholder="Users ID [:ids]"
					@if(Input::get('filter',null) == "users" && Input::has('filter_id')) value="[:{{ Input::get("filter_id") }}]" @endif  
					   >
			</div>
			<button type="submit" name="action" value="addSearch" class="btn btn-default">Add</button>
        </div>  
    </div>
	</form>
</div>
@stop