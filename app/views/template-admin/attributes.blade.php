@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
	<li><strong>Elements</strong></li>
	<li><a href="{{ route("admin.show", "images") }}">Images</a></li>
	<li class="active">Attributes</li>
	<li><a href="{{ route("admin.show", "tags") }}">Tags</a></li>
	<li><a href="{{ route("admin.show", "downloads") }}">Downloads</a></li>		
	<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
</ol>   
<h1>Attributes: {{ count($items['grouped']) }} with {{ count($items)-2 }} values</h1>
<br/>
<div class="container">

	@foreach ($items['grouped'] as $name => $item)	  
        <div class="col-sm-3 attribute-form">
			<p><button type="button" class="btn btn-danger">Delete group</button></p>			
				<p><strong><input type="text" class="form-control" placeholder="Name" value="{{ $name }}"></strong></p>
				<p><textarea class="form-control" rows="10" name="">@foreach ($item as $val)
{{ $items[$val]->val }}

@endforeach
</textarea></p>	
<p><small>{{ $items['counted'][$name]['prd'] }} products,<br/>{{ $items['counted'][$name]['pg'] }} pages</small></p>
<button type="button" class="btn btn-default">Update</button>
		</div>
	<div class="col-sm-1"></div>		
	@endforeach

	<div class="col-sm-3 attribute-form">		
		<p><strong><input type="text" class="form-control" placeholder="New attribute name" value=""></strong></p>
		<p><textarea class="form-control" rows="10" placeholder="Values [:id:id]" name=""></textarea></p>	
		<button type="button" class="btn btn-default">Add</button>
	</div>
	<div class="col-sm-1"></div>

	<div class="col-sm-3 attribute-form"><small>		
		@foreach ($items['grouped'] as $name => $item)	 
		<p>{{ $name }} —  
			@foreach ($item as $val)
			<a href="{{ app('url')->current() }}?id={{ $items[$val]->id }}">{{ $items[$val]->val  }}</a> 
			@endforeach
		</p>
		@endforeach
	</small></div>
	<div class="col-sm-1"></div>
</div>
@stop