	@foreach($items as $discount)
	<li class="list-group-item">
		<span class="badge">{{ \Carbon\Carbon::parse($discount->created_at)->format('Y-m-d') }}</span>
		@if(!isset($skipOrder) && $discount->status != "canceled" && $discount->status != "expired")
		<button type="submit" name="cancelDiscount[{{ $discount->id }}]" value="{{ $discount->id }}" class="btn btn-danger btn-xs">
			CANCEL</button>&nbsp;
		@endif
		<small>#{{ $discount->id }}:</small>
		@if(isset($discount->site) && is_object($discount->site))~ {{ $discount->site->configuration->first()->conf_val or $discount->site->url; }} @endif
		<strong>@if(!isset($skipOrder))<a target="_blank" href="{{ route("admin.show", array("discounts", "filter" => "user", "filter_id" => $discount->users_id))}}">{{ $discount->secret_code }}</a>@else {{ $discount->secret_code }} @endif</strong>
		— <strong>{{ $discount->discount }}%</strong>	

		@if($discount->expires > 0 && $discount->expiration_times !== 0)
		<span class="label label-info">
			limit {{ $discount->expiration_times < 0 ? 'reached' : $discount->expiration_times }}</span>
		@endif
		@if(Carbon\Carbon::parse($discount->expiration_day)->timestamp > 0)
		<span class="label label-default">expiration {{ Carbon\Carbon::parse($discount->expiration_day)->format('d M Y'); }}</span>
		@endif

		@if($discount->expires > 0 && $discount->status != "canceled")
		@if($discount->expiration_times < 0) 			
			<span class="label label-danger">expired by times</span>
		@elseif($discount->expiration_day > \Carbon\Carbon::create(2000) && now() > $discount->expiration_day)
			<span class="label label-danger">expired by date</span>
		@else
		<span class="label label-primary">{{ $discount->status }}</span>
		@endif
		@else
		<span class="label label-primary">{{ $discount->status }}</span>
		@endif
		
		
	</li>
	@endforeach