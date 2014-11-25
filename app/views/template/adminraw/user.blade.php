@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Site ID</label>
                <input type="text" class="form-control" name="InSite" placeholder="Site ID">
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="InEmail" placeholder="Email">
              </div>           
              <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="InPassword" placeholder="Password">
              </div>
              <div class="form-group">
                <label>Role (user, distributor, wholesaler, author)</label>
                <input type="text" class="form-control" name="InRole" placeholder="Role">
              </div>
              <div class="form-group">
                <label>Gender</label>
                <select name="InGender" class="form-control">
                            <option></option>
                            <option>m</option>
                            <option>f</option>
                </select>
              </div> 
              <div class="form-group">
                <label>First name</label>
                <input type="text" class="form-control" name="InFirst" placeholder="First name">
              </div>
              <div class="form-group">
                <label>Last name</label>
                <input type="text" class="form-control" name="InLast" placeholder="Last name">
              </div> 
              <div class="form-group">
                <label>Birth</label>
                <input type="date" class="form-control" name="InBirth" placeholder="YYYY-MM-DD">
              </div>
              <div class="form-group">
                <label>Phone</label>
                <input type="tel" class="form-control" name="InPhone" placeholder="Phone">
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnNewsletter"> Newsletter
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnBan"> Banned
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnRestrictOrders"> Restrict Orders
                </label>
              </div> 
              <button type="submit" class="btn btn-default">Submit</button>
        </div>  
        <div class="col-md-6"> 
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnAdministrator"> Administrator
                </label>
            </div>
            <div class="row">
                <div class="col-md-6">
            <div class="form-group">
                <label>Discount</label>
                <input type="text" class="form-control" name="InDiscount" placeholder="Percent">
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnDiscountExp"> Expires?
                </label>
            </div>
            <div class="form-group">
                <label>Discount Expiration Day</label>
                <input type="date" class="form-control" name="InDiscountExpDay" placeholder="YYYY-MM-DD">
            </div> 
             <div class="form-group">
                <label>Discount Expiration Times</label>
                <input type="date" class="form-control" name="InDiscountExpTimes" placeholder="Times">
            </div> 
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>User List</label>
                        <input type="text" class="form-control" name="InUserList" placeholder="User List">
                    </div>
                    <div class="form-group">
                    <label>Products in List (Id Per Row: Prd:Qty)</label>
                    <textarea class="form-control" name="InListProducts" rows="1"></textarea>
                    </div>
                    <div class="form-group">
                    <label>Pages in List (Id Per Row)</label>
                    <textarea class="form-control" name="InListPages" rows="1"></textarea>
                    </div>
                </div>
            </div><div class="rowdelimiter"></div>
            
            <div class="row">
                <div class="col-md-6">
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
            </div>
           
        </div>
    </div>  </form>
</div>
@stop