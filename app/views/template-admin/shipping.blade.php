@extends($template.'.layout.base')

@section('body')
<ol class="breadcrumb">
		<li><strong>E-commerce</strong></li>
		<li><a href="{{ route("admin.show", "orders") }}">Orders</a></li>
		<li><a href="{{ route("admin.show", "bills") }}">Bills</a></li>
		<li><a href="{{ route("admin.show", "discounts") }}">Discounts</a></li>
		<li class="shipping">Shipping methods</li>		
		<li><a href="{{ route("admin.show", "payment") }}">Payment methods</a></li>	
		<li><a href="{{ route("admin.show", "statuses") }}">Statuses</a></li>
</ol>
<h1>Shipping methods</h1>
<br/>
<div class="container">
	@foreach($items as $item)
	
		{{ $item->id }}<br/>
			
	@endforeach
	
	
	<div class="row">
        @for ($i = 0; $i < 3; $i++) 
        <div class="col-md-4">
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite[{{ $i }}]" placeholder="Site ID">
              </div>
              <div class="form-group">
                <label>Shipping Module Name</label>
                <input type="text" class="form-control" name="InName[{{ $i }}]" placeholder="Name">
              </div>
              <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnEnable[{{ $i }}]" checked> ON / OFF
                    </label>
              </div>
              <div class="form-group">
                <label>Delivery Type (delivery, pickup, no-delivery)</label>
                <input type="text" class="form-control" name="InDelivery[{{ $i }}]" placeholder="Type">
              </div>
              <div class="form-group">
                <label>Payment Type (fix, calculator, free)</label>
                <input type="text" class="form-control" name="InPayment[{{ $i }}]" placeholder="Type">
              </div>
              <div class="form-group">
                <label>Price (if fix / or if failed calculation)</label>
                <input type="text" class="form-control" name="InPrice[{{ $i }}]" placeholder="Price">
              </div>
              <div class="form-group">
                <label>Discount (in %)</label>
                <input type="text" class="form-control" name="InDiscount[{{ $i }}]" placeholder="Percent">
              </div>
              <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnDiscountEnable[{{ $i }}]"> Discount Enable
                    </label>
              </div>
              <div class="form-group">
                    <label>Discount Conditions</label>
                    <textarea class="form-control" name="InDiscountConditions[{{ $i }}]" rows="1"></textarea>
              </div>
              <div class="form-group">
                    <label>Address (if it's pickup and known addresses)</label>
                    <textarea class="form-control" name="InAddress[{{ $i }}]" rows="1"></textarea>
              </div>
              <div class="form-group">
                    <label>Other Options</label>
                    <textarea class="form-control" name="InOther[{{ $i }}]" rows="1"></textarea>
              </div>
              <div class="form-group">
                    <label>FUNCTION NAME</label>
                    <input type="text" class="form-control" name="InFunc[{{ $i }}]" placeholder="Function">
              </div>
              <div class="form-group">
                <label>Manual Order</label>
                <input type="text" class="form-control" name="InOrder[{{ $i }}]" placeholder="Manual Order">
              </div>
        </div> 
        @endfor
</div><button type="submit" class="btn btn-default">Submit</button> 
	
</div>
@stop