<ol class="breadcrumb">
	<li><strong>Settings</strong></li>
	@if($place == "configuration")
    <li class="active"><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
	@else
	<li><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
	@endif
	@if($place == "components")
    <li class="active"><a href="{{ route("admin.show", "components") }}">Components</a></li>
	@else
	<li><a href="{{ route("admin.show", "components") }}">Components</a></li>
	@endif
	@if($place == "secrets")
    <li class="active"><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
	@else
	<li><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
	@endif
	@if($place == "jobs")
    <li class="active"><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>
	@else
	<li><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>	
	@endif
	@if($place == "etc")
    <li class="active"><a href="{{ route("admin.show", "etc") }}">etc.</a></li>
	@else
	<li><a href="{{ route("admin.show", "etc") }}">etc.</a></li>
	@endif
</ol>