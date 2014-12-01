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
                <label>Users ID (IF exist)</label>
                <input type="text" class="form-control" name="InUser" placeholder="User ID">
              </div> 
              <div class="form-group">
                <label>Sender</label>
                <input type="text" class="form-control" name="InSender" placeholder="Sender">
              </div>
              <div class="form-group">
                <label>Sender Phone</label>
                <input type="tel" class="form-control" name="InSenderPhone" placeholder="Sender Phone">
              </div>
              <div class="form-group">
                <label>Sender Email</label>
                <input type="email" class="form-control" name="InSenderEmail" placeholder="Sender Email">
              </div>
              <div class="form-group">
                <label>Message</label>
                <textarea class="form-control" rows="3" name="InMessage"></textarea>
              </div> 
              <div class="form-group">
                <label>Theme</label>
                <input type="text" class="form-control" name="InTheme" placeholder="Theme">
              </div>
              <div class="form-group">
                <label>Type (callme, im, email)</label>
                <input type="text" class="form-control" name="InType" placeholder="Type">
              </div> 
              
     </div> 
     <div class="col-md-6">                    
                    <div class="form-group">
                    <label>Recipients (@name per row)</label>
                    <textarea class="form-control" rows="3" name="InRecipients"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Url</label>
                        <input type="url" class="form-control" name="InUrl" placeholder="Url">
                    </div> 
                    <div class="form-group">
                    <label>Connected Pages (ID per row)</label>
                    <textarea class="form-control" name="InConnectedPages" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                    <label>Connected Products (ID per row)</label>
                    <textarea class="form-control" name="InConnectedProducts" rows="3"></textarea>
                    </div>      
                    <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnNotify"> Email Notify
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnIntranet"> Intranet
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="OnHidden"> Hidden
                </label>
              </div>
              <button type="submit" class="btn btn-default">Submit</button> 
    </div> 
</div> </form>
@stop