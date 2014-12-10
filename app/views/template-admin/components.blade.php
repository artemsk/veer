@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Settings</strong></li>
		<li><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
		<li class="active">Components</li>
		<li><a href="{{ route("admin.show", "secrets") }}">Secrets</a></li>
		<li><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>		
		<li><a href="{{ route("admin.show", "etc") }}">etc.</a></li>	
	</ol>
<h1>Components <small>functions | events | pages</small></h1>
<br/>
<div class="container">

	@foreach($items as $site)
	<h2 id="site{{ $site->id }}">{{ $site->url }}</h2>
	<div class="row">
		@foreach($site->components as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><small>#{{$item->id}}</small>
					<p><strong><input type="text" class="form-control admin-form text-center" 
									  placeholder="Route name" value="{{ $item->route_name }}"></strong></p>
					<p><select name="InType" class="form-control" placeholder="Component type">
							<option>{{ $item->components_type }}</option>
							<option>functions</option>
							<option>events</option>
							<option>pages</option>
						</select></p>				  
					<p><input class="form-control" placeholder="Component source" value="{{ $item->components_src }}" 
							  title="app/lib/components|events or page ID" data-toggle="tooltip" data-placement="bottom"></p>
					<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
					&nbsp;<button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button>
					&nbsp;<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
		@endforeach	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><small>NEW COMPONENT</small>
					<p><strong><input type="text" class="form-control admin-form text-center" 
									  placeholder="Route name" value=""></strong></p>
					<p><select name="InType" class="form-control" placeholder="Component type">
							<option>functions</option>
							<option>events</option>
							<option>pages</option>
						</select></p>				  
					<p><input class="form-control" placeholder="Component source" 
							  title="app/lib/components|events or page ID" data-toggle="tooltip" data-placement="bottom"></p>
					<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
	</div>
	<div class="rowdelimiter"></div>
	@endforeach
</div>
@stop