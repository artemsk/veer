<ul class="list-group">
	@foreach($items as $item)
	
	<li class="list-group-item bordered-row">
		<small>#{{ $item->id }}</small>
		{{ $item->name }} <strong>{{ $item->country }},
		{{ $item->region }},
		{{ $item->city }}</strong>
		{{ $item->postcode }}
		{{ $item->address }}
		{{ empty($item->nearby_station) ? '' : '('.$item->nearby_station.')' }}
		@if($item->office_address == true) 
		<span class="label label-info">Office</span>
		@endif
		@if($item->primary == true) 
		<span class="label label-primary">Default</span>
		@endif		
		<span class="badge">{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</span>
		@if($item->updated_at != $item->created_at)
		<span class="badge">{{ \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d') }}</span>
		@endif
		@if($item->orders->count() > 0) 
		<span class="badge"><a href="{{ route("admin.show", array("orders", "filter" => "userbook", "filter_id" => $item->id)) }}">{{ $item->orders->count() }} orders</a></span>
		@endif
		@if(!isset($skipUser) && !isset($skipOrder))
		<a href="{{ route("admin.show", array("users", "id" => $item->users_id)) }}">{{ '@' }}{{ $item->user->username or '?Unknown' }}</a>
		@endif
		
		@if(!empty($item->b_account))
		<br/>		
		<small class="bank-data">
			INN {{ $item->b_inn }} 
			ACCOUNT {{ $item->b_account }}
			BANK {{ $item->b_bank }}
			CORR {{ $item->b_corr }}
			BIK {{ $item->b_bik }}
			OTHER {{ $item->b_others }}
		</small>
		@endif
		@if(!isset($skipOrder))
		<p></p>
		@if(!isset($skipUser)) 	
		<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="PUT">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">@endif
		<button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#bookModal{{ $item->id }}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>&nbsp;		
		<button type="submit" name="deleteUserbook[{{ $item->id }}]" value="{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
		@if(!isset($skipUser)) </form> @endif
		@endif
@if(!isset($skipOrder))
	<div class="modal fade" id="bookModal{{ $item->id }}">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Edit user's book</h4>
				</div>
				@if(!isset($skipUser)) 	
				<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
				<input name="_method" type="hidden" value="PUT">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">@endif
				<div class="modal-body">
					@include($template.'.layout.form-userbook', array('item' =>$item, 'skipSubmit' => true))
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" name="action" value="updateUserbook" class="btn btn-primary">Save changes</button>
				</div>
				@if(!isset($skipUser)) </form> @endif
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	@endif		
	</li>	
	
	@endforeach		
</ul>	