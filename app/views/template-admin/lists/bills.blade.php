@if(isset($skipUser)) <ul class="list-group"> @endif
@foreach($items as $item)
	@if(!isset($skipUser)) <ul class="list-group"> @endif
		<li class="list-group-item bordered-row">
		<button type="submit" value="{{ $item->id }}" name="deleteBill" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp
		#{{ $item->id }}
		@if(is_object($item->payment))
		<strong><a href="{{ route("admin.show", array("bills", "filter" => "payment", "filter_id" => $item->payment->id)) }}">
				{{ $item->payment_method }}</a></strong>
		@else
		<del>&nbsp;{{ $item->payment_method }}&nbsp;</del>
		@endif
		&nbsp;
		@if(is_object($item->status)) <span class="label" style="background-color: {{ $item->status->color }}">
			<a href="{{ route("admin.show", array("bills", "filter" => "status", "filter_id" => $item->status->id)) }}">
			<strong>{{ $item->status->name or null }}</strong>
			</a></span>@endif
		&nbsp;
		@if($item->sent == true) <span class="label label-info"><a href="{{ route("admin.show", array("bills", "filter" => "sent", "filter_id" => 1)) }}">sent</a></span> @else <span class="label label-default"><a href="{{ route("admin.show", array("bills", "filter" => "sent", "filter_id" => 0)) }}">not sent</a></span> @endif
		@if($item->viewed == true) <span class="label label-primary"><a href="{{ route("admin.show", array("bills", "filter" => "viewed", "filter_id" => 1)) }}">viewed</a></span> @else <span class="label label-default"><a href="{{ route("admin.show", array("bills", "filter" => "viewed", "filter_id" => 0)) }}">not seen</a></span> @endif
		@if($item->paid == true) <span class="label label-success"><a href="{{ route("admin.show", array("bills", "filter" => "paid", "filter_id" => 1)) }}">paid</a></span> @else <span class="label label-default"><a href="{{ route("admin.show", array("bills", "filter" => "paid", "filter_id" => 0)) }}">not paid</a></span> @endif
		@if($item->canceled == true) <span class="label label-danger"><a href="{{ route("admin.show", array("bills", "filter" => "canceled", "filter_id" => 1)) }}">canceled</a></span> @endif
		
		
		@if(isset($skipUser))
		&nbsp;
		<small>
		<a href="{{ route("order.bills", array($item->id, $item->link)) }}" target="_blank">link</a>
		</small>
		&nbsp;
		<strong>{{ app('veershop')->priceFormat($item->price) }}</strong>
		@endif
		
		&nbsp;
		<button type="button" class="btn btn-default btn-xs cancel-collapse" data-toggle="modal" data-target="#billModal{{ $item->id }}">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
				
		<span class="badge">{{ $item->created_at }}</span>
		@if($item->updated_at != $item->created_at)
		<span class="badge">{{ $item->updated_at }}</span>
		@endif
		
		@if(!isset($skipUser))
		</li>
		<li class="list-group-item">
		Order: @if(is_object($item->order)) 
		<a href="{{ route("admin.show", array("orders", "id" => $item->orders_id)) }}">
			#{{ app('veershop')->getOrderId($item->order->cluster, $item->order->cluster_oid) }}</a>
			<br/>Payer: <strong>{{ $item->order->name }}</strong>, <a href="mailto:{{ $item->order->email }}">{{ $item->order->email }}</a>, <a 
				href="tel:{{ $item->order->phone }}">{{ $item->order->phone }}</a>
		@else <del><a href="{{ route("admin.show", array("orders", "id" => $item->orders_id)) }}">#{{ $item->orders_id }}</a></del> @endif
		@if($item->users_id > 0 && is_object($item->user))
		| <a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ "@".$item->user->username }}</a>
		@endif
		<br/>
		Bill link: <a href="{{ route("order.bills", array($item->id, $item->link)) }}" target="_blank">{{ $item->link }}</a>
		<br/>
		<h3>{{ app('veershop')->priceFormat($item->price) }}</h3>
		{{-- Currency used when creating bill --}}		
		@endif
		
		<div class="modal fade" id="billModal{{ $item->id }}">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Quick update</h4>
					</div>	
					<div class="modal-body">
						<div class="form-group">
							<label>Status</label>
						<select class="form-control" name="billUpdate[{{ $item->id }}][status_id]">
							@if(is_object($item->status)) 
								<option value="{{ $item->status->id or null }}">{{ $item->status->name or null }}</option>
							@endif
							@foreach(statuses() as $status)
							<option value="{{ $status->id }}">{{ $status->name }}</option>
							@endforeach
						</select>
							<input type="hidden" name="billUpdate[{{ $item->id }}][orders_id]" value="{{ $item->orders_id }}">
						</div>
						<div class="form-group">
							<label>Comment</label>
							<textarea class="form-control" name="billUpdate[{{ $item->id }}][comments]" placeholder="Comment"></textarea>
						</div>
						<div class="checkbox">
							<label>
							<input type="checkbox" name="billUpdate[{{ $item->id }}][to_customer]" value="1"> Show comment to user
							</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" value="{{ $item->id }}" name="updateBillStatus" class="btn btn-primary btn-xs">Update bill status</button>
						&nbsp; | &nbsp;
						@if($item->sent == true)
							@if($item->paid == true)
							<button type="submit" value="{{ $item->id }}" name="updateBillPaid[0]" class="btn btn-success btn-xs">Payment done</button>
							@else
							<button type="submit" value="{{ $item->id }}" name="updateBillPaid[1]" 
									class="btn btn-default btn-xs">Mark bill as paid</button>
							@endif
						@else
						<button type="submit" value="{{ $item->id }}" name="updateBillSend[1]" 
								class="btn btn-info btn-xs">Send bill to user</button>
						@endif
						@if($item->canceled == true)
						<button type="submit" value="{{ $item->id }}" name="updateBillCancel[0]" class="btn btn-danger btn-xs">Bill canceled</button>
						@else
						<button type="submit" value="{{ $item->id }}" name="updateBillCancel[1]" class="btn btn-default btn-xs">Cancel bill</button>
						@endif		
					</div>

				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		</li>
	@if(!isset($skipUser)) </ul> @endif	
	@endforeach
	@if(isset($skipUser)) </ul> @endif	