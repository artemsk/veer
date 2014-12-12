@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>Structure</strong></li>
		<li class="active">Sites</li>
		<li><a href="{{ route("admin.show", "categories") }}">Categories</a></li>
		<li><a href="{{ route("admin.show", "pages") }}">Pages</a></li>
		<li><a href="{{ route("admin.show", "products") }}">Products</a></li>
</ol>
<h1>Sites</h1>
{{ Form::open(array('method' => 'put', 'files' => false)); }}
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
			  <button type="submit" name="turnoff" class="btn btn-success" value="{{$item->id}}">Live</button>
			  @else
			  <button type="submit" name="turnon" class="btn btn-danger" value="{{$item->id}}">OnHold</button>
			  @endif
			</td>
          <td>{{ $item['id'] }}</td>
          <td><input type="url" class="form-control" name="url[{{ $item->id }}]" placeholder="Site Url" value="{{ $item['url'] }}"></td>
          <td><input type="text" class="form-control" name="parent[{{ $item->id }}]" placeholder="Parent Id" 
					 size="3" value="{{ $item['parent_id'] }}"></td>
          <td><input type="text" class="form-control" name="sort[{{ $item->id }}]" placeholder="Sort" size="3" value="{{ $item['manual_sort'] }}"></td>
          <td>@if ((bool)$item['redirect_on'] == true)
			  <input type="checkbox" name="redirect_on[{{ $item->id }}]" checked>
			  @else
			  <input type="checkbox">
			  @endif</td>
          <td><input type="url" class="form-control" name="redirect_url[{{ $item->id }}]" 
					 placeholder="Redirect Url" value="{{ $item['redirect_url'] }}"></td>	
		  <td>{{ $item['updated_at'] }}</td>	
		  <td>{{ $item['created_at'] }}</td>
		</tr>
		<tr class="active">
			<td colspan="9"><small>
					<a href="{{ route("admin.show", array("categories", "#site".$item->id)) }}">{{ $item->categories->count() }} categories</a>, 
					<a href="{{ route("admin.show", array("components", "site" => $item->id)) }}">{{ $item->components->count() }} components</a>, 
					<a href="{{ route("admin.show", array("configuration", "site" => $item->id)) }}">{{ $item->configuration->count() }} confs</a>, 
					{{ $item->users->count() }} users, {{ $item->discounts->count() }} discounts, 
				{{ $item->userlists->count() }} lists, {{ $item->orders->count() }} orders, {{ $item->delivery->count() }} delivery, 
				{{ $item->payment->count() }} payment, {{ $item->communications->count() }} messages, {{ $item->roles->count() }} roles, 
				{{ $item->elements()->count() }} products & pages, {{ $item->subsites->count() }} sub-sites</small></td>
        </tr>
	@endforeach
		<tr>
          <td>New</td>
          <td><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></td>
          <td><input type="url" class="form-control" name="url[new]" placeholder="Site Url" value=""></td>
          <td><input type="text" class="form-control" name="parent[new]" placeholder="Parent Id" size="3"></td>
          <td><input type="text" class="form-control" name="sort[new]" placeholder="Sort" size="3"></td>
          <td><input type="checkbox" name="redirect_on[new]"></td>
          <td><input type="url" class="form-control" name="redirect_url[new]" placeholder="Redirect Url" value=""></td>	
		  <td></td>	
		  <td></td>
        </tr>
      </tbody>
    </table>
	<button type="submit" name="update" class="btn btn-default">Update</button>
</div>
{{ Form::close() }}
@stop