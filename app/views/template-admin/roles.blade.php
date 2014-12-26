@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>			
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "roles") }}">Roles</a></strong></li>
		@else
		<li class="active">Roles</li>
		@endif			
	</ol>
<h1>Roles @if(Input::get('filter',null) != null) 
	<small> filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong></small>
	@endif
</h1>
<br/>
<div class="container">
	<div class="list-group">
		@foreach($items as $item)
		<div class="list-group-item row">
			<div class="col-sm-1">
				<p><button type="button" class="btn btn-danger btn-xs">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> #{{ $item->id }}</button>		
				</p>
			</div>
			<div class="col-sm-3">
				<p><strong><input type="text" value="{{ $item->role }}" placeholder="Role" class="form-control"></strong></p>
			</div>
			<div class="col-sm-4">
				<p><select class="form-control">
						<option value="{{ $item->price_field }}">{{ $item->price_field }}</option>
						<option value="price">Price [no discounts by price type]</option>
						<option value="price_sales">Price Sales</option>
						<option value="price_opt">Price Whole</option>
						<option value="price_base">Price Base</option>
				</select></p>
			</div>
			<div class="col-sm-2">
				<input type="text" value="{{ $item->discount }}" placeholder="Discount" class="form-control">
			</div>
			<div class="col-sm-2">
				<small>
					@if(is_object($item->site)) {{ $item->site->configuration->first()->conf_val or $item->site->url; }} @endif<br/>
					{{ $item->created_at }}
					@if($item->updated_at != $item->created_at)
					<br/>{{ $item->updated_at }}
					@endif
					<br/>
					<span class="label label-info"><a href="{{ route("admin.show", array("users", "filter" => "role", "filter_id" => $item->id)) }}">{{ count($item->users) }} users</a></span>
				</small>
			</div>
		</div>
		@endforeach
		<div class="list-group-item row">
			<div class="col-sm-4">
				<p><strong><input type="text" value="" placeholder="Role (user, wholesaler, author, etc.)" class="form-control"></strong></p>
			</div>
			<div class="col-sm-4">
				<p><select class="form-control">
						<option value="price">Price [no discounts by price type]</option>
						<option value="price_sales">Price Sales</option>
						<option value="price_opt">Price Whole</option>
						<option value="price_base">Price Base</option>
				</select></p>
			</div>
			<div class="col-sm-2">
				<p><input type="text" value="" placeholder="Discount (percent)" class="form-control"></p>
			</div>
			<div class="col-sm-2">
				<p><input type="text" class="form-control" name="InSite" placeholder="Sites Id"></p>
				<p><input type="text" class="form-control" name="InUsers" placeholder="Users Id [:ids]"></p>
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-default">Add | Update</button>	 
	
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->links() }}
		</div>
	</div>	
</div>
@stop