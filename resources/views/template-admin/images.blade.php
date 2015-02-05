@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-elements', array('place' => 'images'))
	
<h1>Images: 
	@if(Input::get('filter', null) == 'unused')
	<a href="{{ route("admin.show", "images") }}">{{ array_pull($data, 'counted') }}</a> <small>| unused</small>
	@else
	{{ array_pull($data, 'counted') }} <small>| <a href="{{ route("admin.show", array("images", "filter" => "unused")) }}">unused</a></small>
	@endif
	</h1>
<br/>
<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
<input name="_method" type="hidden" value="PUT">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="container">

	@include($template.'.lists.images', array('items' => $items))
	
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array('filter' => Input::get('filter', null)))->render() }}
		</div>
	</div>
	
	<hr>
	
	<label>Upload Images</label>
	<div class="row">
		<div class="col-sm-4">
			<p><input class="input-files-enhance" type="file" id="InFile1" name="uploadImages1" multiple=false></p>
			<p><input class="input-files-enhance" type="file" id="InFile2" name="uploadImages2" multiple=false></p>
			<p><input class="input-files-enhance" type="file" id="InFile3" name="uploadImages3" multiple=false></p></div>
	</div>	
	<div class="row">
		<div class="col-sm-4">
			<textarea class="form-control" name="attachImages" placeholder="ID|NEW [:id:id:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Connect existing|new images with products, pages, categories, users. Example: 4:2,3:1 or NEW:1:4,5,6 "></textarea>
			<p></p>
			<p><input class="form-control btn btn-primary" type="submit" value="Update"></p>
		</div>
	</div>	
</div>
</form>
@stop