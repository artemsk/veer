<div class="row">
		@foreach($items as $key => $item)	
		@if(round($key/6) == ($key/6)) <div class="clearfix"></div> @endif	
		<div class="col-lg-2 col-md-2 col-sm-3 text-center">
			<div class="thumbnail">
				<a href="{{ asset(config('veer.images_path').'/'.$item->img) }}" target="_blank">
					<img data-src="holder.js/100%x150/text:Not Found" src="{{ asset(config('veer.images_path').'/'.$item->img) }}" 
						 class="img-responsive"></a>
				<div class="caption"><small>#{{$item->id}}</small>
					<span class="label label-info">
						<a href="{{ route('admin.show', array('products', 'image' => $item->id)) }}" target="_blank">
							{{ $item->products->count() }}</a></span>
					<span class="label label-success">
						<a href="{{ route('admin.show', array('pages', 'image' => $item->id)) }}" target="_blank">
							{{ $item->pages->count() }}</a></span>
					<span class="label label-warning">
						<a href="{{ route('admin.show', array('categories', 'image' => $item->id)) }}" target="_blank">
							{{ $item->categories->count() }}</a></span>
					@if(!isset($denyDelete) || !$denyDelete)
					&nbsp;<button type="submit" class="btn btn-danger btn-xs" name="action" value="deleteImage.{{ $item->id }}"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					@else
					&nbsp;<button type="submit" class="btn btn-warning btn-xs" name="action" value="removeImage.{{ $item->id }}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					@endif
				</div>
			</div>
		</div>
		@endforeach	
	</div>