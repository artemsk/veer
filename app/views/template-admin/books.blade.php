@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "books") }}">Books</a></strong></li>
		@else
		<li class="active">Books</li>
		@endif			
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>		
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		<li><a href="{{ route("admin.show", "communications") }}">Communications</a></li>
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Books 
	@if(Input::get('filter',null) != null) <small>
			filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
	</small>
			{{ array_pull($items, 'counted', 0) ? '' : '' }}
	@else
	:{{ array_pull($items, 'counted', 0) }}
	@endif
	<small> | users addresses</small></h1>
<br/>
<div class="container">
<ul class="list-group">
	@foreach($items as $item)
	
	<li class="list-group-item bordered-row">
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
		<span class="badge"><a href="{{ route("admin.show", array("orders", "filter" => "books", "id" => $item->id)) }}">{{ $item->orders->count() }} orders</a></span>
		@endif
		<a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ '@'.$item->user->username }}</a>
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
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->links() }}
		</div>
	</div>	
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add users book</label>	
	<div class="row">
        <div class="col-md-6">             
			<div class="form-group">
                <input type="text" class="form-control" name="InUsers" placeholder="Users ID">
			</div>
			<div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="InBusinessName" placeholder="Business Name"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InCountry" placeholder="Country"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InRegion" placeholder="Region"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InCity" placeholder="City"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InPostcode" placeholder="Postcode"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InStreet" placeholder="Street Address"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InStation" placeholder="Nearby Station"><div class="xs-rowdelimiter"></div>
            </div>
            <div class="checkbox">
                <label>
					<input type="checkbox" name="OnOffice"> Office Address
                </label>
            </div>
            <div class="checkbox">
                <label>
					<input type="checkbox" name="OnPrimary"> Primary | Default Entry
                </label>
            </div>
        </div>  
        <div class="col-md-6"> 
			<div class="form-group">
				<label>Bank Account</label>
				<input type="text" class="form-control" name="InInn" placeholder="Inn"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="InAccount" placeholder="Account Number"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="InBank" placeholder="Bank"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="InCorr" placeholder="Corr Account"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="InBik" placeholder="BIK"><div class="xs-rowdelimiter"></div>
				<textarea class="form-control" name="InOthers" rows="2" placeholder="Foreign Banks"></textarea>
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
        </div>
    </div>
	{{ Form::close() }}
</div>
@stop