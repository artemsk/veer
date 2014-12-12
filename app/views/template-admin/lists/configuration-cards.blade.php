			
		{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'veer-form-submit-configuration')); }}
				<div class="caption"><small>#{{$item->id}}</small>
					<p><strong><input name="configuration[{{$item->id}}][key]" type="text" class="form-control admin-form text-center" 
									  placeholder="Key" value="{{ $item->conf_key }}"></strong></p>
									  <p><textarea name="configuration[{{$item->id}}][value]" 
												   class="form-control" placeholder="Value">{{ $item->conf_val }}</textarea></p>
					<button type="submit" data-siteid="{{ $siteid }}" name="save[{{$item->id}}]" class="btn btn-success btn-xs">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
					<!--&nbsp;<button type="button" data-siteid="{{ $siteid }}" class="btn btn-info btn-xs">
						<span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button>-->
					&nbsp;<button type="submit" data-siteid="{{ $siteid }}" name="dele[{{$item->id}}]" class="btn btn-danger btn-xs">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					<input type="hidden" name="siteid" value="{{ $siteid }}">
				</div>
		{{ Form::close() }}
		