@foreach($data as $suggest_id => $suggests)
<li class="list-group-item suggestions-clickable" data-separator="{{ \Input::get('separator',',') }}" data-whole="{{ \Input::get('whole') }}" data-chosen="@if($model != 'attribute'){{ $suggest_id }}@else{{ $suggests }}@endif">@if($model != 'image'){{ $suggests }}@else<img src="{{ asset(config('veer.images_path').'/'.$suggests) }}" class="suggests-images"> — #{{ $suggest_id }}@endif</li>
@endforeach

<script>
    $('.suggestions-clickable').click(function() {

        var d = $(this).attr('data-whole');
        var suggest = $(this).attr('data-chosen');

        var darr = d.split(',');
        darr[darr.length-1] = suggest;

        @if($model != "attribute")
            d = darr.join() + ',';
            if(d.slice(0,1) != ':') { d = ':' + d; }
        @else
            d = darr.join();
            $('#loadedSuggestions-<?php echo $model.(\Input::get('selectorId','')); ?>').html('');
        @endif
        $('.suggestions-<?php echo $model.(\Input::get('selectorId','')); ?>').val(d).focus();
    });
</script>