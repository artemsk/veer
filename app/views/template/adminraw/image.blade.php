@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                    <label>Upload Images</label>
                    <input type="file" id="InFile1" name="InFile1">
                    <input type="file" id="InFile2" name="InFile2">
                    <input type="file" id="InFile3" name="InFile3">
              </div>
              <div class="form-group">
                    <label>or Choose Existing Images (ID per row)</label>
                    <textarea class="form-control" name="InConnectedImages" rows="2"></textarea>
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
                    <div class="form-group">
                    <label>Connected Categories (ID per row)</label>
                    <textarea class="form-control" name="InConnectedCategories" rows="2"></textarea>
                    </div> 
              <button type="submit" class="btn btn-default">Submit</button> 
        </div> 
</div> </form>
@stop