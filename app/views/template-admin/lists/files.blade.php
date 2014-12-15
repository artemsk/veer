<ul class="list-group">
				@foreach($files as $file)
				<li class="list-group-item">
					<button type="button" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
					<small>#{{ $file->id }}:</small>
					@if($file->original == true) 
					<span class="label label-default">original</span>
					@else
					<span class="label label-success">copy</span>
					@endif
					<strong> {{ $file->fname }} </strong>
					@if(!empty($file->secret))
					<span class="label label-success"><a href='{{ asset('/download/'.$file->secret) }}'>download link</a></span>
					@endif
					<span class="label label-success"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>{{ $file->downloads }}</span>
					@if($file->expires > 0)
					<span class="label label-info">
						max {{ $file->expires }}</span>
					@endif
					@if(Carbon\Carbon::parse($file->expiration_day)->timestamp > 0)
					<span class="label label-default">expiration {{ Carbon\Carbon::parse($file->expiration_day)->format('d M Y, H:i'); }}</span>
					@endif
					</small>				
				</li>
				@endforeach
</ul>