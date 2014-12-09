@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">  
              <div class="form-group">
                <label>Url</label>
                <input type="url" class="form-control" name="InUrl" placeholder="Url">
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnSite" checked> ON / OFF
                </label>
              </div><p class="rowdelimiter"></p>
              <div class="form-group">
                <label>Parent Site ID</label>
                <input type="text" class="form-control" name="InParentSite" placeholder="Site ID">
              </div> 
              <div class="form-group">
                <label>Manual Sort</label>
                <input type="text" class="form-control" name="InOrder" placeholder="Sort">
              </div>
              <div class="form-group">
                <label>Redirect Url</label>
                <input type="url" class="form-control" name="InRedirectUrl" placeholder="Url">
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnRedirect"> Redirect On/Off
                </label>
              </div>  
              <button type="submit" class="btn btn-default">Submit</button> 
        </div> 
</div> </form>
@stop