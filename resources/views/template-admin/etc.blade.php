@extends($template.'.layout.base')

@section('body')
	
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-settings', array('place' => 'etc'))</div>

            <h3 class="hidden-md">Etc.<br/><small>cache | migrations | password reminders</small></h3>

        </div>
        <div class="visible-xs-block visible-sm-block sm-rowdelimiter"></div>
        <div class="col-md-10 main-content-block settings-column">
            
            <div class="ajax-form-submit ajax-form-submit-1" data-replace-div=".ajax-form-submit-1">
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">	
            <label>Raw Sql</label>
            <p><textarea class="form-control" name="freeFormSql" placeholder="Raw Sql [Update, Insert, Delete]"></textarea></p>
            <button type="submit" class="btn btn-default" name="actionButton" value="runRawSql">Run</button>
            </form>
            </div>
            <div class="ajax-form-submit ajax-form-submit-2" data-replace-div=".ajax-form-submit-2">
            @if(array_get($items, 'trashed') != null)
            <div class="rowdelimiter"></div>

            <div class="form-group">
            <label>Clear Trashed Elements</label>
                <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div id="clearTrashed">
                <input type="hidden" name="actionButton" value="clearTrashed">
                @foreach(array_get($items, 'trashed') as $table => $trash)
                <button type="submit" class="btn btn-default margin-bottom-button" name="tableName" value="{{ $table }}" data-resultdiv="#clearTrashed">Clear <strong>{{ $table }} {{ $trash }}</strong></button>&nbsp;
                @endforeach
                </div>
                </form>
            </div>
            @endif
            </div>
            <div class="rowdelimiter"></div>

            <div class="form-group ajax-form-submit ajax-form-submit-3" data-replace-div=".ajax-form-submit-3">
                <label>Clear Cache</label>
                <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div id="clearCache">
                <input type="hidden" name="actionButton" value="clearCache">
                <button type="submit" class="btn btn-default margin-bottom-button" name="action" value="clearCache" data-resultdiv="#clearCache">Clear cache <strong>{{ array_get($items, 'cache') != null ? count($items['cache']) : null }}</strong></button>&nbsp;
                </div>
                </form>
            </div>

            <div class="rowdelimiter"></div>

            <div class="form-group ajax-form-submit" data-replace-div="#compareVersions" data-skip-reload="true">
                <label>Check Latest Version</label>
                <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div id="compareVersions">
                <input type="hidden" name="actionButton" value="checkLatestVersion">
                <p><button type="submit" class="btn btn-default" name="action" value="checkLatestVersion">Check Version</button></p>
                </div>
                </form>
            </div>
	
            <div class="rowdelimiter"></div>

            <div class="form-group ajax-form-submit" data-replace-div="#sendPingEmail" data-skip-reload="true">
                <label>Send Ping</label>
                <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div id="sendPingEmail">
                <input type="hidden" name="actionButton" value="sendPingEmail">
                <p><button type="submit" class="btn btn-default" name="action" value="sendPingEmail">Send Ping Email</button></p>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop