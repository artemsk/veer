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
		
				&nbsp;<button type="button" class="btn btn-default btn-xs cancel-collapse" data-toggle="modal" data-target="#contentModal{{ $p->id }}">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
				
				@if(!empty($p->comments)) 
				<p></p><small class="text-muted">— {{ $p->comments }}</small>
				@endif
			</div>
			<div class="modal fade" id="contentModal{{ $p->id }}">
				<div class="modal-dialog  modal-sm">
					<div class="modal-content">
						
						<div class="modal-body">
							<div class="form-group">
								<label>Product Id or Empty</label>
								<input type="text" class="form-control input-sm" placeholder="Product ID or Empty" 
								   name="ordersProducts[{{ $p->id }}][fill][products_id]" value="{{ $p->products_id }}">
							</div>
							<div class="form-group">
							<input type="text" class="form-control input-sm" name="ordersProducts[{{ $p->id }}][fill][name]" value="{{ $p->name }}">
							</div>
							<div class="form-group">
								<label>Original Price</label>
								<input type="text" class="form-control input-sm" placeholder="Original price" name="ordersProducts[{{ $p->id }}][fill][original_price]" value="{{ $p->original_price }}">
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Price per one</label>
										<input type="text" class="form-control input-sm" placeholder="Price per one" name="ordersProducts[{{ $p->id }}][fill][price_per_one]" value="{{ $p->price_per_one }}">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Quantity</label>
										<input type="text" class="form-control input-sm" placeholder="Quantity" name="ordersProducts[{{ $p->id }}][fill][quantity]" value="{{ $p->quantity }}">
									</div>
								</div>
							</div>							
							<div class="form-group">
								<label>Attributes</label>
								<input type="text" class="form-control" placeholder="Attributes Ids[,]" 
									   name="ordersProducts[{{ $p->id }}][fill][attributes]" value="
@if(!empty($p->attributes))
@foreach(json_decode($p->attributes) as $attribute)
{{ $attribute }},@endforeach
@endif">
							</div>
							<div class="form-group">
								<label>Comments</label>
								<textarea class="form-control" placeholder="Comments (visible to user)" 
										  name="ordersProducts[{{ $p->id }}][fill][comments]">{{ $p->comments }}</textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="submit" value="{{ $p->id }}" name="editContent" class="btn btn-primary">Update</button>
							<button type="submit" value="{{ $p->id }}" name="deleteContent" class="btn btn-danger">Delete</button>
						</div>

					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</div>
	</li>
@endforeach