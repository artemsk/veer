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
              <div class="form-group">
                <label>Group IDs (IF it's group product)</label>
                <textarea class="form-control" rows="1" name="InGroupIds"></textarea>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnGroup"> Group Product
                </label>
              </div>
              <div class="form-group">
                <label>Status (on, hide, out-of-stock)</label>
                <input type="text" class="form-control" name="InStatus" placeholder="Status">
              </div> 
              <div class="form-group">
                <label>Quantity</label>
                <input type="text" class="form-control" name="InQty" placeholder="Quantity">
              </div>  
              <div class="form-group">
                <label>Weight</label>
                <input type="text" class="form-control" name="InWeight" placeholder="Weight">
              </div> 
              <div class="form-group">
                <label>Production Code</label>
                <input type="text" class="form-control" name="InProdCode" placeholder="Code">
              </div>
              <div class="form-group">
                <label>Score</label>
                <input type="text" class="form-control" name="InScore" placeholder="Score">
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnStar"> Star
                </label>
              </div> 
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnDownload"> Download Type
                </label>
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
            <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <label>PRICE</label>
                <input type="text" class="form-control" name="InPrice" placeholder="Price">
                </div>
                <div class="form-group">
                <label>Price base</label>
                <input type="text" class="form-control" name="InPriceBase" placeholder="Price Base">
                </div>
                <div class="form-group">
                <label>Price opt</label>
                <input type="text" class="form-control" name="InPriceOpt" placeholder="Price Opt">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>CURRENCY</label>
                <input type="text" class="form-control" name="InCurrency" placeholder="Currency">
                </div>
                <div class="form-group">
                <label>Price SALES</label>
                <input type="text" class="form-control" name="InPriceSales" placeholder="Price Sales"><p class="xs-rowdelimiter"></p>
                <input type="date" class="form-control" name="InPriceSalesOn" placeholder="YYYY-MM-DD Sales on"><p class="xs-rowdelimiter"></p>
                <input type="date" class="form-control" name="InPriceSalesOff" placeholder="YYYY-MM-DD Sales off">
                </div>
            </div>
            </div>
            <div class="form-group">
                <label>Starting to show only on specific date</label>
                <input type="date" class="form-control" name="InToShow" placeholder="YYYY-MM-DD">
                </div>
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" rows="3" name="InDescr"></textarea>
              </div> 
              <div class="form-group">
                    <label>Categories (ID per row)</label>
                    <textarea class="form-control" name="InCategories" rows="2"></textarea>
                    </div>
            <div class="row">
                <div class="col-md-6">                    
                    <div class="form-group">
                    <label>Tags (comma)</label>
                    <textarea class="form-control" name="InTags" rows="2"></textarea>
                    </div> 
                    <div class="form-group">
                    <label>Connected Pages (ID per row)</label>
                    <textarea class="form-control" name="InConnectedPages" rows="2"></textarea>
                    </div>                    
                </div>    
                <div class="col-md-6">                    
                    <div class="form-group">
                    <label>Parent Products (ID per row)</label>
                    <textarea class="form-control" name="InParentProducts" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                    <label>Sub Products (ID per row)</label>
                    <textarea class="form-control" name="InSubProducts" rows="2"></textarea>
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