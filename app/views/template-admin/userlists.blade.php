@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-user', array('place' => 'lists'))
	
<h1>Lists <small>| 
		@if(Input::get('filter',null) != null) 
			filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
			@else
			lists:{{ array_pull($items, 'lists', 0) }} carts:{{ array_pull($items, 'basket', 0) }}
		@endif	
		</small></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	@foreach(array_get($items, 'regrouped', array()) as $user => $itemGroup)
	<ul class="list-group">
		<li class="list-group-item list-group-item-info"><strong>
			@if($user > 0) {{ $items['users'][$user]->firstname or null }} {{ $items['users'][$user]->lastname or null }} 
			<strong>{{ '@' }}{{ $items['users'][$user]->username or '?Unknown' }}</strong>
			@if(is_object($items['users'][$user]))
			<a href="{{ route("admin.show", array("users", "id" => $items['users'][$user]->id)) }}">{{ $items['users'][$user]->email }}</a> 
			| <a href="tel:{{ $items['users'][$user]->phone }}">{{ $items['users'][$user]->phone }}</a> 
			@endif
			@else Guest @endif</strong>
			@if($user > 0) 
			@if(is_object($items[head($itemGroup)]->site)) ~ <a href="{{ route('admin.show', array("lists", "filter" => "site", "filter_id" => $items[head($itemGroup)]->site->id)) }}">{{ $items[head($itemGroup)]->site->configuration->first()->conf_val or $items[head($itemGroup)]->site->url; }}</a> @endif
			@endif
		</li>
		@foreach($itemGroup as $item)

		<li class="list-group-item bordered-row">
			<button type="submit" name="deleteList[{{ $items[$item]->id }}]" value="{{ $items[$item]->id }}"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
			@if($items[$item]->name == "[basket]")
			<span class="label label-primary">cart</span>
			@else
			<span class="label label-success">{{ $items[$item]->name }}</span>
			@endif
			@if($items[$item]->elements_type == "Veer\Models\Product")
				{{ $items[$item]->quantity }} x 
				<a href="{{ route("admin.show", array("products", "id" => $items[$item]->elements_id)) }}">
					{{ $items[$item]->elements->title or '[?] Unknown' }}</a>
				@if(!empty($items[$item]->attributes))
				<span class="label label-info">+ attributes</span>
				@endif
			@elseif($items[$item]->elements_type == "Veer\Models\Page")
				<a href="{{ route("admin.show", array("pages", "id" => $items[$item]->elements_id)) }}">
					{{ $items[$item]->elements->title or '[?] Unknown' }}</a>
			@else
				<span class="text-muted">#{{ $items[$item]->elements_id }} ?</span>
			@endif
			@if($user <= 0)
			<small>@if(is_object($items[$item]->site)) ~ <a href="{{ route('admin.show', array("lists", "filter" => "site", "filter_id" => $items[$item]->site->id)) }}">{{ $items[$item]->site->configuration->first()->conf_val or $items[$item]->site->url; }}</a> @endif</small>
			<span class="label label-info" data-toggle="popover" data-container="body" data-placement="bottom" data-content="{{ $items[$item]->session_id }}">session</span>
			@endif
		</li>
		
		@endforeach		
	</ul>		
	@endforeach
	
	{{ Form::close() }}
	
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
	<label>Add list</label>
	<div class="row">
        <div class="col-md-6">             
            <div class="form-group">
                <input type="text" class="form-control" name="fill[sites_id]" placeholder="Sites ID">
			</div>
            <div class="form-group">
                <input type="text" class="form-control" name="fill[users_id]" placeholder="Users ID (or leavy empty for guest)"
					@if(Input::get('filter',null) == "user" && Input::has('filter_id')) value="{{ Input::get("filter_id") }}" @endif
					   >
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="fill[session_id]" placeholder="Session ID">
            </div>     
			<div class="form-group">
                <input type="text" class="form-control" name="fill[name]" placeholder="List Name">
            </div>
            <div class="checkbox">
                <label>
					<input type="checkbox" name="checkboxes[basket]"> (or) shopping cart
                </label>
            </div>		
        </div>  
        <div class="col-md-6">
            <div class="form-group">
				<label>Products (Id per row > Product:Quantity:Attributes[,])</label>
				<textarea class="form-control" name="products" rows="2" placeholder="Id:Quantity"></textarea>
            </div>
            <div class="form-group">
				<label>Pages in List (Id per row)</label>
				<textarea class="form-control" name="pages" rows="2" placeholder="Id"></textarea>
            </div>
			<button type="submit" name="action" value="addList" class="btn btn-default">Submit</button>
        </div>
    </div>
	{{ Form::close() }}
</div>
@stop