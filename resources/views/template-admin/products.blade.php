@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-structure', array('place' => 'products'))

<h1>Products: {{ $items->total() }}
@if(!empty(veer_get('filtered_id')))
 filtered by {{ veer_get('filtered') }} <a href="{{ route("admin.show", array(veer_get('filtered'))) }}">
	 #{{ veer_get('filtered_id') }}</a>
 @else
 <small>| <a href="{{ route("admin.show", array("products", "filter" => "unused")) }}">unused</a></small> 
@endif	
 <a class="btn btn-default" href="{{ route("admin.show", array("products", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
	<input name="_method" type="hidden" value="PUT">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	
	@include($template.'.lists.products', array('items' => $items))
	
	</form>
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array('filter' => Input::get('filter', null), 'filter_id' => Input::get('filter_id', null)))->render() }}
		</div>
	</div>
	
	<div class='rowdelimiter'></div>
	<hr>
	<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
	<input name="_method" type="hidden" value="PUT">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<label>Quick form: Add products</label>
	<div class="row">
		<div class="col-sm-3"><p><input type="text" name="fill[title]" class="form-control" placeholder="Title|Name"></p></div>
		<div class="col-sm-3"><p><input type="text" name="prices" class="form-control" 
										placeholder="Prices [price:sales:whole:base:currency]"></p></div>
		<div class="col-sm-3"><p><input type="text" name="categories"class="form-control" placeholder="Categories Id [,]"></p></div>
		<div class="col-sm-3"><p><input class="input-files-enhance" type="file" id="InFile1" name="uploadImage[]" multiple=true>Image</p></div>
	</div>	
	<div class="xs-rowdelimiter"></div>
	<div class="row">
		<div class="col-sm-6"><p><input type="text" name="fill[url]" class="form-control" placeholder="[Url]"></p></div>
		<div class="col-sm-3"><p><input type="text" name="options" class="form-control" placeholder="[Qty:weight:score:star:production code]"></p></div>
		<div class="col-sm-3"><p><input class="input-files-enhance" type="file" id="InFile2" name="uploadFile[]" multiple=true>Digital product</p></div>
	</div>	
	<div class="row">
		<div class="col-sm-6"><p>
			<textarea class="form-control" name="freeForm" placeholder="Title|Url|CategoryId,|Qty|Weight|Currency|Price|Sales|Whole|Base|SalesOn|SalesOff|ToShow|Score|Star|ImageFile|DownloadFile|ProductionCode|Status|@{{Description}}" rows="10" data-toggle="tooltip" data-placement="bottom" data-html="true" title=""></textarea></p>			
		</div>
		<div class="col-sm-6">
			<p><input class="form-control btn btn-danger" type="submit" value="Add"></p>
		</div>
	</div>
	</form>	
	
</div>
@stop