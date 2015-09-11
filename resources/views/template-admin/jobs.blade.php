@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 pages-breadcrumb">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-settings', array('place' => 'jobs'))</div>

            <h3 class="hidden-md">Jobs<br/><small>failed jobs</small></h3>

        </div>
        <div class="visible-xs-block visible-sm-block sm-rowdelimiter"></div>
        <div class="col-md-10 main-content-block settings-column pages-main">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 text-center">
                    <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="veer-form-submit-configuration">
                    <input name="_method" type="hidden" value="PUT">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="thumbnail queues-wrapper thumbnail-configuration-list" id="cardnewsecret">
                        <div class="caption"><p><small>NEW JOB</small></p>
                            <strong><p><input type="text" class="form-control hidden queues-class-input" name="jobs[new][classname]" disabled="true"
                                              placeholder="Classname for Queues"></p>
                                <p><select class="form-control queues-class-select" placeholder="Classname for Queues" name="jobs[new][classname]">
                                    <option value="">Classname for Queues</option>    
                                    <option value="+">+ Add</option>    
                                    @foreach(data_get($items, 'availModules.queues', []) as $module)
                                    <option>{{ $module }}</option>@endforeach
                                    </select></p>
                            <p><input type="text" class="form-control" name="jobs[new][data]"
                                              placeholder="Array of data"></p></strong>			  
                            <p><input class="form-control" placeholder="Repeat time [0]" 
                                      name="jobs[new][repeat]" title="Set repeat time in minutes" data-toggle="tooltip" data-placement="bottom"></p>
                            <p><input class="form-control date-container" placeholder="Start time [now|manual|date]" 
                                      name="jobs[new][start]" title="Choose date to start" data-toggle="tooltip" data-placement="bottom"></p>
                            <button type="submit" class="btn btn-success btn-xs" name="save" value="newjob">
                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                        </div>
                    </div>
                    </form>
                </div>
                @foreach($items['jobs'] as $item)	
                <div class="col-lg-3 col-md-4 col-sm-6 text-center">
                    <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" class="veer-form-submit-configuration">
                    <input name="_method" type="hidden" value="PUT">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="thumbnail thumbnail-configuration-list" id="card{{$item->id}}">
                        <div class="caption"><small>#{{$item->id}} —
                                    {{ \Carbon\Carbon::parse($item->updated_at)->format("m/d") }}</small>					
                            <strong><p>{{ $items['statuses'][$item->status] }} : {{ $item->attempts }}</p></strong>	
                            <p>scheduled at:<br/>{{ $item->available_at }}</p>	
                            <p>updated at:<br/>{{ $item->updated_at }}</p>	
                            <p><textarea name="payload" class="form-control transparent-textarea" rows="5">{{ $item->payload }}</textarea></p>	
                            @if($items['statuses'][$item->status] != 'Started')
                            <button type="submit" class="btn btn-info btn-xs" name="_run[{{ $item->id }}]">
                                <span class="glyphicon glyphicon-play" aria-hidden="true"></span> Run</button>
                            @endif
                            @if($items['statuses'][$item->status] == 'Open' || $items['statuses'][$item->status] == 'Waiting')
                            <button type="submit" class="btn btn-warning btn-xs" name="paus[{{ $item->id }}]">
                                <span class="glyphicon glyphicon-pause" aria-hidden="true"></span> Pause</button>
                            @endif					
                            <button type="submit" class="btn btn-danger btn-xs" name="dele[{{ $item->id }}]">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>

                        </div>
                    </div>
                    </form>
                </div>
                @endforeach	
                @foreach($items['failed'] as $item)	
                <div class="col-lg-3 col-md-4 col-sm-6 text-center">
                    <div class="thumbnail">
                        <div class="caption"><small>#{{$item->id}} FAILED</small>
                            <p>{{ $item->failed_at }}</p>
                            <p>{{ $item->connection }}</p>	
                            <p>{{ $item->connection }}</p>		  
                            <p>{{ $item->queue }}</p>	
                            <p><textarea class="form-control" rows="5">{{ $item->payload }}</textarea></p>	
                            <button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                        </div>
                    </div>
                </div>
                @endforeach		
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script>    
    $(document).on('change', '.queues-class-select', {}, function() {
        
        var card = $(this).parents('.queues-wrapper');
        if($(this).val() == '+') {
            card.find('.queues-class-select').addClass('hidden').prop('disabled', true);
            card.find('.queues-class-input').removeClass('hidden').prop('disabled', false);
        }
    });
</script>
@stop