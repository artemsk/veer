<div class="row">
		@foreach($items as $item)	
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
					&nbsp;<button type="submit" class="btn btn-danger btn-xs" name="action" value="deleteImage[{{ $item->id }}]"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
		@endforeach	
	</div>