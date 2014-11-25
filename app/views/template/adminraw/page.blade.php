@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
             
              <div class="form-group">
                <label for="InUrl">Url (latin or leave blank)</label>
                <input type="text" class="form-control" id="InUrl" name="InUrl" placeholder="Url">
              </div>
              <div class="form-group">
                <label for="InTitle">Title</label>
                <input type="text" class="form-control" id="InTitle" name="InTitle" placeholder="Title">
              </div>                
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnComms" checked> Allow/Show Comments
                </label>
              </div> 
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnTitles" checked> Show Title
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnDate" checked> Show Date
                </label>
              </div> 
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnBlade"> Original BLADE template
                </label>
              </div> 
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnLists" checked> Show In Lists
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnLasts" checked> Show In Last Pages
                </label>
              </div> 
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnNews"> Show in News
                </label>
              </div>  
              <div class="form-group">
                <label for="InOrder">Manual Order</label>
                <input type="number" class="form-control" id="InOrder" name="InOrder" placeholder="Sort Order">
              </div>  
            <div class="row"><div class="col-md-6">
              <div class="form-group">
              <label>Images</label>
              <input type="file" id="InFile1" name="InFile1">
              <input type="file" id="InFile2" name="InFile2">
              <input type="file" id="InFile3" name="InFile3">
              <p class="help-block">Or ID of existing images per row
              <textarea class="form-control" rows="2" name="InFileExist"></textarea></p>
              </div>
                </div><div class="col-md-6">
              <div class="form-group">
              <label>Files</label>
              <input type="file" id="InFile4" name="InFile4">
              <input type="file" id="InFile5" name="InFile5">
              <input type="file" id="InFile6" name="InFile6">
              <p class="help-block">Or ID of existing files per row
              <textarea class="form-control" rows="2" name="InFileExist2"></textarea></p>
              </div>
                </div></div>
              <button type="submit" class="btn btn-default">Submit</button>
        </div>  
        <div class="col-md-6">
            <div class="form-group">
                <label for="InIntroText">Intro text</label>
                <textarea class="form-control" rows="3" name="InIntroText"></textarea>
              </div>  
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnIntroText" checked> Always show Intro Text
                </label>
              </div>   
              <div class="form-group">
                <label>Main text</label>
                <textarea class="form-control" rows="3" name="InMainText"></textarea>
              </div> 
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    <label>Categories (ID per row)</label>
                    <textarea class="form-control" name="InCategories" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                    <label>Tags (comma)</label>
                    <textarea class="form-control" name="InTags" rows="2"></textarea>
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
                </div>
            </div> 
             
              <div class="form-group">
                <label>Attributes</label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="InAttributesType[1]" class="form-control">
                            <option>descr</option>
                            <option>choose</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="InAttributesType[2]" class="form-control">
                            <option>descr</option>
                            <option>choose</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="InAttributesType[3]" class="form-control">
                            <option>descr</option>
                            <option>choose</option>
                        </select>
                    </div>
                </div><p class="xs-rowdelimiter"></p>
                <div class="row">
                    <div class="col-md-4">                        
                <input type="text" name="InAttributesName[1]" class="form-control" placeholder="Name">
                    </div>
                    <div class="col-md-4">                        
                <input type="text" name="InAttributesName[2]" class="form-control" placeholder="Name">
                    </div>
                    <div class="col-md-4">
                <input type="text" name="InAttributesName[3]" class="form-control" placeholder="Name">
                    </div>
                </div><p class="xs-rowdelimiter"></p>
                <div class="row">
                    <div class="col-md-4">                        
               <input type="text" name="InAttributesVal[1]" class="form-control" placeholder="Value">
                    </div>
                    <div class="col-md-4">                        
                <input type="text" name="InAttributesVal[2]" class="form-control" placeholder="Value">
                    </div>
                    <div class="col-md-4">
                <input type="text" name="InAttributesVal[3]" class="form-control" placeholder="Value">
                    </div>
                </div><p class="xs-rowdelimiter"></p>
                <div class="row">
                    <div class="col-md-4">                        
                <input type="text" name="InAttributesDescr[1]" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-md-4">                        
                <input type="text" name="InAttributesDescr[2]" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-md-4">
                <input type="text" name="InAttributesDescr[3]" class="form-control" placeholder="Description">
                    </div>
                </div>
              </div>  
        </div>
    </div>  </form>
</div>
@stop