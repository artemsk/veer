@extends($template.'.layout.base')

@section('body')

	@include($template.'.layout.breadcrumb-elements', array('place' => 'tags'))

<h1>Tags: {{ array_pull($items, 'counted') }}</h1>
<br/>
{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
<div class="container">

	<div class="row">
	@foreach($items as $key => $item)	
	@if(round($key/6) == ($key/6)) <div class="clearfix"></div> @endif		
	<div class="col-lg-2 round-element">		
		<input type="text" name="renameTag[{{ $item->id }}]" class="form-control admin-form text-center" value="{{ $item->name }}">
		<span class="label label-info"><a href="{{ route('admin.show', array('products', 'filter' => 'tags', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->products->count() }}</a></span>
		<span class="label label-success"><a href="{{ route('admin.show', array('pages', 'filter' => 'tags', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->pages->count() }}</a></span>
		&nbsp;<button type="submit" name="action" value="deleteTag.{{ $item->id }}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
		</div>
	@endforeach
	
	
	<div class="col-lg-2 round-element"><input type="text" name="newTag" class="form-control admin-form" placeholder="New tag [,] [:id:id]" data-toggle="tooltip" data-placement="bottom" data-html="true" title="Several tags comma separated.<br/>IDs of products & pages — :1,2,3:5,6" value=""></div>
	
	<div class="col-lg-2 round-element round-element-primary">
		<button type="submit" name="action" value="updateTags" class="btn btn-primary admin-form">Update</button>
	</div>
	
	</div>
	<div class="row">
		<div class="text-center">
			{{ $items->render() }}
		</div>
	</div>
</div>
</form>
@stop