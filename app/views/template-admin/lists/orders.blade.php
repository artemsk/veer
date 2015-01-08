<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
	@foreach($items as $key => $item)
	<div class="panel panel-default @if($item->pin == true) pinned @else not-pinned @endif">
		<div class="panel-heading" role="tab">
			<!--<button type="button" class="btn btn-danger btn-xs">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp -->
			<a href="{{ route("admin.show", array("orders", "id" => $item->id)) }}"><span class="label label-yellow label-bg">
			#{{ app('veershop')->getOrderId($item->cluster, $item->cluster_oid) }}</span></a>
			
			@if(is_object($item->site))
			<small>&nbsp;~ <a href="{{ route("admin.show", array("orders", "filter" => "site", "filter_id" => $item->sites_id)) }}">
					{{ $item->site->configuration->first()->conf_val or $item->site->url; }}</a></small>
			@endif
			
			&nbsp;
			<strong><a href="{{ route("admin.show", array("orders", "filter" => "type", "filter_id" => $item->type)) }}">
					{{ $item->type }}</a></strong>
			&nbsp;
			@if(is_object($item->status))<span class="label" style="background-color: {{ $item->status->color }}">
				<a href="{{ route("admin.show", array("orders", "filter" => "status", "filter_id" => $item->status->id)) }}">
					<strong>{{ $item->status->name or null }}</strong>
				</a></span>&nbsp; 
			@endif

			@if(!empty($item->user_type))
			<span class="label label-info">{{ $item->user_type or '' }}</span>
			@endif

			@if($item->userdiscount_id > 0 && is_object($item->userdiscount))
			<span class="label label-info">
				<a href="{{ route("admin.show", array("discounts", "filter" => "user", "filter_id" => $item->users_id)) }}">discount</a></span>
			@endif

			@if($item->free == true) 
			<span class="label label-primary">
				<a href="{{ route("admin.show", array("orders", "filter" => "free", "filter_id" => true)) }}">FREE</a></span> 
			@endif

			@if($item->delivery_free == true) 
			<span class="label label-primary">
				<a href="{{ route("admin.show", array("orders", "filter" => "delivery_free", "filter_id" => true)) }}">free shipping</a></span> 
			@endif

			@if($item->delivery_hold == true) 
			<span class="label label-danger">
				<a href="{{ route("admin.show", array("orders", "filter" => "delivery_hold", "filter_id" => true)) }}">hold shipping</a></span> 
			@endif

			@if($item->payment_hold == true) 
			<span class="label label-danger">
				<a href="{{ route("admin.show", array("orders", "filter" => "payment_hold", "filter_id" => true)) }}">hold payment</a></span> 
			@endif

			@if($item->payment_done == true) 
				<span class="label label-success">
					<a href="{{ route("admin.show", array("orders", "filter" => "payment_done", "filter_id" => true)) }}">paid</a></span> 
			@endif

			@if($item->close == true) 
			<span class="label label-success"><a href="{{ route("admin.show", array("orders", "filter" => "close", "filter_id" => true)) }}">close</a> 
				{{ \Carbon\Carbon::parse($item->close_time)->format('Y-m-d'); }}</span> @endif
	
			@if($item->hidden == true) 
			&nbsp;<span class="label label-default">
				<a href="{{ route("admin.show", array("orders", "filter" => "hidden", "filter_id" => true)) }}">hidden</a></span>
			@endif

			@if($item->archive == true) 
			<span class="label label-default">archive</span>
			@endif				

			&nbsp; <small>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }} #{{ $item->id }}</small>
			&nbsp;
			<button type="button" class="btn btn-default btn-xs cancel-collapse" data-toggle="modal" data-target="#orderModal{{ $item->id }}">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
			<button type="submit" name="pin[{{ $item->pin }}]" value="{{ $item->id }}" class="btn @if($item->pin == true) btn-warning @else btn-default @endif btn-xs">
				<span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>

			<div class="pull-right"><a data-toggle="collapse" data-target="#collapse{{ $key }}" data-parent="#accordion" style="cursor:zoom-in">{{ app('veershop')->priceFormat($item->price) }}</a></div>
		</div>
		<div class="panel-collapse collapse @if($key == 0 && !isset($skipUser)) in @else out @endif" id="collapse{{ $key }}">
			<div class="panel-body">
				Payer: <strong>{{ $item->name }}</strong>, <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>, <a 
					href="tel:{{ $item->phone }}">{{ $item->phone }}</a>@if(!isset($skipUser)) |
				@if($item->users_id > 0 && is_object($item->user))
				<a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ "@".$item->user->username }}</a>
				@elseif($item->users_id > 0) 
				<del><a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">#{{ $item->users_id }}</a></del>
				@else
				@endif
				@endif
			</div>
			<ul class="list-group">
				<li class="list-group-item">
					Shipping method: <strong>
					<a href="{{ route("admin.show", array("orders", "filter" => "delivery", "filter_id" => $item->delivery_method_id)) }}">
						{{ $item->delivery_method }}</a></strong><br/>
					Payment method: <strong>
					<a href="{{ route("admin.show", array("orders", "filter" => "payment", "filter_id" => $item->payment_method_id)) }}">
						{{ $item->payment_method }}</a></strong>
				</li>
				<li class="list-group-item">
					Shipping address: <strong>{{ $item->country }},	{{ $item->city }}, {{ $item->address }}</strong>
					@if(is_object($item->userbook))
					~ <a href="{{ route("admin.show", array("orders", "filter" => "userbook", "filter_id" => $item->userbook_id)) }}">
						all orders to address</a>
					@endif

					<div class="xs-rowdelimiter"></div>

					Shipping plan: 
					@if(!empty($item->delivery_plan))
					<span class="label label-default">
						{{ \Carbon\Carbon::parse($item->delivery_plan)->format("Y-m-d H:i") }}</span> 
					@else NOT PLANNED
					@endif
					@if(!empty($item->delivery_real))
					<small>~ real: </small><span class="label label-success">
						{{ \Carbon\Carbon::parse($item->delivery_real)->format("Y-m-d H:i") }}</span>	
					@endif
				</li>
				<li class="list-group-item">
					Content price: <strong>{{ app('veershop')->priceFormat($item->content_price) }}</strong>
					@if($item->used_discount > 0) <small> ~ {{ app('veershop')->priceFormat($item->used_discount) }} discount</small>
					@endif
					<br/>
					Shipping price: <strong>{{ app('veershop')->priceFormat($item->delivery_price) }}</strong>
				</li>
				@if(is_object($item->bills) && count($item->bills) > 0)
				<li class="list-group-item">
						@foreach($item->bills as $bill)
						<a href="{{ route("admin.show", array("bills", "filter" => "order", "filter_id" => $item->id)) }}">Bill</a> #{{$bill->id}} <strong>{{$bill->payment_method}}</strong> <a href="{{ route("order.bills", array($item->id, $item->link)) }}" target="_blank">{{ $bill->link }}</a> 
						<strong>{{ app('veershop')->priceFormat($bill->price) }}</strong> {{ $bill->status->name or null }} 
						<small><strong>
						@if($bill->sent == true) -sent @endif 
						@if($bill->viewed == true) -viewed @endif
						@if($bill->paid == true) -paid @endif
						@if($bill->canceled == true) -canceled @endif</strong></small><br/>
						@endforeach	
				</li>
				@endif
				<li class="list-group-item">
					@if($item->scores > 0) Score: {{ $item->scores }} @endif
					<h3>{{ app('veershop')->priceFormat($item->price) }}</h3>
				</li>
			</ul>	
		</div>
		<div class="modal fade" id="orderModal{{ $item->id }}">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Quick update</h4>
					</div>	
					<div class="modal-body">
						<div class="form-group">
							<label>Status</label>
						<select class="form-control" name="history[{{ $item->id }}][status_id]">
							@if(is_object($item->status)) 
								<option value="{{ $item->status->id or null }}">{{ $item->status->name or null }}</option>
							@endif
							@foreach(statuses() as $status)
							<option value="{{ $status->id }}">{{ $status->name }}</option>
							@endforeach
						</select>
						</div>
						<div class="form-group">
							<label>Comment</label>
							<textarea class="form-control" name="history[{{ $item->id }}][comments]" placeholder="Comment"></textarea>
						</div>
						<div class="checkbox">
							<label>
							<input type="checkbox" name="history[{{ $item->id }}][to_customer]" value="1"> Show comment to user
							</label>
						</div>
						<div class="checkbox">
							<label>
							<input type="checkbox" name="history[{{ $item->id }}][send_to_customer]" value="1"> Send comment by email
							</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" value="{{ $item->id }}" name="updateOrderStatus" class="btn btn-primary btn-xs">Update status</button>
						&nbsp; | &nbsp;
						@if($item->payment_hold == true) 
						<button type="submit" value="{{ $item->id }}" name="updatePaymentHold[0]" class="btn btn-danger btn-xs">Payment on hold</button>
						@else
						@if($item->payment_done == true)
						<button type="submit" value="{{ $item->id }}" name="updatePaymentDone[0]" class="btn btn-success btn-xs">Payment done</button>
						@else
						<button type="submit" value="{{ $item->id }}" name="updatePaymentHold[1]" class="btn btn-default btn-xs">Payment allowed</button>
						<button type="submit" value="{{ $item->id }}" name="updatePaymentDone[1]" class="btn btn-default btn-xs">Payment to be done</button>
						@endif
						@endif
						@if($item->delivery_hold == true) 
						<button type="submit" value="{{ $item->id }}" name="updateShippingHold[0]" class="btn btn-danger btn-xs">Shipping on hold</button>
						@else
						<button type="submit" value="{{ $item->id }}" name="updateShippingHold[1]" class="btn btn-success btn-xs">Shipping allowed</button>
						@endif
						@if($item->close == true) 
						<button type="submit" value="{{ $item->id }}" name="updateOrderClose[0]" class="btn btn-success btn-xs">Closed</button>
						@if($item->hidden == true) 
						<button type="submit" value="{{ $item->id }}" name="updateOrderHide[0]" class="btn btn-info btn-xs">Hidden</button>
						@else
						<button type="submit" value="{{ $item->id }}" name="updateOrderHide[1]" class="btn btn-default btn-xs">Visible</button>
						@endif
						@if($item->archive == true) 
						<button type="submit" value="{{ $item->id }}" name="updateOrderArchive[0]" class="btn btn-info btn-xs">Archived</button>
						@else
						<button type="submit" value="{{ $item->id }}" name="updateOrderArchive[1]" class="btn btn-default btn-xs">Inbox</button>
						@endif
						@else
						<button type="submit" value="{{ $item->id }}" name="updateOrderClose[1]" class="btn btn-default btn-xs">Open</button>
						@endif
						
					</div>

				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>
	
	@endforeach
</div>