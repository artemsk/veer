@extends('template.'.$template.'.layout.base')

@section('body')
<div class="container"><form role="form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">             
              <div class="form-group">
                <label>Author</label>
                <input type="text" class="form-control" name="InAuthor" placeholder="Author">
              </div>
              <div class="form-group">
                <label>Customers ID (IF exist)</label>
                <input type="text" class="form-control" name="InCID" placeholder="Customers ID">
              </div> 
              <div class="form-group">
                <label>Comment</label>
                <textarea class="form-control" rows="3" name="InComment"></textarea>
              </div> 
              <div class="form-group">
                <label>Rate (0-5)</label>
                <input type="text" class="form-control" name="InRate" placeholder="Rate">
              </div>            
              <div class="radio">
                <label>Vote (Yes or No)</label><br/>
                {{ Form::radio('InVote', 'Yes') }} Yes <br/>
                {{ Form::radio('InVote', 'No') }} No <br/>
                {{ Form::radio('InVote', 'Blank') }} - <br/>
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