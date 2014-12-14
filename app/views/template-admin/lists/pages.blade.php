	<div class="row">
		@foreach($items as $item)	
		<div class="col-lg-2 col-md-3 col-sm-6 text-center">
			<div class="thumbnail @if($item->hidden == true)
				 bg-muted
				 @endif ">
				@if(count($item->images)>0)
				<a href="{{ asset(config('veer.images_path').'/'.$item->images[0]->img) }}" target="_blank">
					<img data-src="holder.js/100%x150/text:Not Found" 
						 src="{{ asset(config('veer.images_path').'/'.$item->images[0]->img) }}" class="img-responsive 
						 @if($item->hidden == true) image-faded @endif"></a>
				@else
				<img data-src="holder.js/100%x150/vine/text:{{ $item->title }}" 
						 class="img-responsive @if($item->hidden == true) image-faded @endif">
				@endif		 
				<div class="caption @if($item->hidden == true) image-faded @endif">
					<a href="{{ route('admin.show', array("pages", "id" => $item->id)) }}"><strong>{{ $item->title }}</strong></a>
					<p>{{ Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}<Br/>
						<small>#{{$item->id}} 
						@if(count($item->comments) > 0)
						&nbsp;<span class="glyphicon glyphicon-comment" aria-hidden="true" title="Comments"></span> {{ count($item->comments) }}
						@endif
						@if(count($item->subpages) > 0)
						&nbsp;<span class="glyphicon glyphicon-asterisk" aria-hidden="true" title="Sub pages"></span> {{ count($item->subpages) }}
						@endif
						@if(count($item->categories) <= 0)
						&nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
						@else
						&nbsp;<span class="glyphicon glyphicon-heart warning-icon" aria-hidden="true" title="Categories"></span> {{ 
							count($item->categories) }}
						@endif
						@if(is_object($item->user))
						<br/><a href="{{ route('admin.show', array('users', 'id' => $item->user->id)) }}">{{ '@'.$item->user->firstname }}</a>
						@endif
						</small></p>
					@if ($item->hidden == false)
					<button type="submit" name="action" value="changeStatusPage[{{ $item->id }}]" class="btn btn-success btn-xs" title="Current: ON (SHOW)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
					@else
					<button type="submit" name="action" value="changeStatusPage[{{ $item->id }}]" class="btn btn-warning btn-xs" title="Current: OFF (HIDDEN)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span></button>
					@endif
					@if(!isset($denyDelete) || !$denyDelete)
					&nbsp;<button type="submit" name="action" value="deletePage[{{ $item->id }}]" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					@else
					&nbsp;<button type="submit" name="action" value="removePage[{{ $item->id }}]" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					@endif
				</div>
			</div>
			@if($item->original == true || (File::exists( config('veer.htmlpages_path') . '/' . $item->id . '.html'))) 
			<div class="top-panel">
				@if($item->original == true)
					<span class="glyphicon glyphicon-th" aria-hidden="true"></span>
				@else
					<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
				@endif
			</div>
			@endif
			<div class="top-panel-right">
					<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $item->views }}&nbsp;
			</div>
		</div>
		@endforeach	
	</div>