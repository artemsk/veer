<ul class="list-group">
				@foreach($files as $file)
				<li class="list-group-item">
					<button type="submit" name="action" value="removeFile.{{ $file->id }}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
					<small>#{{ $file->id }}:</small>
					@if($file->original == true) 
					<span class="label label-default">original</span>
					@else
					<span class="label label-success">copy</span>
					@endif
					<strong> <a href='{{ asset(config('veer.downloads_path')."/".$file->fname) }}'>{{ $file->fname }}</a> </strong>
					@if(!empty($file->secret))
					<span class="label label-success"><a href='{{ asset('/download/'.$file->secret) }}'>download link</a></span>
					@endif
					@if($file->original != true)
					<span class="label label-success"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>{{ $file->downloads }}</span>
					@endif
					@if($file->expires > 0)
					<span class="label label-info">
						max {{ $file->expiration_times }}</span>
					@endif
					@if(Carbon\Carbon::parse($file->expiration_day)->timestamp > 0)
					<span class="label label-default">expiration {{ Carbon\Carbon::parse($file->expiration_day)->format('d M Y'); }}</span>
					@endif
					@if($file->expires > 0)
					@if($file->expiration_times > 0 && $file->downloads >= $file->expiration_times) 			
						<span class="label label-danger">expired</span>
					@elseif($file->expiration_day > \Carbon\Carbon::create(2000) && now() > $file->expiration_day)
						<span class="label label-danger">expired</span>
					@else
					@endif	
					@endif
					</small>				
				</li>
				@endforeach
</ul>