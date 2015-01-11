@extends($template.'.layout.base')

@section('body')			
	<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><strong><a href="{{ route("admin.show", "orders") }}">Orders</a></strong></li>	
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
	</ol> 
{{ Form::open(array('url' => URL::full(), 'files' => true, 'method' => 'put')); }}
<h1>Order #@if(isset($items->cluster)){{ app('veershop')->getOrderId($items->cluster, $items->cluster_oid) }} @else — @endif<small>
	@if(isset($items->site) && is_object($items->site))~ {{ $items->site->configuration->first()->conf_val or $items->site->url; }} @endif
	@if(isset($items->status) && is_object($items->status))<span class="label" style="background-color: {{ $items->status->color }}">
		<a href="{{ route("admin.show", array("orders", "filter" => "status", "filter_id" => $items->status->id)) }}" target="_blank">
			<strong>{{ $items->status->name or null }}</strong>
		</a></span>&nbsp; 
	@endif
	@if(isset($items->price)) {{ app('veershop')->priceFormat($items->price) }} @if($items->free == true) free @endif @endif
	</small>
	@if(isset($items->id))
	&nbsp;<button type="submit" name="pin[{{ $items->pin or '0' }}]" value="{{ $items->id or null }}" class="btn @if($items->pin == true) label-yellow @else btn-default @endif btn-xs"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span>@if($items->pin == true) pinned @endif</button>@endif
</h1>
<br/>
<div class="container">

	<div class="row">
		<div class="col-sm-12"><small>#{{ $items->id or '—' }} {{ $items->hash or null }}</small></div>
	</div>
	
	@if(isset($items->progress))
	<div class="row">
		<div class="col-sm-12">
			<div class="progress">
				<div class="progress-bar 
					 @if($items->progress <= 25) progress-bar-warning 
					 @elsif($items->progress <= 75) progress-bar-info
					 @else progress-bar-success 
					 @endif progress-bar-striped @if($items->close != true) active @endif" 
					 role="progressbar" aria-valuenow="{{ $items->progress or '0' }}" aria-valuemin="5" aria-valuemax="100" style="width: 
					 {{ $items->progress or '5' }}%;">
					{{ $items->progress or '5' }}%
				</div>
			</div>		
		</div>
	</div>
	
	<div class="xs-rowdelimiter"></div>
	@endif
	<div class="row">
		<div class="col-sm-4">
			<p><select class="form-control" name="fill[status_id]">
				<option value="{{ $items->status->id or null }}">{{ $items->status->name or null }}</option>
				@foreach(statuses() as $status)
				<option value="{{ $status->id }}">{{ $status->name }}</option>
				@endforeach
			</select></p>	
		</div>
		<div class="col-sm-2">
			<p><input type="text" name="fill[progress]" class="form-control" placeholder="Progress (%)" value="{{ $items->progress or '5' }}%">
				<small>Progress (%)</small></p>
		</div>
		<div class="col-sm-2 col-xs-6">
			<p><select class="form-control" name="fill[type]">
				<option value="{{ $items->type or 'unreg' }}">
				@if(isset($items->type) && $items->type == "reg") Registered @else Unregistered @endif</option>
				<option value="unreg">Unregistered</option>
				<option value="reg">Registered</option>
			</select>
			<small>Order type</small>
			</p>
		</div>		
		<div class="col-sm-2 col-xs-6"><p>created at<br/><strong>{{ !empty($items->created_at) ? 
			Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>
		<div class="col-sm-2 col-xs-12"><p>updated at<br/><strong>{{ !empty($items->created_at) ? Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>	
	</div>
	
	<div class="row">
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">
				  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
				</span>
				<input type="text" name="fill[sites_id]" class="form-control" placeholder="Sites Id" value="{{ $items->sites_id or null }}">
			</div>
		<small>@if(isset($items->site) && is_object($items->site))~ {{ $items->site->configuration->first()->conf_val or $items->site->url; }} @endif</small>
		</div>
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">
				  Cluster
				</span>
				<input type="text" name="fill[cluster]" class="form-control" placeholder="Cluster" value="{{ $items->cluster or null }}">
			</div>
		</div>
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">
				  Order Id
				</span>
				<input type="text" name="fill[cluster_oid]" class="form-control" placeholder="Order Id" value="{{ $items->cluster_oid or null }}">
			</div>
		</div>
		<div class="col-md-6"><p></p>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[free]" data-on-color="success" data-on-text="Free" data-off-text="Regular" @if(isset($items->free) && $items->free == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[close]" data-on-color="success" data-on-text="Close&nbsp;" data-off-color="info"  data-off-text="Open" @if(isset($items->close) && $items->close == true) checked @endif></div>	
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[hidden]" data-on-color="warning" data-on-text="Hidden&nbsp;" data-off-color="info" data-off-text="Visible" @if(isset($items->hidden) && $items->hidden == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[archive]" data-on-color="danger" data-on-text="Archived&nbsp;" data-off-text="Inbox" @if(isset($items->archive) && $items->archive == true) checked @endif></div>	
		</div>	
	</div>
	<div class="rowdelimiter"></div>
	<h3><strong>User</strong></h3>	
	<div class="row">
		<div class="col-sm-4"><p><strong><input type="text" class="form-control input-lg" placeholder="Username" name="fill[name]" value="{{ $items->name or null }}"></strong></p></div>
		<div class="col-sm-4"><p><strong><input type="email" class="form-control input-lg" placeholder="Firstname" name="fill[email]" value="{{ $items->email or null }}"></strong></p></div>
		<div class="col-sm-4"><p><strong><input type="tel" class="form-control input-lg" placeholder="Lastname" name="fill[phone]" value="{{ $items->phone or null }}"></strong></p></div>
	</div>	
	
	<div class="row">	
		<div class="col-md-2"><p></p>
			<div class="input-group">
				<span class="input-group-addon">
				  <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</span>
				<input type="text" name="fill[users_id]" class="form-control" placeholder="Users Id" value="{{ Input::get('user', 
						(isset($items->users_id) ? $items->users_id : \Auth::id())) }}">
			</div>
		@if(isset($items->user) && is_object($items->user))
		<small><a href="{{ route('admin.show', array('users', 'id' => $items->user->id)) }}">{{ '@' }}{{ $items->user->username or null }}</a><br/>
			<span class="text-muted">{{ $items->user->role->role or null }} {{ isset($items->user->administrator) ? '| administrator' : null }}</span>
		</small>
		@endif	
		</div>
		<div class="col-md-3"><p></p>
			<input type="text" name="fill[user_type]" class="form-control" placeholder="User Type" value="{{ $items->user_type or null }}">
			<small>User type</small>
		</div>
		<div class="col-md-3"><p></p>
			<input type="text" name="fill[used_discount]" class="form-control" disabled placeholder="Discount Value" value="{{ $items->used_discount or null }}">
			<small>Discount value: For information purposes only</small>
		</div>
		<div class="col-md-2"><p></p>
			<input type="text" name="fill[userdiscount_id]" class="form-control" placeholder="User Discount Id" value="{{ $items->userdiscount_id or null }}">
			<small>Discount Id (if used)</small>
		</div>
		<div class="col-md-2"><p></p>
			<input type="text" name="fill[scores]" class="form-control" placeholder="Scores" value="{{ $items->scores or null }}">
			<small>Scores</small>
		</div>
	</div>
	
	@if(isset($items->userdiscount) && count($items->userdiscount)>0)
	<div class="sm-rowdelimiter"></div>
	<h4>Discount</h4>	
	<ul class="list-group">
			@include($template.'.lists.discounts', array('items' => array($items->userdiscount), 'skipOrder' => true))
	</ul>
	@endif
</div>

<hr class="no-body-padding">

<div class="container">	
	<h3><strong>Payment</strong>@if(!isset($items->bills) || count(@$items->bills)<=0) <small><a href="#" data-toggle="modal" data-target="#billModalNew">create bill</a></small>@endif</h3>
	<div class="row">
		<div class="col-md-3"><p></p><strong>
			<input type="text" name="fill[payment_method]" class="form-control" placeholder="Payment method" 
				   value="{{ $items->payment_method or null }}">
			</strong><small>Payment method</small>
		</div>
		<div class="col-md-3">
			<p></p>
			<input type="text" name="fill[payment_method_id]" class="form-control" placeholder="Payment method Id" value="{{ $items->payment_method_id or null }}"><small>Id @if(isset($items->payment) && is_object($items->payment))
				— {{ $items->payment->name }}:
				{{ $items->payment->type }},
				{{ $items->payment->paying_time }},
				{{ $items->payment->commission }}
			@else
			Payment method Id
			@endif			
			</small>			
		</div>
		<div class="col-md-6"><p></p>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[payment_hold]" data-on-color="danger" data-off-color="info" data-on-text="Hold&nbsp;payment" data-off-text="Allow&nbsp;payment&nbsp;" @if(isset($items->payment_hold) && $items->payment_hold == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[payment_done]" data-on-color="success" data-on-text="Payment&nbsp;done" data-off-text="Awaiting&nbsp;payment&nbsp;" @if(isset($items->payment_done) && $items->payment_done == true) checked @endif></div>
		</div>
	</div>
	
	<div class="rowdelimiter"></div>
	
	@if(isset($items->bills) && count($items->bills)>0)
	<h4>Bills <small><a href="#" data-toggle="modal" data-target="#billModalNew">new bill</a></small></h4>
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.bills', array('items' => $items->bills, 'skipUser' => true))
		</div>
	</div>
	@endif	
	<div class="modal fade" id="billModalNew">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Create new bill</h4>
				</div>
				<div class="modal-body">
					@include($template.'.layout.form-bill', array('OrdersId' => isset($items->id) ? $items->id : 0, 
					'skipSubmit' => true, 'defaultType' => isset($items->payment_method_id) ? $items->payment_method_id : null))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" name="addNewBill" value="New" class="btn btn-primary">Save changes</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->	
</div>

<hr class="no-body-padding">

<div class="container">	
	<h3><strong>Shipping</strong> @if(isset($items->delivery_price))<small>{{ app('veershop')->priceFormat($items->delivery_price) }}</small>@endif</h3>
	<div class="row">
		<div class="col-md-2"><p></p><strong>
			<input type="text" name="fill[delivery_method]" class="form-control" placeholder="Shipping method" 
				   value="{{ $items->delivery_method or null }}">
			</strong><small>Shipping method</small><p></p>
			<input type="text" name="fill[delivery_method_id]" class="form-control" placeholder="Shipping method Id" value="{{ $items->delivery_method_id or null }}"><small>@if(isset($items->delivery) && is_object($items->delivery))
				{{ $items->delivery->name }}:
				{{ $items->delivery->delivery_type }},
				{{ $items->delivery->payment_type }},
				{{ $items->delivery->price }}
				~{{ $items->delivery->address }}	
			@else
			Shipping method Id
			@endif			
			</small>			
		</div>
		<div class="col-md-2">
			<div class="@if(isset($items->delivery_plan) && $items->delivery_plan < now()) has-warning @endif has-feedback">
			<p></p>
			<input type="text" class="form-control date-container" name="fill[delivery_plan]"
					   placeholder="Month/Day/Year" value="{{ !empty($items->delivery_plan) ? Carbon\Carbon::parse($items->delivery_plan)->format('m/d/Y') : '' }}"/>
			@if(isset($items->delivery_plan) && $items->delivery_plan < now() && $items->delivery_real > now())
			<span class="glyphicon glyphicon-question-sign form-control-feedback" aria-hidden="true"></span>
			@endif
			<small>Delivery date (scheduled)</small>
			</div>
			<div class="has-success has-feedback">
			<p></p>
			<input type="text" class="form-control date-container" name="fill[delivery_real]"
					   placeholder="Month/Day/Year" value="{{ !empty($items->delivery_real) ? Carbon\Carbon::parse($items->delivery_real)->format('m/d/Y') : '' }}"/>
			<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<small>Delivery date (actual)</small>
			</div>
		</div>
		<div class="col-md-4"><p></p>
			<strong>
				<input type="text" class="form-control" name="fill[delivery_price]"
					   placeholder="Price" value="{{ $items->delivery_price or null }}"/>
			</strong>
			<small>Shipping price</small>
			<p></p>
			<input type="text" class="form-control" name="fill[weight]" disabled
					   placeholder="Shipping weight" value="{{ $items->weight or null }}"/>
			<small>Weight(g): calculated by content</small>
			<p></p>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[delivery_free]" data-on-color="success" data-on-text="Free&nbsp;delivery" data-off-text="Paid&nbsp;delivery" @if(isset($items->delivery_free) && $items->delivery_free == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[delivery_hold]" data-on-color="danger" data-off-color="info" data-on-text="Hold&nbsp;shipping" data-off-text="Allow&nbsp;shipping" @if(isset($items->delivery_hold) && $items->delivery_hold == true) checked @endif></div>
		</div>
		<div class="col-md-4"><p></p>
			<input type="text" class="form-control" name="fill[country]" disabled
					   placeholder="Country" value="{{ $items->country or null }}"/>
			<p></p><input type="text" class="form-control" name="fill[city]" disabled
					  placeholder="City" value="{{ $items->city or null }}"/>
			<p></p><input type="text" class="form-control" name="fill[address]" disabled
					  placeholder="Address" value="{{ $items->address or null }}"/>
			<p></p><input type="text" class="form-control input-sm" name="fill[userbook_id]"
					  placeholder="Userbook Id" value="{{ $items->userbook_id or null }}"/>
			<small>Userbook 
				@if(isset($items->users_id))
				<a href="{{ route("admin.show", array("books", "filter" => "user", "filter_id" => $items->users_id)) }}" target="_blank">~ edit all user books</a> | @endif
			@if(isset($items->userbook) && count($items->userbook)>0)
			<a href="#" data-toggle="modal" data-target="#bookModalEdit">edit book</a> | 
			@endif
			<a href="#" data-toggle="modal" data-target="#bookModalNew">new book</a>
			</small>
		</div>
	</div>
		
	@if(isset($items->userbook) && count($items->userbook)>0)
	<div class="rowdelimiter"></div>
	<h4>Book <small>delivery address | <a href="#" data-toggle="modal" data-target="#bookModalEdit">edit book</a> | <a href="#" data-toggle="modal" data-target="#bookModalNew">new book</a></small></h4>
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.books', array('items' => array($items->userbook), 'skipOrder' => true))
		</div>
	</div>
	<div class="modal fade" id="bookModalEdit">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">And new user's book</h4>
				</div>
				<div class="modal-body">
					@include($template.'.layout.form-userbook', array('item' =>$items->userbook, 'skipSubmit' => true))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" name="action" value="updateUserbook" class="btn btn-primary">Save changes</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	@endif
	<div class="modal fade" id="bookModalNew">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">And new user's book</h4>
				</div>
				<div class="modal-body">
					@include($template.'.layout.form-userbook', array('item' =>array(), 'skipSubmit' => true, 
					'UsersId' => isset($items->users_id) ? $items->users_id : 0))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" name="action" value="addUserbook" class="btn btn-primary">Save changes</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

<hr class="no-body-padding">

<div class="container">	
	<h3><strong>Order content</strong> @if(isset($items->content_price))<small>{{ app('veershop')->priceFormat($items->content_price) }}</small>@endif</h3>
	
	@if(isset($items->orderContent) && count ($items->orderContent)>0)
	<p>
	@foreach(array_get($items->orderContent, 'statistics.categories.t', array()) as $k => $e)
	<a href="{{ route("admin.show", array("categories", "category" => $k)) }}">{{ $e }}</a> 
	@endforeach
	
	@foreach(array_get($items->orderContent, 'statistics.tags.t', array()) as $k => $e)
	{{ $e }} 
	@endforeach
	
	@foreach(array_get($items->orderContent, 'statistics.attributes.t', array()) as $k => $e)
	 {{ $e }}
	@endforeach
	</p>
	<div class="xs-rowdelimiter"></div>
	
	<div class="row">
		<div class="col-sm-12">
		<ul class="list-group">
			@include($template.'.lists.order-content', 
			array('items' => array_get($items->orderContent, 'content', array()), 'products' => isset($items->products) ? $items->products : array()))
			<li class="list-group-item">
			<input type="text" name="attachContent" class="form-control input-no-borders" placeholder=":Existings IDs[:id,qty:] or Name:pricePerOne:Qty">	
			</li>
		</ul>
		</div>
	</div>	
	@endif
	
	@if(isset($items->orderContent) && array_get($items->orderContent, 'downloads', null) != null)	
	<h4>Files</h4>
	<div class="row">
		<div class="col-sm-12">
		@include($template.'.lists.files', array('files' => array_get($items->orderContent, 'downloads', array()), 'skipUser' => true))
		</div>
	</div>
	@endif
	
	@if(isset($items->id))
	
	<div class="row">
		<div class="col-sm-12 text-center">
			<h2>
				@if($items->free == true) {{ app('veershop')->priceFormat($items->price) }} free @else
				{{ app('veershop')->priceFormat($items->content_price) }} + 
				{{ ($items->delivery_free == true) ? 'free' : app('veershop')->priceFormat($items->delivery_price) }} =
				{{ app('veershop')->priceFormat($items->price) }}
				@endif
			</h2>
		</div>
	</div>
</div>

<hr class="no-body-padding">

<div class="container">	
	<h3>History</h3>
	@if(is_object($items->status_history))
	<ul class="list-group">
	@foreach($items->status_history as $history)
		<li class="list-group-item">
			<small>
				<strong>
				{{ \Carbon\Carbon::parse($history->pivot->updated_at)->format('Y-m-d') }}
				</strong>
				&nbsp;
				{{ \Carbon\Carbon::parse($history->pivot->updated_at)->format('H:i') }}
			</small>&nbsp
			<span class="label" style="background-color: {{ $history->color or 'inherit' }}"><span class="glyphicon glyphicon-ok"></span></span>&nbsp;
				<a href="{{ route("admin.show", array("orders", "filter" => "status", "filter_id" => $history->pivot->status_id)) }}" target="_blank">
				{{ $history->pivot->name or null }}
				</a>
			&nbsp; 
			@if($history->pivot->to_customer == true) <span class="label label-info">visible</span> @endif
			@if($history->flag_first == true) <span class="label label-primary">first</span> @endif
			@if($history->flag_unreg == true) <span class="label label-default">unreg</span> @endif
			@if($history->flag_error == true) <span class="label label-danger">error</span> @endif
			@if($history->flag_payment == true) <span class="label label-yellow">payment</span> @endif
			@if($history->flag_delivery == true) <span class="label label-yellow">delivery</span> @endif
			@if($history->flag_close == true) <span class="label label-success">close</span> @endif
			@if($history->secret == true) <span class="label label-default">secret</span> @endif
			@if($history->trashed() == true) <small>trashed</small> @endif
			&nbsp;
			<button type="submit" value="{{ $history->pivot->id or null }}" name="deleteHistory" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp
			@if(!empty($history->pivot->comments))
			<p></p>
			<span class="text-muted"><small>{{ $history->pivot->comments }}</small></span>
			@endif
		</li>
	@endforeach
	</ul>
	@endif
	
	<div class="rowdelimiter"></div>
	
	<div class="row">
		<div class="col-sm-2"><button type="submit" name="action" value="delete" class="btn btn-default btn-lg btn-block">Delete</button></div>
		<div class="col-sm-6"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
		<div class="col-sm-4"><button type="submit" name="action" value="recalculate" class="btn btn-info btn-lg btn-block">Recalculate</button></div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">	
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "order", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->communications()->count() }} communications</a>
			<button type="button" class="btn btn-primary"  data-toggle="modal" data-target="#communicationModal">Send message</button>
		</div>
	</div>
	<div class="modal fade" id="communicationModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Send message</h4>
				</div>	
				<div class="modal-body">
					@include($template.'.layout.form-communication', array('send2UserId' => $items->users_id, 
						'send2Username' => isset($items->user->username) ? $items->user->username : null, 'emailOn' => true,
						'placeOn' => 'Order:'.$items->id))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" value="{{ $items->id }}" name="sendMessageToUser" class="btn btn-primary">Send message</button>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	@else
	<button type="submit" name="action" value="add" class="btn btn-danger btn-lg btn-block">Add</button>
	@endif
</div>
@if(isset($items->id))
<div class="action-hover-box"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
@endif
{{ Form::close() }}
@stop