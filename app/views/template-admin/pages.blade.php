@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		@if(!empty($items['filtered'])) 
		<li><a href="{{ route("admin.show", "pages") }}"><strong>Pages</strong></a></li>
		@else
		<li class="active">Pages</li>
		@endif
		<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
</ol>
<h1>Pages: 
@if(!empty($items['filtered'])) 
 filtered by {{ $items['filtered'] }} <a href="{{ route("admin.show", array(array_pull($items, 'filtered'))) }}">
	 #{{ array_pull($items, 'filtered_id') }}</a> 
@endif	
{{ array_pull($items, 'counted') }}  <a class="btn btn-default" 
									   href="{{ route("admin.show", array("pages", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put', 'files' => false)); }}
	
	@include($template.'.lists.pages', array('items' => $items))
	
	{{ Form::close() }}
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array('image' => Input::get('image', null), 'tag' => Input::get('tag', null)))->links() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put', 'files' => true)); }}
	<label>Quick form: Add page</label>
	<div class="row">
		<div class="col-sm-4"><p><input type="text" class="form-control" placeholder="Title" name="title"></p></div>
		<div class="col-sm-4"><p><input type="text" class="form-control" placeholder="Categories Id [,]" name="categories"></p></div>
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="attachImage" multiple=false>Image</p></div>
	</div>	
	<div class="xs-rowdelimiter"></div>
	<div class="row">
		<div class="col-sm-8"><p><input type="text" class="form-control" placeholder="[Url]" name="url"></p></div>
		<div class="col-sm-4"><p><input class="input-files-enhance" type="file" id="InFile1" name="attachFile" multiple=false>Attach file (*.html for full replacement)</p></div>
	</div>	
	<div class="row">
		<div class="col-sm-6"><p>
			<textarea class="form-control" placeholder="@{{Small txt}} Txt" rows="10" name="txt"></textarea></p>			
		</div>
		<div class="col-sm-6">
			<p>{{ Form::submit('Add', array('class' => 'form-control btn btn-danger')); }}</p>
		</div>
	</div>
	{{ Form::close() }}	
	
</div>
@stop