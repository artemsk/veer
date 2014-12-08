@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Route name</label>
                <input type="text" class="form-control" name="InName" placeholder="Name">
              </div>
              <div class="form-group">
              <label>Component Type</label>    
                <select name="InType" class="form-control">
                            <option>functions</option>
                            <option>pages</option>
                </select>
              </div> 
              <div class="form-group">
                <label>Component Source</label>
                <textarea class="form-control" rows="3" name="InSrc"></textarea>
              </div> 
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite" placeholder="Site ID">
              </div>                                           
              <button type="submit" class="btn btn-default">Submit</button> 
    </div>  
</div></form>
@stop