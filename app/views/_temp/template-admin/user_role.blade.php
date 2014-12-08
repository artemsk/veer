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
                <label>Role (user, distributor, wholesaler, author etc.)</label>
                <input type="text" class="form-control" name="InRole" placeholder="Role Name">
              </div>
             <div class="form-group">
                <label>Price Field</label>
                <select name="InField" class="form-control">
                            <option>price</option>
                            <option>price_sales</option>
                            <option>price_opt</option>
                            <option>price_base</option>
                </select>
            </div>  
            <div class="form-group">
                <label>Discount</label>
                <input type="text" class="form-control" name="InDiscount" placeholder="Discount">
            </div>
              <button type="submit" class="btn btn-default">Submit</button>
        </div>  
        <div class="col-md-6">
            <div class="form-group">
            <label>Assign this role to users (ID per row)</label>
            <textarea class="form-control" name="InAssignUsers" rows="7"></textarea>
            </div>
        </div>
    </div>  </form>
</div>
@stop