@extends($template.'.layout.base')

@section('body')
	
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-settings', array('place' => 'configuration'))</div>

            <h3 class="hidden-md">Configuration cards</h3>

        </div>
        <div class="visible-xs-block visible-sm-block sm-rowdelimiter"></div>
        <div class="col-md-10 main-content-block settings-column ajax-form-submit" data-replace-div=".ajax-form-submit">
            @foreach($items as $site)

	<h2 id="site{{ $site->id }}">{{ $site->url }} <small>sort by <a href="{{ route('admin.show', array('configuration', "sort" => "conf_key", "direction" => "asc")) }}">keys</a> | <a href="{{ route('admin.show', array('configuration', "sort" => "id", "direction" => "desc")) }}">id</a></small></h2>
        @foreach(count($site->configuration->groupBy('theme'))>0 ? $site->configuration->groupBy('theme') : [''=>[]] as $theme => $value)
        <h4 class="configuraton-component-group" data-toggle="collapse" data-target='#collapsed{{ $site->id.$theme }}'><span class="glyphicon glyphicon-chevron-down danger-icon"></span> {{ $theme }} <small>{{ count($value) }}</small></h4>

	<div class="row collapse in" id="collapsed{{ $site->id.$theme }}">
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<form method="POST" action="{{ URL::full() }}#collapsed{{ $site->id.$theme }}" accept-charset="UTF-8" class="veer-form-submit-configuration">
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="thumbnail newcard thumbnail-configuration-list" id="cardnew{{ $site->id.$theme }}" >
				<div class="caption"><small>NEW CARD</small>
					<p><strong><input type="text" name="new[{{ $site->id}}][key]" class="form-control admin-form text-center newkey"
									  placeholder="Key" value=""></strong></p>
									  <p><textarea name="new[{{ $site->id}}][value]" class="form-control newval" placeholder="Value" rows="5"></textarea></p>
                                                                          <p><small><input type="text" name="new[{{ $site->id}}][theme]" class="form-control admin-form text-center newtheme" placeholder="theme|tag" value="{{ $theme }}"></small></p>
					<button type="submit" data-siteid="{{ $site->id }}" data-intheme="{{ $theme }}" name="save[new]" class="btn btn-success btn-xs">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
				</div>
				<input type="hidden" name="siteid" value="{{ $site->id }}">
				<input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
				<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">
			</div>
			</form>
		</div>
		<div id="cardstock{{ $site->id }}">
				@include($template.'.lists.configuration-cards', array('configuration' => $value, 'siteid' => $site->id))
		</div>
	</div>        
        @endforeach
        <div class="rowdelimiter"></div>
	@endforeach
        </div>
    </div>
</div>
@stop
