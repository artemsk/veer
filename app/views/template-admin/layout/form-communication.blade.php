<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<input type="text" class="form-control" name="communication[fill][sender]" placeholder="Sender">
		</div>
		<div class="form-group">
			<input type="tel" class="form-control" name="communication[fill][sender_phone]" placeholder="Sender Phone">
		</div>
		<div class="form-group">
			<input type="email" class="form-control" name="communication[fill][sender_email]" placeholder="Sender Email">
		</div>
		<div class="form-group">
			<textarea class="form-control" rows="3" name="communication[message]" placeholder="Message @recipient @recipient">
@if(isset($send2UserId) && empty($send2Username))
{{ '@:'.$send2UserId }} @elseif(isset($send2Username) && !empty($send2Username))
{{ '@'.$send2Username }} @endif</textarea>
		</div> 
		<div class="form-group">
			<input type="text" class="form-control" name="communication[fill][theme]" placeholder="Theme">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" name="communication[fill][type]" placeholder="Label | Type (IM, email, callme etc.)">
		</div> 
		<div class="checkbox">
			<label>
				<input type="checkbox" name="communication[checkboxes][public]" checked> Public
			</label>
		</div>			
		<div class="checkbox">
			<label>
				<input type="checkbox" name="communication[checkboxes][email_notify]"
					   @if(isset($emailOn)) checked @endif> Email Notify
			</label>
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="communication[checkboxes][intranet]"> Intranet
			</label>
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="communication[checkboxes][hidden]"> Hidden
			</label>
		</div>			

	</div> 
	<div class="col-md-6">
		<div class="form-group">
			<input type="text" class="form-control" name="communication[fill][sites_id]" placeholder="Sites ID">
		</div> 
		<div class="form-group">
			<input type="text" class="form-control" name="communication[fill][users_id]" placeholder="Users ID [or empty for current]"
				   @if(Input::get('filter',null) == "user" && Input::has('filter_id')) value="{{ Input::get("filter_id") }}" @endif>
		</div> 			
		<div class="form-group">
			<label>Place on specific Url</label>
			<input type="url" class="form-control" name="communication[fill][url]" placeholder="Url">
		</div> 
		<div class="form-group">
			<label>Place on Product | Page | Category | Order</label>
			<textarea class="form-control" name="communication[connected]" rows="3" placeholder="page|product|category|order:id">
@if(isset($placeOn))
{{ $placeOn }}
@endif</textarea>
		</div>   
		<button type="submit" name="action" value="addMessage" class="btn btn-default">Submit</button> 
	</div> 
</div>