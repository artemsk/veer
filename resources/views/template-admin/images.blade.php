@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-elements', array('place' => 'images'))
	
<h1>Images: 
	@if(Input::get('filter', null) == 'unused')
	<a href="{{ route("admin.show", "images") }}">{{ array_pull($items, 'counted') }}</a> <small>| unused</small>
	@else
	{{ array_pull($items, 'counted') }} <small>| <a href="{{ route("admin.show", array("images", "filter" => "unused")) }}">unused</a></small>
	@endif
	</h1>
<br/>
{{ Form::open(array('url'=> URL::full(), 'method' => 'put', 'files' => true)); }}
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
			<p>{{ Form::submit('Update', array('class' => 'form-control btn btn-primary')); }}</p>
		</div>
	</div>	
</div>
</form>
@stop