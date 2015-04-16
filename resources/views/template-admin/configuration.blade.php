@extends($template.'.layout.base')

@section('body')
	
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-settings', array('place' => 'configuration'))</div>

            <h3>Configuration cards</h3>

        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block settings-column">
            @foreach($items as $site)

	<h2 id="site{{ $site->id }}">{{ $site->url }} <small>sort by <a href="{{ route('admin.show', array('configuration', "sort" => "conf_key", "direction" => "asc")) }}">keys</a> | <a href="{{ route('admin.show', array('configuration', "sort" => "id", "direction" => "desc")) }}">id</a></small></h2>
        @foreach($site->regrouped as $key => $value)
        <h4 class="@if(count($value)>0) configuraton-component-group @else configuration-component-group-empty @endif"><span class="glyphicon glyphicon-chevron-down danger-icon"></span> {{ data_get($site->components, $key.'.components_src', $key) }} <small>{{ data_get($site->components, $key.'.components_type') }} | {{ count($value) }}</small></h4>

	<div class="row">
		<div class="col-lg-3 col-md-4 col-sm-6 text-center @if(count($value)<=0) collapse out @endif">
			<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="veer-form-submit-configuration">
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="thumbnail newcard thumbnail-configuration-list" id="cardnew{{ $site->id }}" >
				<div class="caption"><small>NEW CARD</small>
					<p><strong><input type="text" name="new[{{ $site->id}}][key]" class="form-control admin-form text-center newkey"
									  placeholder="Key" value=""></strong></p>
									  <p><textarea name="new[{{ $site->id}}][value]" class="form-control newval" placeholder="Value" rows="5"></textarea></p>
                                                                          <p><small><input type="text" name="new[{{ $site->id}}][theme]" class="form-control admin-form text-center newtheme" placeholder="theme" value=""></small></p>
					<button type="submit" data-siteid="{{ $site->id }}" name="save[new]" class="btn btn-success btn-xs">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
				<input type="hidden" name="siteid" value="{{ $site->id }}">
				<input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
				<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">
                                <input type="hidden" name="component_id" value="{{ $key }}">
			</div>
			</form>
		</div>
		<div id="cardstock{{ $site->id }}">
                    @if(!empty(data_get($site->regrouped, $key)))
				@include($template.'.lists.configuration-cards', array('configuration' => data_get($site->regrouped, $key), 'siteid' => $site->id, 'component_id' => $key))
                                @endif
		</div>
	</div>        
        @endforeach
        <div class="rowdelimiter"></div>
	@endforeach
        </div>
    </div>
</div>
@stop
