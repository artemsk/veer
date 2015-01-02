	<div class="row">
        <div class="col-md-6">             
			<div class="form-group">
                <input type="text" class="form-control" name="fill[users_id]" placeholder="Users ID" value="{{ $item->users_id or null }}">
				<small>Users Id</small>
			</div>
			<div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="fill[name]" placeholder="Business Name" value="{{ $item->name or null }}"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="fill[country]" placeholder="Country" value="{{ $item->country or null }}"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="fill[region]" placeholder="Region" value="{{ $item->region or null }}"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="fill[city]" placeholder="City" value="{{ $item->city or null }}"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="fill[postcode]" placeholder="Postcode" value="{{ $item->postcode or null }}"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="fill[address]" placeholder="Street Address" value="{{ $item->address or null }}"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="fill[nearby_station]" placeholder="Nearby Station" value="{{ $item->nearby_station or null }}"><div class="xs-rowdelimiter"></div>
            </div>
            <div class="checkbox">
                <label>
					<input type="checkbox" name="checkboxes[office_address]" @if(isset($item->office_address) && $item->office_address == true) checked @endif> Office Address
                </label>
            </div>
            <div class="checkbox">
                <label>
					<input type="checkbox" name="checkboxes[primary]" @if(isset($item->primary) && $item->primary == true) checked @endif> Primary | Default Entry
                </label>
            </div>
        </div>  
        <div class="col-md-6"> 
			<div class="form-group">
				<label>Bank Account</label>
				<input type="text" class="form-control" name="fill[b_inn]" placeholder="Inn" value="{{ $item->b_inn or null }}"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="fill[b_account]" placeholder="Account Number" value="{{ $item->b_account or null }}"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="fill[b_bank]" placeholder="Bank" value="{{ $item->b_bank or null }}"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="fill[b_corr]" placeholder="Corr Account" value="{{ $item->b_corr or null }}"><div class="xs-rowdelimiter"></div>
				<input type="text" class="form-control" name="fill[b_bik]" placeholder="BIK" value="{{ $item->b_bik or null }}"><div class="xs-rowdelimiter"></div>
				<textarea class="form-control" name="fill[b_others]" rows="2" placeholder="Foreign Banks">{{ $item->b_others or null }}</textarea>
			</div>
			@if(isset($item->id))
			<input type="hidden" name="bookId" value="{{ $item->id or null }}">
			@endif
			@if(!isset($skipSubmit))
			<button type="submit" name="action" value="addUserbook" class="btn btn-default">Submit</button>
			@endif
        </div>
    </div>