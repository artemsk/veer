@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="breadcumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'sites'))</div>

            <h1>Sites</h1>

        </div>
        <div class="col-sm-10 main-content-block">


<form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
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
          <td><input type="url" class="form-control" name="site[{{ $item->id }}][url]" placeholder="Site Url" value="{{ $item['url'] }}"></td>
          <td><input type="text" class="form-control" name="site[{{ $item->id }}][parent_id]" placeholder="Parent Id" 
					 size="3" value="{{ $item['parent_id'] }}"></td>
          <td><input type="text" class="form-control" name="site[{{ $item->id }}][manual_sort]" placeholder="Sort" size="3" value="{{ $item['manual_sort'] }}"></td>
          <td> <input type="checkbox" name="site[{{ $item->id }}][redirect_on]" @if((bool)$item['redirect_on'] == true) checked @endif>
			  </td>
          <td><input type="url" class="form-control" name="site[{{ $item->id }}][redirect_url]" 
					 placeholder="Redirect Url" value="{{ $item['redirect_url'] }}"></td>	
		  <td>{{ $item['updated_at'] }}</td>	
		  <td>{{ $item['created_at'] }}</td>
		</tr>
		<tr class="active">
			<td colspan="9"><small>
				<a href="{{ route("admin.show", array("categories", "#site".$item->id)) }}">{{ $item->categories()->count() }} categories</a>, 
				<a href="{{ route("admin.show", array("components", "site" => $item->id)) }}">{{ $item->components()->count() }} components</a>, 
				<a href="{{ route("admin.show", array("configuration", "site" => $item->id)) }}">{{ $item->configuration()->count() }} confs</a>, 
				<a href="{{ route("admin.show", array("users", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->users()->count() }} users</a>, 
				<a href="{{ route("admin.show", array("discounts", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->discounts()->count() }} discounts</a>, 
				<a href="{{ route("admin.show", array("lists", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->userlists()->count() }} lists</a>, 
				<a href="{{ route("admin.show", array("orders", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->orders()->count() }} orders</a>, 
				<a href="{{ route("admin.show", array("shipping", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->delivery()->count() }} shipping</a>, 
				<a href="{{ route("admin.show", array("payment", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->payment()->count() }} payment</a>, 
				<a href="{{ route("admin.show", array("communications", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->communications()->count() }} messages</a>, 
				<a href="{{ route("admin.show", array("roles", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->roles()->count() }} roles</a>, 
				{{ $item->elements()->count() }} <a href="{{ route("admin.show", array("products", "filter" => "site", "filter_id" => $item->id)) }}">products</a> & <a href="{{ route("admin.show", array("pages", "filter" => "site", "filter_id" => $item->id)) }}">pages</a>, {{ $item->subsites()->count() }} sub-sites</small></td>
        </tr>
	@endforeach
		<tr>
          <td>New</td>
          <td><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></td>
          <td><input type="url" class="form-control" name="site[{{ ($item->id)+1 }}][url]" placeholder="Site Url" value=""></td>
          <td><input type="text" class="form-control" name="site[{{ ($item->id)+1 }}][parent_id]" placeholder="Parent Id" size="3"></td>
          <td><input type="text" class="form-control" name="site[{{ ($item->id)+1 }}][manual_sort]" placeholder="Sort" size="3"></td>
          <td><input type="checkbox" name="site[{{ ($item->id)+1 }}][redirect_on]"></td>
          <td><input type="url" class="form-control" name="site[{{ ($item->id)+1 }}][redirect_url]" placeholder="Redirect Url" value=""></td>	
		  <td></td>	
		  <td></td>
        </tr>
      </tbody>
    </table>
	<button type="submit" name="update" class="btn btn-default">Update</button>
	<input type="hidden" name="_action" value="update">
</div>
</form>

        </div>
    </div>
</div>

@stop