<div class="common-page">
<ul class="list-group sortable" data-parentid="0">
    @foreach($items as $key => $item)	
    <li class="list-group-item @if($item->hidden == true)
             bg-muted
             @endif sorting-hover">
            <div>
                <a href="{{ route('admin.show', array("pages", "id" => $item->id)) }}"><strong>{{ empty($item->title) ? 'Empty' : $item->title  }}</strong></a>
                <small>{{ Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}
                    #{{$item->id}} 
                    &nbsp;<i class="fa fa-paragraph" title="Characters"></i> {{ strlen($item->small_txt.$item->txt) }}
                    &nbsp;<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $item->views }}
                    @if(count($item->comments) > 0)
                    &nbsp;<span class="glyphicon glyphicon-comment" aria-hidden="true" title="Comments"></span> {{ count($item->comments) }}
                    @endif
                    @if(count($item->subpages) > 0)
                    &nbsp;<span class="glyphicon glyphicon-asterisk" aria-hidden="true" title="Sub pages"></span> {{ count($item->subpages) }}
                    @endif
                    @if(count($item->categories) <= 0)
                    &nbsp;<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
                    @endif
                    @if(is_object($item->user))
                    <a href="{{ route('admin.show', array('users', 'id' => $item->user->id)) }}">{{ '@'.$item->user->username }}</a>
                    @endif
                    <div class="pull-right">
                    <span class="glyphicon glyphicon-sort text-muted" aria-hidden="true" title="Categories"></span>{{ $item->manual_order }}&nbsp;</small>
                @if ($item->hidden == false)
                <button type="submit" name="action" value="changeStatusPage.{{ $item->id }}" class="btn btn-success btn-xs" title="Current: ON (SHOW)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
                @else
                <button type="submit" name="action" value="changeStatusPage.{{ $item->id }}" class="btn btn-warning btn-xs" title="Current: OFF (HIDDEN)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span></button>
                @endif
                @if(!isset($denyDelete) || !$denyDelete)
                &nbsp;<button type="submit" name="action" value="deletePage.{{ $item->id }}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                @else
                &nbsp;<button type="submit" name="action" value="removePage.{{ $item->id }}" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                @endif
                </div>
            </div>
        @if($item->original == true)
        <span class="label label-default"><span class="glyphicon glyphicon-th" aria-hidden="true"></span></span>
        @elseif(File::exists(config('veer.htmlpages_path') . '/' . $item->id . '.html'))
        <span class="label label-default"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></span>
        @endif
        <div class="clearfix"></div>
    </li>
    @endforeach	
</ul>
</div>