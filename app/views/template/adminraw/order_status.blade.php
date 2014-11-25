@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    @for ($i = 0; $i < 5; $i++)    
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Status Name</label>
                <input type="text" class="form-control" name="InName[{{ $i }}]" placeholder="Name">
              </div> 
              <div class="form-group">
                <label>Color</label>
                <input type="color" class="form-control" name="InColor[{{ $i }}]" placeholder="Color">
              </div>
              <div class="form-group">
                <label>Manual Order</label>
                <input type="text" class="form-control" name="InOrder[{{ $i }}]" placeholder="Order">
              </div> 
        </div> 
        <div class="col-md-6">         
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnFirst[{{ $i }}]"> First Status
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnUnreg[{{ $i }}]"> Unregistered Order Status
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnError[{{ $i }}]"> Error Status
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnPayment[{{ $i }}]"> Payment Type Status
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnDelivery[{{ $i }}]"> Delivery Type Status
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnClose[{{ $i }}]"> Close Status
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="OnSecret[{{ $i }}]"> Secret Status (hidden from user)
                    </label>
                </div>
             </div> 
</div><div class="rowdelimiter"></div>
    @endfor
    <button type="submit" class="btn btn-default">Submit</button> 
    <div class="rowdelimiter"></div>
    </form>
@stop