@foreach($configuration as $item)
@if(is_object($item))
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail thumbnail-configuration-list" id="card{{$item->id}}">
		<form method="POST" action="{{ URL::full() }}#card{{$item->id}}" accept-charset="UTF-8" class="veer-form-submit-configuration"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="caption"><small>#{{$item->id}}</small>
					<p><strong><input name="configuration[{{$item->id}}][key]" type="text" class="form-control admin-form text-center" 
									  placeholder="Key" value="{{ $item->conf_key }}"></strong></p>
									  <p><textarea name="configuration[{{$item->id}}][value]" 
                                                                                       class="form-control transparent-textarea" placeholder="Value" rows="5">{{ $item->conf_val }}</textarea></p>
                                                                                                   <p><small><input type="text" name="configuration[{{ $item->id }}][theme]" class="form-control admin-form text-center" placeholder="—" value="@if(!empty($item->theme)){{ $item->theme }}@endif"></small></p>
					<button type="submit" data-siteid="{{ $siteid }}" name="save[{{$item->id}}]" class="btn btn-success btn-xs">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button> &nbsp;<button type="button" data-siteid="{{ $siteid }}" data-confkey="{{ $item->conf_key }}" data-confval='{{ $item->conf_val }}'  data-conftheme="@if(!empty($item->theme)){{ $item->theme }}@endif" data-confsiteid="{{ $siteid }}" class="btn btn-info btn-xs copybutton">
						<span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button> &nbsp;<button type="submit" data-siteid="{{ $siteid }}" name="dele[{{$item->id}}]" class="btn btn-danger btn-xs">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					<input type="hidden" name="siteid" value="{{ $siteid }}">
					<input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
					<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">								
				</div>
		</form>
			</div>
		</div>
@endif
@endforeach			
		