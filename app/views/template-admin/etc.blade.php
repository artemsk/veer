@extends($template.'.layout.base')

@section('body')
	
	@include($template.'.layout.breadcrumb-settings', array('place' => 'etc'))
	
<h1>Etc. <small>cache | migrations | password reminders</small></h1>
<br/>
<div class="container">

	{{ Form::open(array('method' => 'put', 'files' => false)); }}	
	<label>Raw Sql</label>
	<p><textarea class="form-control" name="freeFormSql" placeholder="Raw Sql [Update, Insert, Delete]"></textarea></p>
	<button type="submit" class="btn btn-default" name="action" value="runRawSql">Run</button>
	</form>
	
	@if(array_get($items, 'trashed') != null)
	<div class="rowdelimiter"></div>
	
	<div class="form-group">
	<label>Clear Trashed Elements</label>
	{{  Form::open(array('method' => 'put', 'files' => false));  }}
	<div id="clearTrashed">
	<input type="hidden" name="actionButton" value="clearTrashed">
	@foreach(array_get($items, 'trashed') as $table => $trash)
	<button type="submit" class="btn btn-default margin-bottom-button" name="button" value="{{ $table }}" data-resultdiv="#clearTrashed">Clear <strong>{{ $table }} {{ $trash }}</strong></button>&nbsp;
	@endforeach
	</div>
	</form>
	@endif

	<div class="rowdelimiter"></div>
	
	<div class="form-group">
	<label>Clear Cache</label>
	{{  Form::open(array('method' => 'put', 'files' => false));  }}
	<div id="clearCache">
	<input type="hidden" name="actionButton" value="clearCache">
	<button type="submit" class="btn btn-default margin-bottom-button" name="action" value="clearCache" data-resultdiv="#clearCache">Clear cache <strong>{{ array_get($items, 'cache') != null ? count($items['cache']) : null }}</strong></button>&nbsp;
	</div>
	</form>

	<div class="rowdelimiter"></div>
	
	<div class="form-group">
	<label>Check Latest Version</label>
	{{  Form::open(array('method' => 'put', 'class' => 'ajaxFormSubmit', 'files' => false));  }}
	<div id="compareVersions">
	<input type="hidden" name="actionButton" value="checkLatestVersion">
	<p><button type="submit" class="btn btn-default" name="action" value="checkLatestVersion" data-resultdiv="#compareVersions">Check Version</button></p>
	</div>
	</form>
	</div>
	
	<div class="rowdelimiter"></div>
	
	<div class="form-group">
	<label>Send Ping</label>
	{{  Form::open(array('method' => 'put', 'class' => 'ajaxFormSubmit', 'files' => false));  }}
	<div id="sendPingEmail">
	<input type="hidden" name="actionButton" value="sendPingEmail">
	<p><button type="submit" class="btn btn-default" name="action" value="sendPingEmail" data-resultdiv="#sendPingEmail">Send Ping Email</button></p>
	</div>
	</form>
	</div>
</div>
@stop