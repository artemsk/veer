@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Settings</strong></li>
		<li class="active">Configuration</li>
		<li><a href="{{ route("admin.show", "components") }}">Components</a></li>
		<li><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
		<li><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>		
		<li><a href="{{ route("admin.show", "etc") }}">etc.</a></li>	
	</ol>
<h1>Configuration cards</h1>
<br/>
<div class="container">
	@foreach($items as $site)
	<h2 id="site{{ $site->id }}">{{ $site->url }}</h2>
	<div class="row">
		<div id="configurationsite{{ $site->id }}">
		@foreach($site->configuration as $item)
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail" id="card{{$item->id}}">
				@include($template.'.lists.configuration-cards', array('item' => $item, 'siteid' => $site->id))	
			</div>
		</div>
		@endforeach	
		</div>
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">			
			{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'veer-form-submit-configuration')); }}
			<div class="thumbnail" id="cardnew{{ $site->id }}" >
				<div class="caption"><small>NEW CARD</small>
					<p><strong><input type="text" name="new[{{ $site->id}}][key]" class="form-control admin-form text-center" 
									  placeholder="Key" value=""></strong></p>
									  <p><textarea name="new[{{ $site->id}}][value]" class="form-control" placeholder="Value"></textarea></p>
					<button type="submit" data-siteid="{{ $site->id }}" name="save[new]" class="btn btn-success btn-xs">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
	<div class="rowdelimiter"></div>
	@endforeach

</div>
@stop