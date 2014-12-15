@foreach($configuration as $item)
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail" id="card{{$item->id}}">		
		{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'veer-form-submit-configuration')); }}
				<div class="caption"><small>#{{$item->id}}</small>
					<p><strong><input name="configuration[{{$item->id}}][key]" type="text" class="form-control admin-form text-center" 
									  placeholder="Key" value="{{ $item->conf_key }}"></strong></p>
									  <p><textarea name="configuration[{{$item->id}}][value]" 
												   class="form-control" placeholder="Value">{{ $item->conf_val }}</textarea></p>
					<button type="submit" data-siteid="{{ $siteid }}" name="save[{{$item->id}}]" class="btn btn-success btn-xs">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
					<button type="button" data-siteid="{{ $siteid }}" data-confkey="{{ $item->conf_key }}" data-confval="{{ $item->conf_val }}" 
							class="btn btn-info btn-xs copybutton">
						<span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button>
					&nbsp;<button type="submit" data-siteid="{{ $siteid }}" name="dele[{{$item->id}}]" class="btn btn-danger btn-xs">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					<input type="hidden" name="siteid" value="{{ $siteid }}">
					<input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
					<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">								
				</div>
		{{ Form::close() }}
			</div>
		</div>
@endforeach			
		