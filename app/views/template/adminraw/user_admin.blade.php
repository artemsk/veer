@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Users ID</label>
                <input type="text" class="form-control" name="InUsers" placeholder="Users ID">
              </div>
              <div class="form-group">
                <label>Description</label>
                <input type="text" class="form-control" name="InDescr" placeholder="Description">
              </div>
              <div class="form-group">
                    <label>Access Parameters</label>
                    <textarea class="form-control" name="InAccess" rows="3"></textarea>
              </div>
              <div class="form-group">
                    <label>Sites Watch (ID per row)</label>
                    <textarea class="form-control" name="InSite" rows="2"></textarea>
              </div>            
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnBan"> Banned
                </label>
              </div>              
              <button type="submit" class="btn btn-default">Submit</button>
        </div>  
    </div>  </form>
</div>
@stop