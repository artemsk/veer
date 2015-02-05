@extends($template.'.layout.base')

@section('body')
	
	@include($template.'.layout.breadcrumb-settings', array('place' => 'components'))

<h1>Components <small>functions | events | pages</small></h1>
<br/>
<div class="container">

	@foreach($items as $site)
	<h2 id="site{{ $site->id }}">{{ $site->url }} <small>sort by <a href="{{ route('admin.show', array('components', "sort" => "route_name", "direction" => "asc")) }}">route name</a> | <a href="{{ route('admin.show', array('components', "sort" => "id", "direction" => "desc")) }}">id</a></small></h2>
	<div class="row">
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="veer-form-submit-configuration">
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="thumbnail newcard" id="cardnew{{ $site->id }}">
				<div class="caption"><small>NEW COMPONENT</small>
					<p><strong><input type="text" name="new[{{ $site->id}}][name]" class="form-control admin-form text-center newname" 
									  placeholder="Route name" value=""></strong></p>
					<p><select class="form-control newtype" placeholder="Component type" name="new[{{ $site->id}}][type]">
							<option>functions</option>
							<option>events</option>
							<option>pages</option>
						</select></p>				  
					<p><input class="form-control newsrc" placeholder="Component source" name="new[{{ $site->id}}][src]" 
							  title="app/lib/components|events or page ID" data-toggle="tooltip" data-placement="bottom"></p>
					<button type="submit" data-siteid="{{ $site->id }}" class="btn btn-success btn-xs" name="save[new]">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
				<input type="hidden" name="siteid" value="{{ $site->id }}">
				<input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
				<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">
			</div>
			</form>
		</div>		
		<div id="cardstock{{ $site->id }}">
				@include($template.'.lists.components-cards', array('components' => $site->components, 'siteid' => $site->id))	
		</div>	
	</div>
	<div class="rowdelimiter"></div>
	@endforeach
</div>
@stop