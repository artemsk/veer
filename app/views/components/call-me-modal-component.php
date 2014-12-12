<div class="tags-lnks hidden-xs">
	<button type="button" class="btn btn-default dropdown-toggle-menu" data-container="body" 
			data-toggle="popover" data-placement="bottom" 
			data-content='<?php echo Form::open(array('route' => 'search.store')); ?><div class="input-group">
			<input type="text" class="form-control" name="q">
			<span class="input-group-btn">
			<button class="btn btn-default" type="submit">Go!</button>
			</span>
			</div><?php echo Form::close(); ?>' data-html="true" data-title="Перезвоните мне">
		Заказать звонок
	</button>
</div>