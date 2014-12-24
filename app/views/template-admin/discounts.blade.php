@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li class="active">Discounts</li>
		<li><a href="{{ route("admin.show", "shipping") }}">Shipping methods</a></li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
</ol>
<h1>Discounts</h1>
<br/>
<div class="container">
	@foreach($items as $item)
	
		{{ $item->id }}<br/>
			
	@endforeach
	
	<div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Users ID</label>
                <input type="text" class="form-control" name="InUsers" placeholder="Users ID">
              </div>
            <div class="form-group">
                <label>Sites ID</label>
                <input type="text" class="form-control" name="InSite" placeholder="Sites ID">
              </div>
             <div class="form-group">
                <label>Secret Code</label>
                <input type="text" class="form-control" name="InSecret" placeholder="Secret Code">
            </div>
            <div class="form-group">
                <label>Status (active, wait, expired, canceled) </label>
                <input type="text" class="form-control" name="InStatus" placeholder="Status">
            </div>             
              <button type="submit" class="btn btn-default">Submit</button>
        </div>  
        <div class="col-md-6">
             <div class="form-group">
                <label>Discount</label>
                <input type="text" class="form-control" name="InDiscount" placeholder="Percent">
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnDiscountExp"> Expires?
                </label>
            </div>
            <div class="form-group">
                <label>Discount Expiration Day</label>
                <input type="date" class="form-control" name="InDiscountExpDay" placeholder="YYYY-MM-DD">
            </div> 
             <div class="form-group">
                <label>Discount Expiration Times</label>
                <input type="date" class="form-control" name="InDiscountExpTimes" placeholder="Times">
            </div> 
        </div>
    </div>
</div>
@stop