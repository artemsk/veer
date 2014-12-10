@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
	<li><strong>Elements</strong></li>
	<li><a href="{{ route("admin.show", "images") }}">Images</a></li>	
	<li><a href="{{ route("admin.show", "attributes") }}">Attributes</a></li>	
	<li><a href="{{ route("admin.show", "tags") }}">Tags</a></li>
	<li class="active">Downloads</li>		
	<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
</ol>
<h1>Downloads: {{ $items['counted'] }} files, {{ $items['temporary'] }} active downloads</h1>
<br/>
<div class="container">

	<div class="row">
		@foreach ($items['regrouped'] as $group) 
		<div class="col-md-3 col-sm-6">
			<h3>#{{ $items[head($group[1])]->id }}</h3>
			<div class="thumbnail text-center">		    
			<a href="{{ asset(config('veer.downloads_path').'/'.$items[head($group[1])]->fname) }}" 
			   target="_blank"><strong>{{ $items[head($group[1])]->fname }}</strong></a>
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
				<span class="text-muted">#{{ $items[$item]->id }} Unused</span>
			@endif			
			<small>
				<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>{{ $items[$item]->downloads }}
				<p>{{ $items[$item]->expires }} | {{ $items[$item]->expiration_day  }}</p>
			</small>
			<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
			@if($key > 0)
			<button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> make link</button>
			@else
			@if(empty($items[$item]->secret))
			<span class="badge">bad copy</span>
			@else
			<span class="badge"><a href="{{ asset('/download/'.$items[$item]->secret) }}">download link</a></span>
			@endif
			@endif
			</li>
			@endforeach
			</ul>
			@endforeach	
			<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> product | page</button>
		</div>		
		@endforeach

	</div>
	
	<div class="rowdelimiter"></div>
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
	
	<hr>
		
	{{ Form::open(array('method' => 'put', 'files' => true)); }}
	<div class="row">
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="InFile1" multiple=false></p></div>
	</div>	
	<div class="row">
		<div class="col-sm-4">
			<textarea class="form-control" placeholder="ID|NEW/blank [:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Connect existing|new files with products & pages. Example: 4:2,3:1 or :1:4,5,6 "></textarea>
			<p></p>
			<p>{{ Form::submit('Update | Upload', array('class' => 'form-control btn btn-primary')); }}</p>
		</div>
	</div>
	{{ Form::close() }}
</div>
@stop