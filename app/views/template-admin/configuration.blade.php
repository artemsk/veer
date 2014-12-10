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
		@foreach($site->configuration as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><small>#{{$item->id}}</small>
					<p><strong><input type="text" class="form-control admin-form text-center" 
									  placeholder="Key" value="{{ $item->conf_key }}"></strong></p>
									  <p><textarea class="form-control" placeholder="Value">{{ $item->conf_val }}</textarea></p>
					<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
					&nbsp;<button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button>
					&nbsp;<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
		@endforeach	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><small>NEW CARD</small>
					<p><strong><input type="text" class="form-control admin-form text-center" 
									  placeholder="Key" value=""></strong></p>
									  <p><textarea class="form-control" placeholder="Value"></textarea></p>
					<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
	</div>
	<div class="rowdelimiter"></div>
	@endforeach
</div>
@stop