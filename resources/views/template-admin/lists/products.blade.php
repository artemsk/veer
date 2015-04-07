	<div class="row">
		@foreach($items as $key => $item)
		@if(round($key/6) == ($key/6)) <div class="clearfix visible-lg-block"></div> @endif
                @if(round($key/4) == ($key/4)) <div class="clearfix visible-md-block"></div> @endif
                @if(round($key/2) == ($key/2)) <div class="clearfix visible-sm-block"></div> @endif
		<div class="col-lg-2 col-md-3 col-sm-6 text-center">
			<div class="thumbnail products-thumbnail @if($item->status == 'hide')
				 bg-muted
				 @endif ">
                            <div class="products-image-thumb">
                                @if(count($item->images)>0)
				<a href="{{ route('admin.show', array("products", "id" => $item->id)) }}" target="_blank">
					<img src="@if(config('veer.use_cloud_images')){{ config('veer.cloudstorage_path').'/' }}@else{{ asset('') }}@endif{{ (config('veer.images_path').'/'.@$item->images[0]->img) }}" class="products-thumbnail-img img-responsive @if($item->status == 'hide')	 image-faded @endif "></a>@else
                                                 <img data-src="holder.js/100%x50/gray/text:No Image!"
						 class="img-responsive products-thumbnail-img @if($item->status == 'hide') image-faded @endif">
                                                 @endif
                            </div>
				<div class="caption @if($item->hidden == true) image-faded @endif"><small>#{{$item->id}}
					</small>
					<a href="{{ route('admin.show', array("products", "id" => $item->id)) }}">{{ $item->title }}</a>
					<p><strong>{{ app('veershop')->getPrice($item, true, array('forced_currency' => 1)) }}</strong><Br/>
						<small>
						@if($item->price_base != $item->price)
						{{ app('veershop')->priceCurrencyFormat($item->price_base, $item->currency, array('forced_currency' => 1)) }}
						@endif						
						&nbsp;<span class="glyphicon glyphicon-th-list" aria-hidden="true" title="Quantity"></span> {{ $item->qty }}
						@if($item->currency > 0)
						&nbsp;<span class="glyphicon glyphicon-asterisk" aria-hidden="true" title="Currency set!"></span>
						@endif
						@if($item->weight <= 0)
						&nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No weight!"></span>
						@endif
						@if(count($item->categories) <= 0)
						&nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
						@endif
						@if($item->star == true)
						&nbsp;<span class="glyphicon glyphicon-heart warning-icon" aria-hidden="true" title="Star"></span>
						@endif	
						@if($item->score > 0)
						&nbsp;<span class="glyphicon glyphicon-plus text-info" aria-hidden="true" title="Score {{ $item->score }}"></span>
						@endif							
						</small></p>
					@if (now() < $item->to_show)
					<button type="submit" name="action" value="showEarlyProduct.{{ $item->id }}" class="btn btn-warning btn-xs" title="Waiting for {{ $item->to_show }}. Press to show it now" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></button>&nbsp;
					@endif
					@if ($item->status == 'buy' || $item->status == 'on')
					<button type="submit" name="action" value="changeStatusProduct.{{ $item->id }}" class="btn btn-success btn-xs" title="Current: ON (BUY)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
					@elseif ($item->status == 'hide')
					<button type="submit" name="action" value="changeStatusProduct.{{ $item->id }}" class="btn btn-default btn-xs" title="Current: OFF (HIDE)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-stop" aria-hidden="true"></span></button>
					@elseif ($item->status == 'sold')
					<button type="submit" name="action" value="changeStatusProduct.{{ $item->id }}" class="btn btn-warning btn-xs" title="Current: SOLD OUT (SOLD)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span></button>
					@else
					<button type="submit" name="action" value="changeStatusProduct.{{ $item->id }}" class="btn btn-danger btn-xs" title="Current: UNKNOWN ({{ $item->status }})" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></button>
					@endif
					@if(!isset($denyDelete) || !$denyDelete)
					&nbsp;<button type="submit" name="action" value="deleteProduct.{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					@else
					&nbsp;<button type="submit" name="action" value="removeProduct.{{ $item->id }}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					@endif
				</div>
			</div>
			@if($item->download == true) 
			<div class="top-panel">
					<span class=" glyphicon glyphicon-floppy-save" aria-hidden="true"></span>
			</div>
			@endif
			<div class="top-panel-right">
					<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $item->viewed }}&nbsp;
					<span class="glyphicon glyphicon-fire danger-icon" aria-hidden="true"></span> {{ $item->ordered }}
			</div>
		</div>
		@endforeach	
	</div>