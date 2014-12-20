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
<h1>Attributes: {{ count($items['grouped']) }} with {{ count($items)-2 }} values</h1>
<br/>
<div class="container">

	@foreach ($items['grouped'] as $name => $item)
	<div class="row">
        <div class="col-sm-12">	
				<p><strong><input type="text" class="form-control" placeholder="Name" value="{{ $name }}"></strong></p>
				
				<div class="row">
				@foreach ($item as $key => $val)	
				<div class="col-lg-2 col-md-6">
					<div class="attribute-form">		
					<input type="text" name="renameAttrValue[{{ $items[$val]->id }}]" 
						   class="form-control admin-form text-center" value="{{ $items[$val]->val }}">
					<textarea name="descrAttrValue[{{ $items[$val]->id }}]" class="form-control input-sm">{{ $items[$val]->descr }}</textarea>
					<p></p>
					<span class="label label-info"><a href="{{ route('admin.show', array('products', 'attribute' => $items[$val]->id)) }}" target="_blank">{{ $items[$val]->products->count() }}</a></span>
					<span class="label label-success"><a href="{{ route('admin.show', array('pages', 'attribute' => $items[$val]->id)) }}" target="_blank">{{ $items[$val]->pages->count() }}</a></span>
					&nbsp;<button type="submit" name="action" value="deleteAttrValue.{{ $items[$val]->id }}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>	
					</div>
				</div>
				@endforeach


				<div class="col-lg-2 round-element"><input type="text" name="newAttrValue" class="form-control admin-form" placeholder="New [:][,] [:id:id]" value=""></div>

				<div class="col-lg-2 round-element">
					<button type="submit" name="action" value="updateAttributes" class="btn btn-default admin-form">Update</button>
				</div>

				</div>
				
		<p><small>{{ $items['counted'][$name]['prd'] }} products, {{ $items['counted'][$name]['pg'] }} pages</small></p>
		</div>
	</div>
	<div class="rowdelimiter"></div>
	@endforeach

	<div class="col-sm-3 attribute-form">		
		<p><strong><input type="text" class="form-control" placeholder="New attribute name" value=""></strong></p>
		<p><textarea class="form-control" rows="10" placeholder="Values [:id:id]" name=""></textarea></p>	
		<button type="button" class="btn btn-default">Add</button>
	</div>
	<div class="col-sm-1"></div>

	<div class="col-sm-3 attribute-form"><small>		
		@foreach ($items['grouped'] as $name => $item)	 
		<p>{{ $name }} —  
			@foreach ($item as $val)
			<a href="{{ app('url')->current() }}?id={{ $items[$val]->id }}">{{ $items[$val]->val  }}</a> 
			@endforeach
		</p>
		@endforeach
	</small></div>
	<div class="col-sm-1"></div>
</div>
@stop