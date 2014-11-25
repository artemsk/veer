@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
             
              <div class="form-group">
                <label>Sites ID</label>
                <input type="text" class="form-control" name="InSite" placeholder="Site ID">
              </div>
              <div class="form-group">
                <label>Type (reg, unreg ?)</label>
                <input type="text" class="form-control" name="InType" placeholder="Type">
              </div>            
              <div class="form-group">
                <label>Users ID</label>
                <input type="text" class="form-control" name="InUser" placeholder="User ID">
              </div>
            <div class="row">
                <div class="col-md-4">
              <div class="form-group">
                <label>Email (unreg user)</label>
                <input type="email" class="form-control" name="InEmail" placeholder="Email">
              </div>
                </div><div class="col-md-4">
              <div class="form-group">
                <label>Phone (unreg user)</label>
                <input type="tel" class="form-control" name="InPhone" placeholder="Phone">
              </div>
               </div><div class="col-md-4">
              <div class="form-group">
                <label>Name (unreg user)</label>
                <input type="text" class="form-control" name="InName" placeholder="Name">
              </div>
               </div></div>                 
              <div class="form-group">
                <label>User Book ID (or add new)</label>
                <input type="text" class="form-control" name="InUserBook" placeholder="User Book ID">
              </div> 
              <div class="form-group">
                <label>Payment Method ID</label>
                <input type="text" class="form-control" name="InPayment" placeholder="Payment ID">
              </div>
              
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnFree"> Free Order
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnPaymentDone"> Payment Done
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnClose"> Close
                </label>
              </div>
              <div class="form-group">
                <label>Close Time</label>
                <input type="date" class="form-control" name="InCloseTime" placeholder="YYYY-MM-DD">
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnHidden"> Hidden
                </label>
              </div>  
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnPin"> Pin
                </label>
              </div>  
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnArchive"> Archive
                </label>
              </div>              
              <div class="form-group">
                <label>Score</label>
                <input type="text" class="form-control" name="InScore" placeholder="Score">
              </div>
              
              <button type="submit" class="btn btn-default">Submit</button>
              <div class="rowdelimiter"></div>
        </div>  
        <div class="col-md-6"> 
             <div class="form-group">
                <label>Delivery Method ID</label>
                <input type="text" class="form-control" name="InDelivery" placeholder="Delivery ID">
              </div> 
              <div class="form-group">
                <label>Delivery Price (Will overwrite calculations)</label>
                <input type="text" class="form-control" name="InDeliveryPrice" placeholder="Delivery Price">
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnDeliveryFree"> Delivery Free
                </label>
              </div>
            <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <label>Delivery Plan</label>
                <input type="date" class="form-control" name="InDeliveryPlan" placeholder="YYYY-MM-DD">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>Delivery Real</label>
                <input type="date" class="form-control" name="InDeliveryReal" placeholder="YYYY-MM-DD">
                </div>
            </div>
            </div>
            <div class="form-group">
                    <label>Products & Quantity (ID per row ID:QTY:{PRICE?}:{attr?}:{comm?})</label>
                    <textarea class="form-control" name="InProducts" rows="4"></textarea>
            </div>
            <div class="form-group">
                    <label>Other Positions in Order (Position per row Pos:{PRICE?}:{comm?})</label>
                    <textarea class="form-control" name="InProductsOther" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Status ID (If not new)</label>
                <input type="text" class="form-control" name="InStatus" placeholder="Status ID">
            </div>
            <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" name="InComment" rows="3"></textarea>
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnSend"> Send status & comment to customer?
                </label>
            </div> 
        </div>
    </div>
    
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
    </div>
        
    </form>
</div>
@stop