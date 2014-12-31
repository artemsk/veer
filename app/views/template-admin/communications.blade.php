@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Users</strong></li>
		<li><a href="{{ route("admin.show", "users") }}">Users</a></li>
		<li><a href="{{ route("admin.show", "books") }}">Books</a></li>
		<li><a href="{{ route("admin.show", "lists") }}">Lists</a></li>
		<li><a href="{{ route("admin.show", "searches") }}">Searches</a></li>			
		<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
		@if(Input::get('filter',null) != null) 
		<li><strong><a href="{{ route("admin.show", "communications") }}">Communications</a></strong></li>
		@else
		<li class="active">Communications</li>
		@endif			
		<li><a href="{{ route("admin.show", "roles") }}">Roles</a></li>
	</ol> 
<h1>Communications :{{ array_pull($items, 'counted', 0) }} <small>| 
		@if(Input::get('filter',null) != null) 
	filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
	@else emails | ims | etc.
	@endif
	</small></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<ul class="list-group">
	@foreach($items as $key => $item)
		@if($key !== 'recipients')
		<div class="panel @if($item->intranet == true) panel-info @else panel-default @endif">
		<div class="panel-heading">
			<button type="submit" name="deleteMessage[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
			&nbsp;
			@if($item->hidden > 0)
			<button type="submit" name="unhideMessage[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></button>
			@else
			<button type="submit" name="hideMessage[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
			@endif
			&nbsp;
			<small>#{{ $item->id }}</small>
			@if(is_object($item->site))~ {{ $item->site->configuration->first()->conf_val or $item->site->url; }} @endif
			<strong>:
			@if(!empty($item->url))
			<a href="{{ route("admin.show", array("communications", "filter" => "url", "filter_id" => $item->url)) }}">{{ $item->url }}</a>
			@endif
			
			@if($item->elements_type == "Veer\Models\Product")
				<a href="{{ route("admin.show", array("products", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@elseif($item->elements_type == "Veer\Models\Page")
				<a href="{{ route("admin.show", array("pages", "id" => $item->elements_id)) }}">{{ $item->elements->title or '[?] Unknown' }}</a>
			@elseif($item->elements_type == "Veer\Models\Category")
				<a href="{{ route("admin.show", array("categories", "category" => $item->elements_id)) }}">
				{{ $item->elements->title or '[?] Unknown' }}</a>	
			@elseif($item->elements_type == "Veer\Models\Order")
				<a href="{{ route("admin.show", array("orders", "id" => $item->elements_id)) }}">
				@if(is_object($item->elements)) 
				Order # ID {{ app('veershop')->getOrderId($item->elements->cluster, $item->elements->cluster_oid) }}@else [?] Unknown @endif</a>				
			@else
			@endif
			</strong>
			@if($item->email_notify == true)
			&nbsp;
			<span class="label label-warning">
				<span class="glyphicon glyphicon-send text-danger" aria-hidden="true" title="Email notified"></span>
				&nbsp;Notified
			</span>
			@endif
			&nbsp;
			<span class="label @if($item->type == 'email') label-primary @else label-info @endif">
				<a href="{{ route("admin.show", array("communications", "filter" => "type", "filter_id" => $item->type)) }}">{{ $item->type }}</a></span>
			&nbsp;
			<span class="badge" title="views">{{ $item->views }}</span>
			<span class="badge">{{ $item->created_at }}</span>
			@if($item->updated_at != $item->created_at)
			<span class="badge">{{ $item->updated_at }}</span>
			@endif
		</div>
		<div class="panel-body">
			<strong>
				@if(!empty($item->sender))
				{{ $item->sender }}
				@endif
				@if(!empty($item->sender_email))
				| {{ $item->sender_email }}
				@endif
				@if(!empty($item->sender_phone))
				| {{ $item->sender_phone }}
				@endif
				@if($item->users_id > 0)
				<a href="{{ route('admin.show', array("users", "id" => empty($item->users_id) ? '' : $item->users_id)) }}">
					{{ '@'.$item->user->username }}</a>
				@endif			
			</strong>{{ empty($item->theme)? null : ": ".$item->theme }}
		</div>
		<ul class="list-group">
			<li class="list-group-item">
				@if(isset($items['recipients'][$key])) 
					@foreach($items['recipients'][$key] as $r)
					<a href="{{ route('admin.show', array("users", "id" => empty($r->id) ? '' : $r->id)) }}">
						{{ '@' }}{{ $r->username or '?' }}</a>
					@endforeach
				@endif
				{{ $item->message }}
				@if($item->public == true) <span class="badge">public</span> @endif
			</li>
		</ul>
		</div>
		@endif
	@endforeach
	</ul>
	{{ Form::close() }}
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->links() }}
		</div>
	</div>	
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<label>Add message to anything as anybody</label>
	<div class="row">
        <div class="col-md-6">
			<div class="form-group">
                <input type="text" class="form-control" name="fill[sender]" placeholder="Sender">
			</div>
			<div class="form-group">
                <input type="tel" class="form-control" name="fill[sender_phone]" placeholder="Sender Phone">
			</div>
			<div class="form-group">
                <input type="email" class="form-control" name="fill[sender_email]" placeholder="Sender Email">
			</div>
			<div class="form-group">
                <textarea class="form-control" rows="3" name="message" placeholder="Message @recipient @recipient"></textarea>
			</div> 
			<div class="form-group">
                <input type="text" class="form-control" name="fill[theme]" placeholder="Theme">
			</div>
			<div class="form-group">
                <input type="text" class="form-control" name="fill[type]" placeholder="Label | Type (IM, email, callme etc.)">
			</div> 
			<div class="checkbox">
                <label>
					<input type="checkbox" name="checkboxes[public]" checked> Public
                </label>
			</div>			
			<div class="checkbox">
                <label>
					<input type="checkbox" name="checkboxes[email_notify]"> Email Notify
                </label>
			</div>
			<div class="checkbox">
                <label>
					<input type="checkbox" name="checkboxes[intranet]"> Intranet
                </label>
			</div>
			<div class="checkbox">
                <label>
					<input type="checkbox" name="checkboxes[hidden]"> Hidden
                </label>
			</div>			

		</div> 
		<div class="col-md-6">
			<div class="form-group">
                <input type="text" class="form-control" name="fill[sites_id]" placeholder="Sites ID">
			</div> 
			<div class="form-group">
                <input type="text" class="form-control" name="fill[users_id]" placeholder="Users ID [or empty for current]">
			</div> 			
			<div class="form-group">
				<label>Place on specific Url</label>
				<input type="url" class="form-control" name="fill[url]" placeholder="Url">
			</div> 
			<div class="form-group">
				<label>Place on Product | Page | Category | Order</label>
				<textarea class="form-control" name="connected" rows="3" placeholder="page|product|category|order:id"></textarea>
			</div>   
			<button type="submit" name="action" value="addMessage" class="btn btn-default">Submit</button> 
		</div> 
	</div>
	{{ Form::close() }}
</div>
@stop