@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-settings', array('place' => 'secrets'))</div>

            <h3 class="hidden-md">Secrets</h3>

        </div>
        <div class="visible-xs-block visible-sm-block sm-rowdelimiter"></div>
        <div class="col-md-10 main-content-block settings-column">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 text-center">
                    <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="veer-form-submit-configuration"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="thumbnail thumbnail-configuration-list" id="cardnewsecret">
                        <div class="caption"><p><small>NEW PASSWORD | SECRET</small></p>
                            <strong><p><select name="secrets[new][elements_type]" class="form-control">
                                    <option>Veer\Models\Product</option>
                                    <option>Veer\Models\Page</option>
                                    <option>Veer\Models\Order</option>
                                    </select></p>		
                            <p><input type="text" class="form-control text-center" name="secrets[new][elements_id]"
                                              placeholder="ID"></p></strong>			  
                            <p><input class="form-control" placeholder="Password for access" value="{{ str_random(64) }}" 
                                      name="secrets[new][pss]" title="Password for access" data-toggle="tooltip" data-placement="bottom"></p>
                            <button type="submit" class="btn btn-success btn-xs" name="save[newsecret]">
                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                        </div>
                    </div>
                        <input type="hidden" name="secrets[new][id]" value="">
                    </form>
                </div>
                @foreach($items as $item)	
                <div class="col-lg-3 col-md-4 col-sm-6 text-center">
                    <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="veer-form-submit-configuration"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="thumbnail thumbnail-configuration-list" id="card{{$item->id}}">
                        <div class="caption"><p><small>#{{$item->id}} — 
                                    {{ Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}</small></p>					
                            <strong><p><select name="secrets[{{ $item->id }}][elements_type]" class="form-control transparent-textarea">
                                    <option>{{ $item->elements_type }}</option>
                                    <option>Veer\Models\Product</option>
                                    <option>Veer\Models\Page</option>
                                    <option>Veer\Models\Order</option>
                                    </select></p>		
                            <p><input type="text" name="secrets[{{ $item->id }}][elements_id]" class="form-control text-center transparent-textarea" 
                                              placeholder="Elements ID" value="{{ $item->elements_id }}"></p></strong>			  
                            <p><input class="form-control transparent-textarea" placeholder="Password for access" value="{{ $item->secret }}" 
                                      name="secrets[{{ $item->id }}][pss]" title="Password for access" data-toggle="tooltip" data-placement="bottom"></p>
                            <button type="submit" class="btn btn-success btn-xs" name="save[{{$item->id}}]">
                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                            &nbsp;<button type="submit" class="btn btn-danger btn-xs" name="dele[{{$item->id}}]">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                        </div>
                    </div>
                        <input type="hidden" name="secrets[{{ $item->id }}][id]" value="{{ $item->id }}">
                    </form>
                </div>
                @endforeach			
            </div>
        </div>
    </div>
</div>
@stop