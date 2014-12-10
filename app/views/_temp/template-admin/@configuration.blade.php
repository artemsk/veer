@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-3"> 
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite[1]" placeholder="Site ID">
              </div> 
              <div class="form-group">
                <label>Configuration Name</label>
                <input type="text" class="form-control" name="InName[1]" placeholder="Name">
              </div>
              <div class="form-group">
                <label>Configuration Value</label>
                <textarea class="form-control" rows="3" name="InValue[1]"></textarea>
              </div>   
              <button type="submit" class="btn btn-default">Submit</button>
        </div>
        <div class="col-md-3"> 
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite[2]" placeholder="Site ID">
              </div> 
              <div class="form-group">
                <label>Configuration Name</label>
                <input type="text" class="form-control" name="InName[2]" placeholder="Name">
              </div>
              <div class="form-group">
                <label>Configuration Value</label>
                <textarea class="form-control" rows="3" name="InValue[2]"></textarea>
              </div>                                            
        </div>
        <div class="col-md-3"> 
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite[3]" placeholder="Site ID">
              </div> 
              <div class="form-group">
                <label>Configuration Name</label>
                <input type="text" class="form-control" name="InName[3]" placeholder="Name">
              </div>
              <div class="form-group">
                <label>Configuration Value</label>
                <textarea class="form-control" rows="3" name="InValue[3]"></textarea>
              </div>                                            
        </div> 
        <div class="col-md-3"> 
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite[4]" placeholder="Site ID">
              </div> 
              <div class="form-group">
                <label>Configuration Name</label>
                <input type="text" class="form-control" name="InName[4]" placeholder="Name">
              </div>
              <div class="form-group">
                <label>Configuration Value</label>
                <textarea class="form-control" rows="3" name="InValue[4]"></textarea>
              </div>            
        </div> 
</div></form>
@stop