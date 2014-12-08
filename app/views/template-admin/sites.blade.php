@extends($template.'.layout.base')

@section('body')
<h1>Sites</h1>
<div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ON/OFF</th>				
          <th>#</th>
          <th>Url</th>
          <th>Parent</th>
          <th>Sort</th>
          <th colspan="2">Redirect On/Off
          & Url</th>
          <th>Updated At</th>
		  <th>Created At</th>		  
        </tr>
      </thead>
      <tbody>
	@foreach ($items as $item)	  
        <tr>
          <td>@if ((bool)$item['on_off'] == true)
			  <button type="button" class="btn btn-success">Live</button>
			  @else
			  <button type="button" class="btn btn-danger">OnHold</button>
			  @endif
			</td>
          <td>{{ $item['id'] }}</td>
          <td><input type="url" class="form-control" placeholder="Site Url" value="{{ $item['url'] }}"></td>
          <td><input type="text" class="form-control" placeholder="Parent Id" size="3" value="{{ $item['parent_id'] }}"></td>
          <td><input type="text" class="form-control" placeholder="Sort" size="3" value="{{ $item['manual_sort'] }}"></td>
          <td>@if ((bool)$item['redirect_on'] == true)
			  <input type="checkbox" checked>
			  @else
			  <input type="checkbox">
			  @endif</td>
          <td><input type="url" class="form-control" placeholder="Redirect Url" value="{{ $item['redirect_url'] }}"></td>	
		  <td>{{ $item['updated_at'] }}</td>	
		  <td>{{ $item['created_at'] }}</td>
		</tr>
		<tr class="active">
			<td colspan="9"><small>{{ $item->categories->count() }} categories, {{ $item->components->count() }} components, 
				{{ $item->configuration->count() }} confs, {{ $item->users->count() }} users, {{ $item->discounts->count() }} discounts, 
				{{ $item->userlists->count() }} lists, {{ $item->orders->count() }} orders, {{ $item->delivery->count() }} delivery, 
				{{ $item->payment->count() }} payment, {{ $item->communications->count() }} messages, {{ $item->roles->count() }} roles, 
				{{ $item->elements()->count() }} products & pages, {{ $item->subsites->count() }} sub-sites</small></td>
        </tr>
	@endforeach
		<tr>
          <td>New</td>
          <td><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></td>
          <td><input type="url" class="form-control" placeholder="Site Url" value=""></td>
          <td><input type="text" class="form-control" placeholder="Parent Id" size="3"></td>
          <td><input type="text" class="form-control" placeholder="Sort" size="3"></td>
          <td><input type="checkbox"></td>
          <td><input type="url" class="form-control" placeholder="Redirect Url" value=""></td>	
		  <td></td>	
		  <td></td>
        </tr>
      </tbody>
    </table>
	<button type="button" class="btn btn-default">Update</button>
</div>
@stop