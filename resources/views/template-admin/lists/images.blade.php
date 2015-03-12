<div class="row sortableImages">
		@foreach($items as $key => $item)	
		@if(round($key/6) == ($key/6)) <div class="clearfix"></div> @endif	
		<div class="col-lg-2 col-md-2 col-sm-3 text-center">
			<div class="thumbnail">
				<a href="{{ asset(config('veer.images_path').'/'.$item->img) }}" target="_blank">
					<img data-src="holder.js/100%x150/text:Not Found" src="{{ asset(config('veer.images_path').'/'.$item->img) }}" 
						 class="img-responsive"></a>
				<div class="caption"><small>#{{$item->id}}</small>
					@if(!isset($denyDelete) || !$denyDelete)
					<button type="submit" class="btn btn-default btn-xs" name="action" value="deleteImage.{{ $item->id }}"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					@else
					<button type="submit" class="btn btn-default btn-xs" name="action" value="removeImage.{{ $item->id }}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					@endif
					<span class="label label-info"><a href="{{ route('admin.show', array('products', 'filter' => 'images', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->products->count() }}</a></span>
					<span class="label label-success"><a href="{{ route('admin.show', array('pages', 'filter' => 'images', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->pages->count() }}</a></span>
                                        <span class="label label-warning"><a href="{{ route('admin.show', array('categories', 'image' => $item->id)) }}" target="_blank">{{ $item->categories->count() }}</a></span>
					<span class="label label-default"><a href="{{ route('admin.show', array('users', 'filter' => 'images', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->users->count() }}</a></span>					
				</div>
			</div>
		</div>
		@endforeach
                @if(count($items)>1 && (isset($denyDelete) || !empty($denyDelete)))
                <div class="clearfix"></div>
                <div class="col-lg-2 col-md-2 col-sm-3">
                    <button type="submit" class="btn btn-default btn-xs" name="action" value="removeAllImages"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Remove All</button>
                </div>
                @endif
	</div>