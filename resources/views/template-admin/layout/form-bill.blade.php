<div class="row">
	<div class="col-md-4">
		@if(!isset($OrdersId))
		<div class="form-group">
			<input type="text" class="form-control" name="billCreate[fill][orders_id]" placeholder="Orders ID">
		</div>			
		@else
		<input type="hidden" name="billCreate[fill][orders_id]" value="{{ $OrdersId }}">
		@endif
		<div class="form-group">
			<select class="form-control" name="billCreate[fill][status_id]">
				<option value="{{ !isset(statuses("payment")->first()->id) ? 0 : statuses("payment")->first()->id }}">{{ !isset(statuses("payment")->first()->name) ? '[?]error' : statuses("payment")->first()->name }}</option>
                @foreach(statuses() as $status)
				<option value="{{ $status->id }}">{{ $status->name }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<select class="form-control" name="billCreate[fill][payment_method_id]">
				<option value="0">[manual]</option>
                @foreach(payments() as $payment)
				<option value="{{ $payment->id }}">{{ $payment->name }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<input type="text" class="form-control" name="billCreate[fill][payment_method]" placeholder="Payment Method Name [manual]">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" name="billCreate[fill][link]" placeholder="Link" value="{{ str_random(18) }}">
			<small>link</small>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<input type="text" class="form-control" name="billCreate[fill][price]" placeholder="Price">
		</div>
		<div class="form-group">
			<select class="form-control" name="billCreate[template]">
				@if(null != veer_get('billsTypes'))
                @foreach(veer_get('billsTypes') as $templ)
				<option value="{{ $templ }}">{{ $templ }}</option>
				@endforeach
				@endif
				<option value="">[empty]</option>
			</select>
		</div>
		<div class="checkbox">
			<input type="checkbox" name="billCreate[fill][sendTo]" value="1" checked data-on-text="On" date-off-text="Off" class="page-checkboxes"> &nbsp;Send to user
		</div>
		@if(!isset($skipSubmit))
		<button type="submit" class="btn btn-default" name="addNewBill" value="New">Submit</button> 
		@endif
	</div>
</div>