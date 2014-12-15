@foreach($components as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail" id="card{{$item->id}}">
		{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'veer-form-submit-configuration')); }}		
				<div class="caption"><small>#{{$item->id}}</small>
					<p><strong><input type="text" name="components[{{$item->id}}][name]" class="form-control admin-form text-center" 
									  placeholder="Route name" value="{{ $item->route_name }}"></strong></p>
					<p><select class="form-control" placeholder="Component type" name="components[{{$item->id}}][type]">
							<option>{{ $item->components_type }}</option>
							<option>functions</option>
							<option>events</option>
							<option>pages</option>
						</select></p>				  
					<p><input class="form-control" placeholder="Component source" value="{{ $item->components_src }}"
							  name="components[{{$item->id}}][src]"
							  title="app/lib/components|events or page ID" data-toggle="tooltip" data-placement="bottom"></p>
					<button type="submit"  data-siteid="{{ $siteid }}" class="btn btn-success btn-xs" name="save[{{$item->id}}]">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
					&nbsp;<button type="button" class="btn btn-info btn-xs copybutton" data-confname="{{ $item->route_name }}" data-conftype="{{ $item->components_type }}" data-confsrc="{{ $item->components_src }}"><span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button>
					&nbsp;<button type="submit"  data-siteid="{{ $siteid }}" class="btn btn-danger btn-xs" name="dele[{{$item->id}}]">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					<input type="hidden" name="siteid" value="{{ $siteid }}">	
				</div>
		{{ Form::close() }}
			</div>
		</div>
@endforeach		
		