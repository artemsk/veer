@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li><a href="{{ route("admin.show", "sites") }}">Sites</a></li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		@if(!empty($items['filtered'])) 
		<li><a href="{{ route("admin.show", "products") }}"><strong>Products</strong></a></li>
		@else
		<li class="active">Products</li>
		@endif
</ol>
<h1>Products: 
@if(!empty($items['filtered'])) 
 filtered by {{ $items['filtered'] }} <a href="{{ route("admin.show", array(array_pull($items, 'filtered'))) }}">
	 #{{ array_pull($items, 'filtered_id') }}</a> 
@endif	
{{ array_pull($items, 'counted') }} <a class="btn btn-default" 
									   href="{{ route("admin.show", array("products", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	{{ Form::open(array('url' =>  URL::full(), 'method' => 'put', 'files' => false)); }}
	
	@include($template.'.lists.products', array('items' => $items))
	
	{{ Form::close() }}
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array('image' => Input::get('image', null), 'tag' => Input::get('tag', null)))->links() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	{{ Form::open(array('url' =>  URL::full(), 'method' => 'put', 'files' => true)); }}
	<label>Quick form: Add products</label>
	<div class="row">
		<div class="col-sm-3"><p><input type="text" name="fill[title]" class="form-control" placeholder="Title|Name"></p></div>
		<div class="col-sm-3"><p><input type="text" name="prices" class="form-control" 
										placeholder="Prices [price:sales:whole:base:currency]"></p></div>
		<div class="col-sm-3"><p><input type="text" name="categories"class="form-control" placeholder="Categories Id [,]"></p></div>
		<div class="col-sm-3"><p><input class="input-files-enhance" type="file" id="InFile1" name="uploadImage" multiple=false>Image</p></div>
	</div>	
	<div class="xs-rowdelimiter"></div>
	<div class="row">
		<div class="col-sm-6"><p><input type="text" name="fill[url]" class="form-control" placeholder="[Url]"></p></div>
		<div class="col-sm-3"><p><input type="text" name="options" class="form-control" placeholder="[Qty:weight:score:star:production code]"></p></div>
		<div class="col-sm-3"><p><input class="input-files-enhance" type="file" id="InFile2" name="uploadFile" multiple=false>Digital product</p></div>
	</div>	
	<div class="row">
		<div class="col-sm-6"><p>
			<textarea class="form-control" name="freeForm" placeholder="Title|Url|CategoryId,|Qty|Weight|Currency|Price|Sales|Whole|Base|SalesOn|SalesOff|ToShow|Score|Star|ImageFile|DownloadFile|ProductionCode|Status|@{{Description}}" rows="10" data-toggle="tooltip" data-placement="bottom" data-html="true" title=""></textarea></p>			
		</div>
		<div class="col-sm-6">
			<p>{{ Form::submit('Add', array('class' => 'form-control btn btn-danger')); }}</p>
		</div>
	</div>
	{{ Form::close() }}	
	
</div>
@stop