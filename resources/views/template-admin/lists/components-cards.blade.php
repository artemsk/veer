@foreach($components as $item)
@if(is_object($item))
		<div class="col-lg-3 col-md-4 col-sm-6 text-center">
			<div class="thumbnail thumbnail-configuration-list components-list" id="card{{$item->id}}">
		<form method="POST" action="{{ URL::full() }}#card{{$item->id}}" accept-charset="UTF-8" class="veer-form-submit-configuration"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="caption"><p><small>#{{$item->id}}</small></p>
                    <p><strong><select class="form-control transparent-textarea" placeholder="Route name" name="components[{{$item->id}}][name]">
                            <option selected="true">{{ $item->route_name }}</option>    
                            <option>GLOBAL</option>  
                            @foreach(Route::getRoutes() as $route)
                            <option>{{ $route->getName() }}</option>@endforeach
                            </select></strong></p>                  
					<p><select class="form-control components-type transparent-textarea" placeholder="Component type" name="components[{{$item->id}}][type]">
							<option>{{ $item->components_type }}</option>
							<option>functions</option>
							<option>events</option>
							<option>pages</option>
						</select></p>
                    <p><select class="form-control transparent-textarea components-src components-src-functions @if($item->components_type != 'functions') hidden @endif" placeholder="Component source" name="components[{{$item->id}}][src]" title="app/components|events or page ID" data-toggle="tooltip" data-placement="bottom" @if($item->components_type != 'functions') disabled="true" @endif >
                            <option value="{{ $item->components_src }}">{{ $item->components_src }}</option>
                            <option value="+">+ Add</option>
                            @foreach(data_get($items, 'availModules.components', []) as $module)
                                <option value="{{ $module }}">{{ $module }}</option>
                            @endforeach
                        </select></p>
                    <p><select class="form-control transparent-textarea components-src @if($item->components_type != 'events') hidden @endif components-src-events" placeholder="Component source" name="components[{{$item->id}}][src]" title="app/components|events or page ID" data-toggle="tooltip" data-placement="bottom" @if($item->components_type != 'events') disabled="true" @endif >
                            <option value="{{ $item->components_src }}">{{ $item->components_src }}</option>
                            <option value="+">+ Add</option>
                            @foreach(data_get($items, 'availModules.events', []) as $module)
                                <option value="{{ $module }}">{{ $module }}</option>
                            @endforeach
                        </select></p>    
                    <p><input class="form-control transparent-textarea @if($item->components_type != 'pages') hidden @endif components-src components-src-pages" placeholder="Component source" value="{{ $item->components_src }}" name="components[{{$item->id}}][src]" title="app/components|events or page ID" data-toggle="tooltip" data-placement="bottom" @if($item->components_type != 'pages') disabled="true" @endif ></p>     
                    <p><small><input type="text" name="components[{{ $item->id }}][theme]" class="form-control admin-form text-center" placeholder="—" value="@if(!empty($item->theme)){{ $item->theme }}@endif"></small></p>
					<button type="submit"  data-siteid="{{ $siteid }}" class="btn btn-success btn-xs" name="save[{{$item->id}}]">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button> &nbsp;<button type="button" class="btn btn-info btn-xs copybutton" data-confname="{{ $item->route_name }}" data-conftype="{{ $item->components_type }}" data-confsrc="{{ $item->components_src }}" data-conftheme="@if(!empty($item->theme)){{ $item->theme }}@endif" data-confsiteid="{{ $siteid }}"><span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span></button> &nbsp;<button type="submit"  data-siteid="{{ $siteid }}" class="btn btn-danger btn-xs" name="dele[{{$item->id}}]">
						<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
					<input type="hidden" name="siteid" value="{{ $siteid }}">
                                        <input type="hidden" name="sort" value="{{ Input::get('sort', null) }}">
					<input type="hidden" name="direction" value="{{ Input::get('direction', null) }}">
				</div>
		</form>
			</div>
		</div>
@endif
@endforeach		
		