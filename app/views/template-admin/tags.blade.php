@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
	<li><strong>Elements</strong></li>
	<li><a href="{{ route("admin.show", "images") }}">Images</a></li>
	<li><a href="{{ route("admin.show", "attributes") }}">Attributes</a></li>	
	<li class="active">Tags</li>
	<li><a href="{{ route("admin.show", "downloads") }}">Downloads</a></li>		
	<li><a href="{{ route("admin.show", "comments") }}">Comments</a></li>	
</ol>
<h1>Tags: {{ array_pull($items, 'counted') }}</h1>
<br/>
<div class="container">

	<div class="row">
	@foreach($items as $item)	
	<div class="col-lg-2 round-element">		
		<input type="text" class="form-control admin-form text-center" value="{{ $item->name }}">
		<span class="label label-info">{{ $item->products->count() }}</span>
		<span class="label label-success">{{ $item->pages->count() }}</span>
		</div>
	@endforeach
	
	
	<div class="col-lg-2 round-element"><input type="text" class="form-control admin-form" placeholder="New tag [,] [:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Several tags comma separated.<br/>IDs of products & pages — :1,2,3:5,6" value=""></div>
	
	<div class="col-lg-2 round-element round-element-primary">
		<button type="submit" class="btn btn-primary admin-form">Update</button>
	</div>
	
	</div>
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
</div>
@stop