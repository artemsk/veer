<div class="tags-lnks hidden-xs">
	<button type="button" class="btn btn-default dropdown-toggle-menu" data-container="body" 
			data-toggle="popover" data-placement="bottom" 
			data-content='<form method="POST" action="{{ route('search.store') }}" accept-charset="UTF-8">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="input-group">
			<input type="text" class="form-control" name="q">
			<span class="input-group-btn">
			<button class="btn btn-default" type="submit">Go!</button>
			</span>
			</div></form>' data-html="true" data-title="Перезвоните мне">
		Заказать звонок
	</button>
</div>