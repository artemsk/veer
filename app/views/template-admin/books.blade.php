@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		<li class="active">Books</li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>		
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Books <small>| users addresses</small></h1>
<br/>
<div class="container">
<ul class="list-group">
	@foreach($items as $item)
	
	<li class="list-group-item">
		<small>#{{ $item->id }}</small>
		{{ $item->name }} <strong>{{ $item->country }},
		{{ $item->region }},
		{{ $item->city }}</strong>
		{{ $item->postcode }}
		{{ $item->address }}
		{{ empty($item->nearby_station) ? '' : '('.$item->nearby_station.')' }}
		@if($item->office_address == true) 
		<span class="label label-info">Office</span>
		@endif
		@if($item->primary == true) 
		<span class="label label-primary">Default</span>
		@endif		
		<span class="badge">{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</span>
		@if($item->updated_at != $item->created_at)
		<span class="badge">{{ \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d') }}</span>
		@endif
		@if($item->orders->count() > 0) 
		<span class="badge">{{ $item->orders->count() }} orders</span>
		@endif
		<a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ '@'.$item->user->firstname }}</a>
		@if(!empty($item->b_account))
		<br/>		
		<small class="bank-data">
			INN {{ $item->b_inn }} 
			ACCOUNT {{ $item->b_account }}
			BANK {{ $item->b_bank }}
			CORR {{ $item->b_corr }}
			BIK {{ $item->b_bik }}
			OTHER {{ $item->b_others }}
		</small>
		@endif
		<p></p>
		<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>&nbsp;
		<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
	</li>			
	@endforeach		
</ul>	
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>	
</div>
@stop