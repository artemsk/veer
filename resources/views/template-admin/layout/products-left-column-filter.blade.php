<small>
    <p><u>filter</u><br/>
    @if(!empty(veer_get('filtered_id')))
    <mark>filtered by {{ veer_get('filtered') }} <a href="{{ route("admin.show", array(veer_get('filtered'))) }}">
            #{{ veer_get('filtered_id') }}</a></mark>
    <br><a href="{{ route("admin.show", "products") }}" class="">&times; reset</a>
    @elseif(veer_get('filtered') == 'unused')
    unused
    <br><a href="{{ route("admin.show", "products") }}" class="">&times; reset</a>
    @else
    <a href="{{ route("admin.show", array("products", "filter" => "unused")) }}">unused</a>
    @endif
</small>
