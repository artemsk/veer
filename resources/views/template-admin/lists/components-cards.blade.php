@foreach($components as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail thumbnail-configuration-list" id="card{{$item->id}}">
		<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="veer-form-submit-configuration"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">		
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
							  title="app/components|events or page ID" data-toggle="tooltip" data-placement="bottom"></p>
                                        <p><small><input type="text" name="components[{{ $item->id }}][theme]" class="form-control admin-form text-center" placeholder="—" value="@if(!empty($item->theme)){{ $item->theme }}@endif"></small></p>
					<button type="submit"  data-siteid="{{ $siteid }}" class="btn btn-success btn-xs" name="save[{{$item->id}}]">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button> &nbsp;<button type="button" class="btn btn-info btn-xs copybutton" data-confname="{{ $item->route_name }}" data-conftype="{{ $item->components_type }}" data-confsrc="{{ $item->components_src }}"><span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button> &nbsp;<button type="submit"  data-siteid="{{ $siteid }}" class="btn btn-danger btn-xs" name="dele[{{$item->id}}]">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					<input type="hidden" name="siteid" value="{{ $siteid }}">	
				</div>
		</form>
			</div>
		</div>
@endforeach		
		