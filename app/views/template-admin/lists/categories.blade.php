<ul class="list-group">
				@if(isset($items->categories) && count($items->categories)>0)	
				@foreach ($items->categories as $category)	
				<li class="list-group-item">
					<span class="badge">{{ $category->views }}</span>
					<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
					<a href="{{ route('admin.show', array('categories', 'category' => $category->id)) }}">{{ $category->title }}</a> 
					<small>{{ $category->remote_url }}</small>
				</li>	
				@endforeach
				@endif
				<li class="list-group-item">
						<input type="text" class="form-control" placeholder=":Existings IDs[,]" 
							   value="{{ !empty($items->fromCategory) ? ':'.$items->fromCategory : null }}">
				</li>
</ul>