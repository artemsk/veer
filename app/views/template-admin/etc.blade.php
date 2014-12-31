@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Settings</strong></li>
		<li><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
		<li><a href="{{ route("admin.show", "components") }}">Components</a></li>
		<li><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
		<li><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>		
		<li class="active">etc.</li>	
	</ol>
<h1>Etc. <small>cache | migrations | password reminders</small></h1>
<br/>
<div class="container">

	{{ Form::open(array('method' => 'put', 'files' => false)); }}	
	<label>Raw Sql</label>
	<p><textarea class="form-control" name="freeFormSql" placeholder="Raw Sql [Update, Insert, Delete]"></textarea></p>
	<button type="submit" class="btn btn-default" name="action" value="runRawSql">Run</button>
	{{ Form::close() }}
</div>
@stop