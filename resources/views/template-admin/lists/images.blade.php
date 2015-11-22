<div class="row sortableImages">
		@foreach($items as $key => $item)	
		@if(round($key/6) == ($key/6)) <div class="clearfix visible-lg-block"></div> @endif
                @if(round($key/4) == ($key/4)) <div class="clearfix visible-md-block"></div> @endif
                @if(round($key/2) == ($key/2)) <div class="clearfix visible-sm-block"></div> @endif
		<div class="col-lg-2 col-md-3 col-sm-6 text-center">
			<div class="thumbnail thumbnail-image-list">
				<a href="@if(config('veer.use_cloud_images')){{ config('veer.cloudstorage_path').'/' }}@else{{ asset('') }}@endif{{ (config('veer.images_path').'/'.$item->img) }}" target="_blank">
					<img data-src="holder.js/100%x150/text:Not Found" src="@if(config('veer.use_cloud_images')){{ config('veer.cloudstorage_path').'/' }}@else{{ asset('') }}@endif{{ (config('veer.images_path').'/'.$item->img) }}"
						 class="img-responsive thumbnail-image"></a>
                <div class="caption" style="padding-left: 0px; padding-right: 0px;"><nobr>@if(!isset($denyDelete) || !$denyDelete)<button type="submit" class="btn btn-link btn-xs" name="action" value="deleteImage.{{ $item->id }}" style="padding: 0px;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>@else<button type="submit" class="btn btn-link btn-xs" name="action" value="removeImage.{{ $item->id }}" style="padding: 0px;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>@endif&nbsp;<small>#{{$item->id}}</small>&nbsp;<span class="label label-info"><a href="{{ route('admin.show', array('products', 'filter' => 'images', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->products->count() }}</a></span> <span class="label label-success"><a href="{{ route('admin.show', array('pages', 'filter' => 'images', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->pages->count() }}</a></span> <span class="label label-warning"><a href="{{ route('admin.show', array('categories', 'image' => $item->id)) }}" target="_blank">{{ $item->categories->count() }}</a></span> <span class="label label-default"><a href="{{ route('admin.show', array('users', 'filter' => 'images', 'filter_id' => $item->id)) }}" target="_blank">{{ $item->users->count() }}</a></span></nobr>					
				</div>
			</div>
		</div>
		@endforeach
	</div>
