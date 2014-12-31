<ul class="list-group">
@foreach($items as $p)
	<li class="list-group-item bordered-row">
		<span class="badge">{{ app('veershop')->priceFormat($p->price) }}</span>
		<div class="row">
			<div class="col-sm-1 col-xs-4 xs-thumbnails-grid"><img data-src="holder.js/50x50/text:&nbsp;" 
				@if(isset($products[$p->id]))
					src="{{ asset(config('veer.images_path').'/'.@$products[$p->id]->images->first()->img) }}" 
				@endif class="img-rounded xs-thumbnails-img">
			</div>
			
			<div class="col-sm-10 col-xs-7">
				<small class="text-muted">[
					#{{ $p->id }}
					{{ $p->product ? 'product' : '—' }}
					]</small><br/>
				<strong>
					{{ $p->quantity }} x 
					@if(isset($products[$p->id])) 
					<a href="{{ route("admin.show", array("products", "id" => $p->products_id)) }}">{{ $p->name }}</a>
					@else
					{{ $p->name }}
					@endif
					= {{ app('veershop')->priceFormat($p->price) }}
				</strong>
				@if($p->original_price != $p->price_per_one) 
				<span class="label label-info">original {{ app('veershop')->priceFormat($p->original_price) }} per one</span>
				@endif
				
				@if(is_array($p->attributesParsed))
					@foreach($p->attributesParsed as $k => $a)
					<span class="label label-warning">
						{{ $a['name'] }} : {{ $a['val'] }} {{ $a['pivot']['product_new_price'] > 0 ? 
							app('veershop')->priceFormat($a['pivot']['product_new_price']) : null }}
					</span>
					@endforeach		
				@endif
		
				@if(!empty($p->comments)) 
				<p></p><small class="text-muted">— {{ $p->comments }}</small>
				@endif
			</div>
		</div>
	</li>
@endforeach
</ul>