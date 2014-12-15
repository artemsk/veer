@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
	<li><strong>Elements</strong></li>
	<li class="active">Images</li>
	<li><a href="{{ route("admin.show", "attributes") }}">Attributes</a></li>	
	<li><a href="{{ route("admin.show", "tags") }}">Tags</a></li>
	<li><a href="{{ route("admin.show", "downloads") }}">Downloads</a></li>		
	<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
</ol>
<h1>Images: {{ array_pull($items, 'counted') }}</h1>
<br/>
<div class="container">

	@include($template.'.lists.images', array('items' => $items))
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
	
	<hr>
	{{ Form::open(array('method' => 'put', 'files' => true)); }}
	<label>Upload Images</label>
	<div class="row">
		<div class="col-sm-4">
			<p><input class="input-files-enhance" type="file" id="InFile1" name="InFile1" multiple=false></p>
			<p><input class="input-files-enhance" type="file" id="InFile2" name="InFile2" multiple=false></p>
			<p><input class="input-files-enhance" type="file" id="InFile3" name="InFile3" multiple=false></p></div>
	</div>	
	<div class="row">
		<div class="col-sm-4">
			<textarea class="form-control" placeholder="ID|NEW [:id:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Connect existing|new images with products, pages, categories. Example: 4:2,3:1 or NEW:1:4,5,6 "></textarea>
			<p></p>
			<p>{{ Form::submit('Update', array('class' => 'form-control btn btn-primary')); }}</p>
		</div>
	</div>
	{{ Form::close() }}
	
</div>
@stop