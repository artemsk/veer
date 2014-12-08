@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                    <label>Upload Files</label>
                    <input type="file" id="InFile4" name="InFile4">
                    <input type="file" id="InFile5" name="InFile5">
                    <input type="file" id="InFile6" name="InFile6">
              </div>
              <div class="form-group">
                    <label>or Choose Existing Files (ID per row)</label>
                    <textarea class="form-control" name="InFileExist2" rows="2"></textarea>
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
</div> </form>
@stop