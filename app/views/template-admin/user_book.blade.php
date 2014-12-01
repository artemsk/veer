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
                <label>Address</label>
                <input type="text" class="form-control" name="InBusinessName" placeholder="Business Name"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InCountry" placeholder="Country"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InRegion" placeholder="Region"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InCity" placeholder="City"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InPostcode" placeholder="Postcode"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InStreet" placeholder="Street Address"><div class="xs-rowdelimiter"></div>
                <input type="text" class="form-control" name="InStation" placeholder="Nearby Station"><div class="xs-rowdelimiter"></div>
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnOffice"> Office Address
                </label>
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnPrimary"> Primary Entry
                </label>
            </div>
              <button type="submit" class="btn btn-default">Submit</button>
        </div>  
        <div class="col-md-6"> 
                      <div class="form-group">
                        <label>Bank Account</label>
                        <input type="text" class="form-control" name="InInn" placeholder="Inn"><div class="xs-rowdelimiter"></div>
                        <input type="text" class="form-control" name="InAccount" placeholder="Account Number"><div class="xs-rowdelimiter"></div>
                        <input type="text" class="form-control" name="InBank" placeholder="Bank"><div class="xs-rowdelimiter"></div>
                        <input type="text" class="form-control" name="InCorr" placeholder="Corr Account"><div class="xs-rowdelimiter"></div>
                        <input type="text" class="form-control" name="InBik" placeholder="BIK"><div class="xs-rowdelimiter"></div>
                        <textarea class="form-control" name="InOthers" rows="2" placeholder="Foreign Banks"></textarea>
                    </div>
        </div>
    </div>  </form>
</div>
@stop