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
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'veer-form-submit-configuration')); }}
			<div class="thumbnail" id="cardnewsecret">
				<div class="caption"><p><small>NEW JOB</small></p>
					<strong><p><input type="text" class="form-control text-center" name="jobs[new][classname]"
									  placeholder="Classname in app/lib/Queues/"></p>		
					<p><input type="text" class="form-control text-center" name="jobs[new][data]"
									  placeholder="Array of data"></p></strong>			  
					<p><input class="form-control" placeholder="Repeat time [0]" 
							  name="jobs[new][repeat]" title="Set repeat time in minutes" data-toggle="tooltip" data-placement="bottom"></p>
					<p><input class="form-control date-container" placeholder="Start time [now|manual|date]" 
							  name="jobs[new][start]" title="Choose date to start" data-toggle="tooltip" data-placement="bottom"></p>
					<button type="submit" class="btn btn-success btn-xs" name="save" value="newjob">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
			</div>
			{{ Form::close() }}
		</div>
		@foreach($items['jobs'] as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'veer-form-submit-configuration')); }}	
			<div class="thumbnail" id="card{{$item->id}}">
				<div class="caption"><small>#{{$item->id}} —
							{{ \Carbon\Carbon::parse($item->updated_at)->format("m/d") }}</small>					
					<strong><p>{{ $items['statuses'][$item->status] }} : {{ $item->times }}</p></strong>	
					<p>scheduled at:<br/>{{ $item->scheduled_at }}</p>	
					<p>updated at:<br/>{{ $item->updated_at }}</p>	
					<p><textarea name="payload" class="form-control" rows="5">{{ $item->payload }}</textarea></p>	
					@if($items['statuses'][$item->status] != 'Started')
					<button type="submit" class="btn btn-info btn-xs" name="_run[{{ $item->id }}]">
						<span class="glyphicon glyphicon-play" aria-hidden="true"></span> Run</button>
					@endif
					@if($items['statuses'][$item->status] == 'Open' || $items['statuses'][$item->status] == 'Waiting')
					<button type="submit" class="btn btn-warning btn-xs" name="paus[{{ $item->id }}]">
						<span class="glyphicon glyphicon-pause" aria-hidden="true"></span> Pause</button>
					@endif					
					<button type="submit" class="btn btn-danger btn-xs" name="dele[{{ $item->id }}]">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					
				</div>
			</div>
			{{ Form::close() }}
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