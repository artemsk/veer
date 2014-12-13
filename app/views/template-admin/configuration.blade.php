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
	<h2 id="site{{ $site->id }}">{{ $site->url }} <small>sort by <a href="{{ route('admin.show', array('configuration', "sort" => "conf_key", "direction" => "asc")) }}">keys</a> | <a href="{{ route('admin.show', array('configuration', "sort" => "id", "direction" => "desc")) }}">id</a></small></h2>
	<div class="row">
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">			
			{{ Form::open(array('method' => 'put', 'files' => false, 'class' => 'veer-form-submit-configuration')); }}
			<div class="thumbnail newcard" id="cardnew{{ $site->id }}" >
				<div class="caption"><small>NEW CARD</small>
					<p><strong><input type="text" name="new[{{ $site->id}}][key]" class="form-control admin-form text-center newkey" 
									  placeholder="Key" value=""></strong></p>
									  <p><textarea name="new[{{ $site->id}}][value]" class="form-control newval" placeholder="Value"></textarea></p>
					<button type="submit" data-siteid="{{ $site->id }}" name="save[new]" class="btn btn-success btn-xs">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
				<input type="hidden" name="siteid" value="{{ $site->id }}">
				<input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
				<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">				
			</div>
			{{ Form::close() }}
		</div>		
		<div id="cardstock{{ $site->id }}">
				@include($template.'.lists.configuration-cards', array('configuration' => $site->configuration, 'siteid' => $site->id))	
		</div>
	</div>
	<div class="rowdelimiter"></div>
	@endforeach

</div>
@stop