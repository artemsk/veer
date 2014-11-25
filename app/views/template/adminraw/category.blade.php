@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Category Title</label>
                <input type="text" class="form-control" name="InName" placeholder="Title">
              </div>
              <div class="form-group">
                <label>Category Remote Url (IF exist)</label>
                <input type="text" class="form-control" name="InUrl" placeholder="Remote Url">
              </div> 
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite" placeholder="Site ID">
              </div>             
              <div class="form-group">
                <label for="InDescr">Description</label>
                <textarea class="form-control" rows="3" name="InDescr"></textarea>
              </div> 
              <div class="form-group">
                <label>Manual Sort</label>
                <input type="text" class="form-control" name="InOrder" placeholder="Sort">
              </div>   
     </div> 
     <div class="col-md-6"> 
         <div class="row">
             <div class="col-md-6">
                    <div class="form-group">
                    <label>Parent Categories (ID per row)</label>
                    <textarea class="form-control" name="InParentCategories" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                    <label>Sub Categories (ID per row)</label>
                    <textarea class="form-control" name="InSubCategories" rows="2"></textarea>
                    </div>    
                    <div class="form-group">
                    <label>Connected Images or Existing</label>
                    <input type="file" id="InFile1" name="InFile1">
                    <input type="file" id="InFile2" name="InFile2">
                    <input type="file" id="InFile3" name="InFile3">
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
                    <label></label>
                    <textarea class="form-control" name="InConnectedImages" rows="2"></textarea>
                    </div> 
             </div>
         </div>                          
              <button type="submit" class="btn btn-default">Submit</button> 
    </div> 
</div> </form>
@stop