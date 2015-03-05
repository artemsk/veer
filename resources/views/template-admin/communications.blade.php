@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-user', array('place' => 'communications'))

<h1>Communications :{{ $items->total() }} <small>unread: {{ unread('communication') }} 
	@if(Input::get('filter',null) != null) 
	| filtered by <strong>#{{ Input::get('filter',null) }}:{{ Input::get('filter_id',null) }}</strong>
	@endif
	</small></h1>
<br/>
<div class="container">
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
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
					{{ '@' }}{{ $item->user->username or '?Unknown' }}</a>
				@endif			
			</strong>{{ empty($item->theme)? null : ": ".$item->theme }}
		</div>
		<ul class="list-group">
			<li class="list-group-item">
				@if(null != veer_get('recipients.'.$key))
					@foreach(veer_get('recipients.'.$key) as $r)
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
	</form>
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array(
					'filter' => Input::get('filter', null), 
					'filter_id' => Input::get('filter_id', null),
				))->render() }}
		</div>
	</div>	
	
	<div class='rowdelimiter'></div>
	<hr>
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	<label>Add message to anything as anybody</label>
	@include($template.'.layout.form-communication')
	</form>
</div>
@stop