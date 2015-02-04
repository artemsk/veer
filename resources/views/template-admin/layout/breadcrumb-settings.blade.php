<ol class="breadcrumb">
	<li><strong>Settings</strong></li>
	@if($place == "configuration")
	<li class="active">Configuration</li>
	@else
	<li><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
	@endif
	@if($place == "components")
	<li class="active">Components</li>
	@else
	<li><a href="{{ route("admin.show", "components") }}">Components</a></li>
	@endif
	@if($place == "secrets")
	<li class="active">Secrets</li>
	@else
	<li><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
	@endif
	@if($place == "jobs")
	<li class="active">Jobs</li>
	@else
	<li><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>	
	@endif
	@if($place == "etc")
	<li class="active">etc.</li>
	@else
	<li><a href="{{ route("admin.show", "etc") }}">etc.</a></li>
	@endif
</ol>