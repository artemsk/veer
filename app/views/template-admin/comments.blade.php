@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-user', array('place' => 'comments'))
	
<h1>Comments: {{ array_pull($items, 'counted') }} <small>unread: {{ array_pull($items, 'counted_unread', 0) }} @if(Input::get('filter',null) != null) 
	| filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
	@endif </small></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<div class="list-group">
		@foreach ($items as $key => $item) 
		<div class="list-group-item bordered-row">
			<button type="submit" name="deleteComment[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
			&nbsp;
			@if($item->hidden > 0)
			<button type="submit" name="unhideComment[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></button>
			@else
			<button type="submit" name="hideComment[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
			@endif
			&nbsp;
			<small>#{{ $item->id }}</small>
			&nbsp;
			@if($item->users_id > 0)
			<a href="{{ route('admin.show', array("users", "id" => empty($item->users_id) ? '' : $item->users_id)) }}">
			<strong>{{ '~'.$item->author }}</strong>
			</a>
			@else
			<strong>{{ $item->author }}</strong>
			@endif
			&nbsp;
			@if($item->rate > 0)
			<span class="label label-success">
			<span class="glyphicon glyphicon-star" aria-hidden="true"></span> {{ $item->rate }}
			</span>
			@else
			<span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>
			@endif
			@if($item->vote_y > 0)
			&nbsp;
			<span class="label label-success">
			<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> {{ $item->vote_y }}</span>&nbsp;
			@endif
			@if($item->vote_n > 0)
			&nbsp;
			<span class="label label-danger">
			<span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span> {{ $item->vote_n }}</span>
			@endif
			&nbsp;
			<small>
			@if($item->elements_type == "Veer\Models\Product")
				<a href="{{ route("admin.show", array("products", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@elseif($item->elements_type == "Veer\Models\Page")
				<a href="{{ route("admin.show", array("pages", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@else
				<span class="text-muted">#{{ $item->elements_id }} Empty</span>
			@endif	
			&nbsp;
			{{ $item->created_at }}	
			</small>
			<p></p>
			<p>{{ $item->txt }}</p>
		</div>
		@endforeach
	</div>
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
	<label>Add comment to anything as anybody</label>
	<div class="row">
		<div class="col-md-6">             
			<div class="form-group">
				<input type="text" class="form-control" name="fill[author]" placeholder="Author">
			</div>
			<div class="form-group">
				<textarea class="form-control" rows="3" name="fill[txt]" placeholder="Comment"></textarea>
			</div> 
			<div class="form-group">
				<input type="text" class="form-control" name="fill[rate]" placeholder="Rate (0-5)">
			</div>            
			<div class="radio">
				<label>
					{{ Form::radio('vote', 'Yes') }} Yes <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
				</label>
			</div>
			<div class="radio">
				<label>
				{{ Form::radio('vote', 'No') }} No <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
				</label>
			</div>
			<div class="radio">	
				<label>
				{{ Form::radio('vote', 'Blank') }} Do not vote
				</label>
			</div>   
		</div> 
		<div class="col-md-6"> 
			<div class="form-group">
				<input type="text" class="form-control" name="fill[users_id]" placeholder="Users ID [or empty for current]"
					   @if(Input::get('filter',null) == "user" && Input::has('filter_id')) value="{{ Input::get("filter_id") }}" @endif
					   >
			</div> 			
			<div class="form-group">
				<label>Place on Product | Page</label>
				<textarea class="form-control" name="connected" rows="2" placeholder="Page|Product:id"></textarea>
			</div>                  
			<button type="submit" name="action" value="addComment" class="btn btn-default">Submit</button> 
		</div> 
	</div> 
	</form>
</div>
@stop