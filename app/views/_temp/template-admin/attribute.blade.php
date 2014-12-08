@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Attribute Name</label>
                <input type="text" class="form-control" name="InName" placeholder="Name">
              </div>
              <div class="form-group">
                <label>Attribute Value</label>
                <input type="text" class="form-control" name="InValue" placeholder="Value">
              </div> 
              <div class="form-group">
              <label>Type</label>    
                <select name="InType" class="form-control">
                            <option>descr</option>
                            <option>choose</option>
                </select>
              </div>  
              <div class="form-group">
                <label for="InDescr">Description</label>
                <textarea class="form-control" rows="3" name="InDescr"></textarea>
              </div> 
     </div> 
     <div class="col-md-6"> 
              <div class="form-group">
                    <label>Connected Pages (ID per row)</label>
                    <textarea class="form-control" name="InConnectedPages" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                    <label>Connected Products (ID per row)</label>
                    <textarea class="form-control" name="InConnectedProducts" rows="2"></textarea>
                    </div> 
              <button type="submit" class="btn btn-default">Submit</button> 
    </div>  
</div></form>
@stop