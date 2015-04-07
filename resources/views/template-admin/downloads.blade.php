@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-elements', array('place' => 'downloads'))
	
<h1>Downloads: {{ veer_get('counted') }} files, {{ veer_get('temporary') }} active downloads</h1>
<br/>
<div class="container">
	
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="row">
		@foreach ($items['regrouped'] as $g => $group) 
		@if(round(array_get($items, 'index.'.$g)/4) == (array_get($items, 'index.'.$g)/4)) <div class="clearfix"></div> @endif
                @if(isset($group[1])) 
		<div class="col-md-3 col-sm-6">
			<h3>#{{ $items[head($group[1])]->id }}</h3>
			<div class="thumbnail text-center">		    
			<strong><small>{{ $items[head($group[1])]->fname }}</small></strong>
			<div class="caption">	
			<strong>{{ count($group[1]) }}</strong> elements, <strong>{{ count(@$group[0]) }}</strong> active
			</div>
			</div>			
			@foreach ($group as $key => $group_one)
			<ul class="list-group">
			@foreach ($group_one as $item)
			<li class="list-group-item {{ empty($key) ? 'active-download' : '' }}">
			@if($items[$item]->elements_type == "Veer\Models\Product")
				#{{ $items[$item]->id }} 
				<a href="{{ route("admin.show", array("products", "id" => $items[$item]->elements['id'])) }}">{{ $items[$item]->elements['title'] }}</a>
			@elseif($items[$item]->elements_type == "Veer\Models\Page")
				#{{ $items[$item]->id }} 
				<a href="{{ route("admin.show", array("pages", "id" => $items[$item]->elements['id'])) }}">{{ $items[$item]->elements['title'] }}</a>
			@else
				<span class="text-muted">#{{ $items[$item]->id }} Unused </span>
			@endif
			@if(empty($key))
			<small>
				<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>{{ $items[$item]->downloads }}
				<p>{{ $items[$item]->expiration_times }} | {{ $items[$item]->expiration_day  }}
				@if($items[$item]->expires > 0)
				@if($items[$item]->expiration_times > 0 && $items[$item]->downloads >= $items[$item]->expiration_times) 			
					<br/><span class="label label-success">expired</span>
				@elseif($items[$item]->expiration_day > \Carbon\Carbon::create(2000) && now() > $items[$item]->expiration_day)
					<br/><span class="label label-success">expired</span>
				@else
				@endif	
				@endif
				</p>
			</small>
			@endif
			@if($items[$item]->elements['id'] > 0 && $key > 0)
			<button type="submit" name="action" value="removeFile.{{ $items[$item]->id }}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
			@else
			<button type="submit" name="action" value="deleteFile.{{ $items[$item]->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
			@endif
			@if($items[$item]->elements['id'] > 0)
			@if($key > 0)
			<button type="button" name="action" value="makeRealLink.{{ $items[$item]->id }}" class="btn btn-info btn-xs" data-toggle="popover" title="Add New" 
						data-content='
						<div class="form-inline">
						<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
						<input name="_method" type="hidden" value="PUT">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<p><input type="text" class="form-control" placeholder="Maximum Downloads" size=6 name="times"></p>
						<p><input type="date" class="form-control" placeholder="Expiration Date" size=6 name="expiration_day"></p>
                                                <p><input type="text" class="form-control" placeholder="Link (optional)" size=6 name="link_name"></p>
						<p><button class="btn btn-info btn-xs" type="submit" name="action" value="makeRealLink.{{ $items[$item]->id }}">Make</button></p>
						</form>
						</div>
						' data-html="true"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> make link</button>
			@else
			@if(empty($items[$item]->secret))
			<span class="badge">bad copy</span>
			@else
			<span class="badge"><a href="{{ asset('/download/'.$items[$item]->secret) }}">download link</a></span>
			@endif
			@endif
			@endif
			</li>
			@endforeach
			</ul>
			@endforeach	
			<button type="button" class="btn btn-success btn-xs" data-toggle="popover" title="Add New" 
						data-content='
						<div class="form-inline">
						<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
						<input name="_method" type="hidden" value="PUT">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<p><input type="text" class="form-control" placeholder="Product Id" size=6 name="prdId"></p>
						<p><input type="text" class="form-control" placeholder="Page Id" size=6 name="pgId"></p>
						<p><button class="btn btn-success btn-xs" type="submit" name="action" value="copyFile.{{ $items[head($group[1])]->id }}">Add</button></p>
						</form>
						</div>
						' data-html="true"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> product | page</button>		
			<div class="rowdelimiter"></div>
		</div>
                @endif
		@endforeach

	</div>
	</form>
	<div class="rowdelimiter"></div>
	
	<div class="row">
		<div class="text-center">
			{{ $items->render() }}
		</div>
	</div>
	
	<hr>
		
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
	<input name="_method" type="hidden" value="PUT">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="row">
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="uploadFiles"  multiple=false></p></div>
	</div>	
	<div class="row">
		<div class="col-sm-4">
			<textarea class="form-control" name="attachFiles" placeholder="ID|NEW/blank [:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Connect existing|new files with products & pages. Example: 4:2,3:1 or :1:4,5,6 "></textarea>
			<p></p>
			<p><input class="form-control btn btn-primary" type="submit" value="Update | Upload"></p>
		</div>
	</div>
	</form>
</div>
@stop