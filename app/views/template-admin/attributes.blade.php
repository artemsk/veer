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
<h1>Attributes: {{ count($items['grouped']) }} @if(count($items)>2) with {{ count($items)-2 }} values @endif</h1>
<br/>
<div class="container">
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	@foreach ($items['grouped'] as $name => $item)
	<div class="row">
        <div class="col-sm-12">	
				<p><strong><input type="text" class="form-control" placeholder="Name" name="renameAttrName[{{ $name }}]" value="{{ $name }}"></strong></p>
				<br/>
				<div class="row">
				@foreach ($item as $key => $val)	
				<div class="col-lg-2 col-md-6 text-center">
					<div class="attribute-form">		
					<input type="text" name="renameAttrValue[{{ $items[$val]->id }}]" 
						   class="form-control admin-form text-center" value="{{ $items[$val]->val }}">
					
					<div data-toggle="collapse" data-target="#descr{{ $items[$val]->id }}" class="attribute-descr"
					   aria-expanded="true" aria-controls="descr{{ $items[$val]->id }}">description</div>
					<div id="descr{{ $items[$val]->id }}" class="collapse @if(empty($items[$val]->descr)) out @else in @endif">
					<textarea name="descrAttrValue[{{ $items[$val]->id }}]" class="form-control input-sm">{{ $items[$val]->descr }}</textarea>
					</div>
					<p></p>
					<input class="page-checkboxes" data-on-text="descr" data-off-text="choose&nbsp;" data-size="mini" type="checkbox" value="1" name="attrType[{{ $items[$val]->id }}]" @if($items[$val]->type == "descr") checked @endif >
					<p></p>
					<small>#{{ $items[$val]->id }}</small>
					<span class="label label-info"><a href="{{ route('admin.show', array('products', 'attribute' => $items[$val]->id)) }}" target="_blank">{{ $items[$val]->products->count() }}</a></span>
					<span class="label label-success"><a href="{{ route('admin.show', array('pages', 'attribute' => $items[$val]->id)) }}" target="_blank">{{ $items[$val]->pages->count() }}</a></span>
					&nbsp;<button type="submit" name="action" value="deleteAttrValue.{{ $items[$val]->id }}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>	
					</div>
				</div>
				@endforeach
				
				<div class="col-lg-2 col-md-6">
					<input type="text" name="newAttrValue[{{ $name }}]" class="form-control" placeholder="New [,] [:id:id]" value="">
					<p></p><button type="submit" name="action" value="updateAttributes" class="btn btn-default">Update</button>
				</div>

				</div>
				
		<p><small>{{ $items['counted'][$name]['prd'] }} products, {{ $items['counted'][$name]['pg'] }} pages</small></p>
		</div>
	</div>
	<div class="rowdelimiter"></div>
	@endforeach

	{{ Form::close() }}
	
	<div class="row">
		<div class="text-center">
			{{ $items->links() }}
		</div>
	</div>
	
	{{ Form::open(array('url'=> URL::full(), 'method' => 'put')); }}
	<div class="row">		
	<div class="col-sm-3">
		<div class="attribute-form">
		<p><strong><input type="text" class="form-control" name="newName" placeholder="New attribute name" value=""></strong></p>
		<p><textarea class="form-control" rows="10" name="newValue" placeholder="Values [:id:id]" name=""></textarea></p>	
		<button type="submit" name="action" value="newAttribute" class="btn btn-default">Add</button>
		</div>
	</div>
	@if(count($items['grouped'])>0) 
	<div class="col-sm-9">
		<div class="attribute-form">
		<small>		
		@foreach ($items['grouped'] as $name => $item)	 
		<p>{{ $name }} —  
			@foreach ($item as $val)
			<strong>{{ $items[$val]->val  }}</strong>
			@endforeach
		</p>
		@endforeach
		</small>
		</div>
	</div>
	@endif
	</div>
	{{ Form::close() }}
</div>
@stop