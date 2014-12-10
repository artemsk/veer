@extends($template.'.layout.base')

@section('body')
	<ol class="breadcrumb">
		<li><strong>Settings</strong></li>
		<li><a href="{{ route("admin.show", "configuration") }}">Configuration</a></li>
		<li><a href="{{ route("admin.show", "components") }}">Components</a></li>
		<li class="active">Secrets</li>
		<li><a href="{{ route("admin.show", "jobs") }}">Jobs</a></li>		
		<li><a href="{{ route("admin.show", "etc") }}">etc.</a></li>	
	</ol>
<h1>Secrets</h1>
<br/>
<div class="container">

	<div class="row">
		@foreach($items as $item)	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><p><small>#{{$item->id}} — 
							{{ Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}</small></p>					
					<strong><p><select name="InType" class="form-control">
							<option>{{ $item->elements_type }}</option>
							<option>Veer\Models\Product</option>
							<option>Veer\Models\Page</option>
							<option>Veer\Models\Order</option>
							</select></p>		
					<p><input type="text" class="form-control text-center" 
									  placeholder="Elements ID" value="{{ $item->elements_id }}"></p></strong>			  
					<p><input class="form-control" placeholder="Password for access" value="{{ $item->secret }}" 
							  title="Password for access" data-toggle="tooltip" data-placement="bottom"></p>
					<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
					&nbsp;<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
		@endforeach	
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail">
				<div class="caption"><p><small>NEW PASSWORD | SECRET</small></p>
					<strong><p><select name="InType" class="form-control">
							<option>Veer\Models\Product</option>
							<option>Veer\Models\Page</option>
							<option>Veer\Models\Order</option>
							</select></p>		
					<p><input type="text" class="form-control text-center" 
									  placeholder="ID"></p></strong>			  
					<p><input class="form-control" placeholder="Password for access" value="{{ str_random(64) }}" 
							  title="Password for access" data-toggle="tooltip" data-placement="bottom"></p>
					<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
			</div>
		</div>
	</div>
</div>
@stop