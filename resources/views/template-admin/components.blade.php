@extends($template.'.layout.base')

@section('body')
	
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-settings', array('place' => 'components'))</div>

            <h3 class="hidden-md">Components<br/><small>functions | events | pages</small></h3>

        </div>
        <div class="visible-xs-block visible-sm-block sm-rowdelimiter"></div>
        <div class="col-md-10 main-content-block settings-column">
	@foreach($items as $site)
	<h2 id="site{{ $site->id }}">{{ $site->url }} <small>sort by <a href="{{ route('admin.show', array('components', "sort" => "route_name", "direction" => "asc")) }}">route name</a> | <a href="{{ route('admin.show', array('components', "sort" => "id", "direction" => "desc")) }}">id</a></small></h2>
        @foreach($site->components->groupBy('theme') as $theme => $value)

        <h4 class="configuraton-component-group" data-toggle="collapse" data-target='#collapsed{{ $site->id.$theme }}'><span class="glyphicon glyphicon-chevron-down danger-icon"></span> {{ $theme }} <small>{{ count($value) }}</small></h4>

	<div class="row collapse in" id="collapsed{{ $site->id.$theme }}">
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<form method="POST" action="{{ URL::full() }}#collapsed{{ $site->id.$theme }}" accept-charset="UTF-8" class="veer-form-submit-configuration">
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="thumbnail newcard thumbnail-configuration-list" id="cardnew{{ $site->id.$theme }}">
				<div class="caption"><small>NEW COMPONENT</small>
					<p><strong><input type="text" name="new[{{ $site->id}}][name]" class="form-control admin-form text-center newname"
									  placeholder="Route name" value=""></strong></p>
					<p><select class="form-control newtype" placeholder="Component type" name="new[{{ $site->id}}][type]">
							<option>functions</option>
							<option>events</option>
							<option>pages</option>
						</select></p>
					<p><input class="form-control newsrc" placeholder="Component source" name="new[{{ $site->id}}][src]"
							  title="app/components|events or page ID" data-toggle="tooltip" data-placement="bottom"></p>
                                        <p><small><input type="text" name="new[{{ $site->id}}][theme]" class="form-control admin-form text-center newtheme" placeholder="theme|tag" value="{{ $theme }}"></small></p>
					<button type="submit" data-siteid="{{ $site->id }}" data-intheme="{{ $theme }}" class="btn btn-success btn-xs" name="save[new]">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
				<input type="hidden" name="siteid" value="{{ $site->id }}">
				<input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
				<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">
			</div>
			</form>
		</div>
		<div id="cardstock{{ $site->id }}">
				@include($template.'.lists.components-cards', array('components' => $value, 'siteid' => $site->id))
		</div>
	</div>
        @endforeach
	<div class="rowdelimiter"></div>
	@endforeach
        </div>
    </div>
</div>
@stop
