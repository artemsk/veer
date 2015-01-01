@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-user', array('place' => 'roles'))
	
<h1>Roles @if(Input::get('filter',null) != null) 
	<small> filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong></small>
	@endif
</h1>
<br/>
<div class="container">
	{{ Form::open(array('method' => 'put', 'files' => false)); }}
	<div class="list-group">
		@foreach($items as $item)
		<div class="list-group-item row">
			<div class="col-sm-1">
				<p><button type="submit" name="action" value="deleteRole.{{ $item->id }}" class="btn btn-danger btn-xs">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> #{{ $item->id }}</button>		
				</p>
			</div>
			<div class="col-sm-3">
				<p><strong><input type="text" value="{{ $item->role }}" placeholder="Role" class="form-control" name="role[{{ $item->id }}][role]"></strong></p>
			</div>
			<div class="col-sm-4">
				<p><select class="form-control" name="role[{{ $item->id }}][price_field]">
						<option value="{{ $item->price_field }}">{{ $item->price_field }}</option>
						<option value="price">Price [no discounts by price type]</option>
						<option value="price_sales">Price Sales</option>
						<option value="price_opt">Price Whole</option>
						<option value="price_base">Price Base</option>
				</select></p>
			</div>
			<div class="col-sm-2">
				<input type="text" value="{{ $item->discount }}" placeholder="Discount" class="form-control" name="role[{{ $item->id }}][discount]">
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
				<p><strong><input type="text" value="" placeholder="Role (user, wholesaler, author, etc.)" class="form-control" name="role[new][role]"></strong></p>
			</div>
			<div class="col-sm-4">
				<p><select class="form-control" name="role[new][price_field]">
						<option value="price">Price [no discounts by price type]</option>
						<option value="price_sales">Price Sales</option>
						<option value="price_opt">Price Whole</option>
						<option value="price_base">Price Base</option>
				</select></p>
			</div>
			<div class="col-sm-2">
				<p><input type="text" value="" placeholder="Discount (percent)" class="form-control" name="role[new][discount]"></p>
			</div>
			<div class="col-sm-2">
				<p><input type="text" class="form-control" name="InSite" placeholder="Sites Id"></p>
				<p><input type="text" class="form-control" name="InUsers" placeholder="NEW|Id [:users ids]"></p>
			</div>
		</div>
	</div>
	<button type="submit" name="action" value="updateRoles" class="btn btn-default">Add | Update</button>	 
	
	{{ Form::close() }}
	
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