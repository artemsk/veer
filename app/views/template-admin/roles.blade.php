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
		<li class="active">Roles</li>
	</ol>
<h1>Roles</h1>
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
					{{ $item->site->configuration->first()->conf_val or $item->site->url; }}<br/>
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
			<div class="col-sm-1">
			</div>
			<div class="col-sm-3">
				<p><strong><input type="text" value="" placeholder="Role" class="form-control"></strong></p>
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
				<input type="text" value="" placeholder="Discount (percent)" class="form-control">
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-default">Add | Update</button>	
</div>
@stop