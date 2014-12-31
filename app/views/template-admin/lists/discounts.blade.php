<ul class="list-group">
	@foreach($items as $discount)
	<li class="list-group-item">
		@if(!isset($skipOrder))
		<button type="submit" name="action" value="deleteDiscount.{{ $discount->id }}" class="btn btn-danger btn-xs">
			<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
		@endif
		<small>#{{ $discount->id }}:</small>
		{{ $discount->sites_id }}
		{{ $discount->secret_code }}
		{{ $discount->discount }}
		{{ $discount->expires }}
		{{ $discount->expiration_day }}
		{{ $discount->expiration_times }}
		{{ $discount->status }}
		{{ $discount->created_at }}
		{{ $discount->updated_at }}

		@if($discount->expires > 0)
		<span class="label label-info">
			max {{ $discount->expiration_times }}</span>
		@endif
		@if(Carbon\Carbon::parse($discount->expiration_day)->timestamp > 0)
		<span class="label label-default">expiration {{ Carbon\Carbon::parse($discount->expiration_day)->format('d M Y'); }}</span>
		@endif

		@if($discount->expires > 0)
		@if($discount->expiration_times > 0 && count($discount->orders) >= $discount->expiration_times) 			
			<span class="label label-danger">expired</span>
		@elseif($discount->expiration_day > \Carbon\Carbon::create(2000) && now() > $discount->expiration_day)
			<span class="label label-danger">expired</span>
		@else
		@endif	
		@endif
		</small>				
	</li>
	@endforeach
</ul>