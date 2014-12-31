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
		<span class="badge"><a href="{{ route("admin.show", array("orders", "filter" => "userbook", "filter_id" => $item->id)) }}">{{ $item->orders->count() }} orders</a></span>
		@endif
		@if(!isset($skipUser) && !isset($skipOrder))
		<a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ '@'.$item->user->username }}</a>
		@endif
		
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
		@if(!isset($skipOrder))
		<p></p>
		<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>&nbsp;
		<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
		@endif
	</li>			
	@endforeach		
</ul>	