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
<h1>Order #@if(isset($items->cluster)){{ app('veershop')->getOrderId($items->cluster, $items->cluster_oid) }} @else — @endif<small>
		@if(isset($items->site) && is_object($items->site))~ {{ $items->site->configuration->first()->conf_val or $items->site->url; }} @endif
		@if($items->pin == true) <span class="label label-yellow"><span class="glyphicon glyphicon-pushpin"></span> pinned</span> @endif
		@if(is_object($items->status))<span class="label" style="background-color: {{ $items->status->color }}">
			<a href="{{ route("admin.show", array("orders", "filter" => "status", "filter_id" => $items->status->id)) }}" target="_blank">
				<strong>{{ $items->status->name or null }}</strong>
			</a></span>&nbsp; 
		@endif
		{{ app('veershop')->priceFormat($items->price) }}
		</small></h1>
<br/>
{{ Form::open(array('url' => URL::full(), 'files' => true, 'method' => 'put')); }}
<div class="container">

	<div class="row">
		<div class="col-sm-12"><small>#{{ $items->id }} {{ $items->hash }}</small></div>
	</div>
	
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
	
	<div class="row">
		<div class="col-sm-4">
			<p><select class="form-control" name="fill[status_id]">
				<option value="{{ $items->status->id }}">{{ $items->status->name }}</option>
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
				<input type="text" name="fill[cluster_id]" class="form-control" placeholder="Order Id" value="{{ $items->cluster_oid or null }}">
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
				<input type="text" name="fill[users_id]" class="form-control" placeholder="Users Id" value="{{ $items->users_id or null }}">
			</div>
		@if(is_object($items->user))
		<small><a href="{{ route('admin.show', array('users', 'id' => $items->user->id)) }}">{{ '@'.$items->user->username }}</a><br/>
			<span class="text-muted">{{ $items->user->role->role }} {{ isset($items->user->administrator) ? '| administrator' : null }}</span>
		</small>
		@endif	
		</div>
		<div class="col-md-3"><p></p>
			<input type="text" name="fill[user_type]" class="form-control" placeholder="User Type" value="{{ $items->user_type or null }}">
			<small>User type</small>
		</div>
		<div class="col-md-3"><p></p>
			<input type="text" name="fill[used_discount]" class="form-control" placeholder="Discount Value" value="{{ $items->used_discount or null }}">
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
	
	<div class="rowdelimiter"></div>
	@if(isset($items->userdiscount) && count($items->userdiscount)>0)
	<h4>Used discount</h4>	
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.discounts', array('items' => array($items->userdiscount), 'skipOrder' => true))
		</div>
	</div>
	@endif
	
	<div class="rowdelimiter"></div>
	<h3><strong>Shipping</strong> <small>{{ app('veershop')->priceFormat($items->delivery_price) }}</small></h3>
	<div class="row">
		<div class="col-md-2"><p></p><strong>
			<input type="text" name="fill[delivery_method]" class="form-control" placeholder="Shipping method" 
				   value="{{ $items->delivery_method or null }}">
			</strong><small>Shipping method</small><p></p>
			<input type="text" name="fill[delivery_method_id]" class="form-control" placeholder="Shipping method Id" value="{{ $items->delivery_method_id or null }}"><small>@if(is_object($items->delivery))
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
			<div class="@if($items->delivery_plan < now()) has-warning @endif has-feedback">
			<p></p>
			<input type="text" class="form-control date-container" name="fill[delivery_plan]"
					   placeholder="Month/Day/Year" value="{{ !empty($items->delivery_plan) ? Carbon\Carbon::parse($items->delivery_plan)->format('m/d/Y') : '' }}"/>
			@if($items->delivery_plan < now() && $items->delivery_real > now())
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
			<input type="text" class="form-control" name="fill[weight]"
					   placeholder="Shipping weight" value="{{ $items->weight or null }}"/>
			<small>Weight(g): calculated by content</small>
			<p></p>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[delivery_free]" data-on-color="success" data-on-text="Free&nbsp;delivery" data-off-text="Paid&nbsp;delivery" @if(isset($items->delivery_free) && $items->delivery_free == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[delivery_hold]" data-on-color="danger" data-off-color="info" data-on-text="Hold&nbsp;shipping" data-off-text="Allow&nbsp;shipping" @if(isset($items->delivery_hold) && $items->delivery_hold == true) checked @endif></div>
		</div>
		<div class="col-md-4"><p></p>
			<input type="text" class="form-control" name="fill[country]"
					   placeholder="Country" value="{{ $items->country or null }}"/>
			<p></p><input type="text" class="form-control" name="fill[city]"
					  placeholder="City" value="{{ $items->city or null }}"/>
			<p></p><input type="text" class="form-control" name="fill[address]"
					  placeholder="Address" value="{{ $items->address or null }}"/>
			<p></p><input type="text" class="form-control input-sm" name="fill[userbook_id]"
					  placeholder="Userbook Id" value="{{ $items->userbook_id or null }}"/>
			<small>Userbook 
				<a href="{{ route("admin.show", array("books", "filter" => "user", "filter_id" => $items->users_id)) }}" target="_blank">~ edit user books</a></small>
		</div>
	</div>
		
	@if(isset($items->userbook) && count($items->userbook)>0)
	<div class="rowdelimiter"></div>
	<h4>Book <small>delivery address</small></h4>
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.books', array('items' => array($items->userbook), 'skipOrder' => true))
		</div>
	</div>
	@endif
		
	<div class="rowdelimiter"></div>
		
	<h3><strong>Payment</strong></h3>
	<div class="row">
		<div class="col-md-3"><p></p><strong>
			<input type="text" name="fill[payment_method]" class="form-control" placeholder="Payment method" 
				   value="{{ $items->payment_method or null }}">
			</strong><small>Payment method</small>
		</div>
		<div class="col-md-3">
			<p></p>
			<input type="text" name="fill[payment_method_id]" class="form-control" placeholder="Payment method Id" value="{{ $items->payment_method_id or null }}"><small>@if(is_object($items->payment))
				{{ $items->payment->name }}:
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
	
	<h4>Bills</h4>
	@if(isset($items->bills) && count($items->bills)>0)
	<div class="row">
		<div class="col-sm-12">
			@include($template.'.lists.bills', array('items' => $items->bills, 'skipUser' => true))
		</div>
	</div>
	@endif	

	<div class="rowdelimiter"></div>
	
	<h3><strong>Order content</strong> <small>{{ app('veershop')->priceFormat($items->content_price) }}</small></h3>
	
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
			@include($template.'.lists.order-content', 
			array('items' => array_get($items->orderContent, 'content', array()), 'products' => isset($items->products) ? $items->products : array()))
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
	
	<div class="row">
		<div class="col-sm-12 text-center">
			<h2>
				{{ app('veershop')->priceFormat($items->content_price) }} + 
				{{ ($items->delivery_free == true) ? 'free' : app('veershop')->priceFormat($items->delivery_price) }} =
				{{ app('veershop')->priceFormat($items->price) }}
			</h2>
		</div>
	</div>	

	<h3>History</h3>
	@if(is_object($items->status_history))
	<ul class="list-group">
	@foreach($items->status_history as $history)
		<li class="list-group-item">
			{{ $history->pivot->name }}
			{{ $history->pivot->comments }}
			{{ $history->pivot->to_customer }}
			{{ $history->pivot->order_cache }}
			{{ $history->pivot->created_at }}
			{{ $history->pivot->updated_at }}
			{{ $history->name }}
			{{ $history->color }}
			{{ $history->flag_first }}
			{{ $history->flag_unreg }}
			{{ $history->flag_error }}
			{{ $history->flag_payment }}
			{{ $history->flag_delivery }}
			{{ $history->flag_close }}
			{{ $history->secret }}
			{{ $history->trashed() }}
		</li>
	@endforeach
	</ul>
	@endif
	
	<div class="rowdelimiter"></div>
	@if(isset($items->id))
	<div class="row">
		<div class="col-sm-12"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
	</div>
	<hr>
	<div class="row">
		<div class="col-xs-12">	
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "order", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->communications()->count() }} communications</a>
		</div>
	</div>
	@else
	<button type="submit" name="action" value="add" class="btn btn-danger btn-lg btn-block">Add</button>
	@endif
</div>
@if(isset($items->id))
<div class="action-hover-box"><button type="submit" name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
@endif
{{ Form::close() }}
@stop