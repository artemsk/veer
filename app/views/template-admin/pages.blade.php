@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-structure', array('place' => 'pages'))

<h1>Pages: 
@if(!empty($items['filtered'])) 
 filtered by {{ $items['filtered'] }} <a href="{{ route("admin.show", array(array_pull($items, 'filtered'))) }}">
	 #{{ array_pull($items, 'filtered_id') }}</a> 
 
 @else
 {{ array_pull($items, 'counted') }} <small>| <a href="{{ route("admin.show", array("pages", "filter" => "unused")) }}">unused</a></small> 
@endif	
<a class="btn btn-default" href="{{ route("admin.show", array("pages", "id" => "new")) }}" role="button">Add</a></h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put', 'files' => false)); }}
	
	@include($template.'.lists.pages', array('items' => $items))
	
	</form>
	<div class="row">
		<div class="text-center">
			{{ $items->appends(array('filter' => Input::get('filter', null), 'filter_id' => Input::get('filter_id', null)))->links() }}
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
	</form>	
	
</div>
@stop