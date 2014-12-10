@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Settings</strong></li>
		<li><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
		<li><a href="{{ route("admin.show", "components") }}">Components</a></li>
		<li><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
		<li class="active">Jobs</li>		
		<li><a href="{{ route("admin.show", "etc") }}">etc.</a></li>	
	</ol>
<h1>Jobs <small>| failed jobs</small></h1>
<br/>
<div class="container">

	<div class="row">
		@foreach($items['jobs'] as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><small>#{{$item->id}}</small>					
					<strong><p>{{ $items['statuses'][$item->status] }} : {{ $item->times }}</p></strong>	
					<p>scheduled at:<br/>{{ $item->scheduled_at }}</p>	
					<p>created at:<br/>{{ $item->created_at }}</p>	
					<p><textarea class="form-control" rows="5">{{ $item->payload }}</textarea></p>	
					@if($items['statuses'][$item->status] != 'Started' && $items['statuses'][$item->status] != 'Finished')
					<button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-play" aria-hidden="true"></span> Run</button>
					@endif
					<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					
				</div>
			</div>
		</div>
		@endforeach	
		@foreach($items['failed'] as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><small>#{{$item->id}} FAILED</small>
					<p>{{ $item->failed_at }}</p>
					<p>{{ $item->connection }}</p>	
					<p>{{ $item->connection }}</p>		  
					<p>{{ $item->queue }}</p>	
					<p><textarea class="form-control" rows="5">{{ $item->payload }}</textarea></p>	
					<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
		@endforeach		
	</div>
</div>
@stop