@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
            <div class="form-group">
                <label>Sites ID</label>
                <input type="text" class="form-control" name="InSite" placeholder="Sites ID">
              </div>
            <div class="form-group">
                <label>Users ID</label>
                <input type="text" class="form-control" name="InUsers" placeholder="Users ID">
              </div>
             <div class="form-group">
                <label>Session_ID</label>
                <input type="text" class="form-control" name="InSession" placeholder="Session ID">
            </div>          
              <button type="submit" class="btn btn-default">Submit</button>
        </div>  
        <div class="col-md-6">
             <div class="form-group">
                <label>User List</label>
                <input type="text" class="form-control" name="InUserList" placeholder="User List">
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnBasket"> Basket
                </label>
            </div>
            <div class="form-group">
            <label>Products in List (Id Per Row: Prd:Qty)</label>
            <textarea class="form-control" name="InListProducts" rows="1"></textarea>
            </div>
            <div class="form-group">
            <label>Pages in List (Id Per Row)</label>
            <textarea class="form-control" name="InListPages" rows="1"></textarea>
            </div>
        </div>
    </div>  </form>
</div>
@stop