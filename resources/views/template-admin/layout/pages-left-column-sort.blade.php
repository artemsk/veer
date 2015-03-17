<small>
    <u>sort</u><br/>
    @if(null != (\Input::get('sort')))<mark>sorted by {{ \Input::get('sort') }}</mark><br/><a href="{{ route("admin.show", "pages") }}" class="">&times; reset</a><p></p>
    @endif
    <ul class="pages-left-sort-variants">
        <li><a href="{{ route("admin.show", array("pages", "sort" => "created_at", "sort_direction" => "desc")) }}">created</a></li>
        <li><a href="{{ route("admin.show", array("pages", "sort" => "updated_at", "sort_direction" => "desc")) }}">updated</a></li>
        <li><a href="{{ route("admin.show", array("pages", "sort" => "manual_order", "sort_direction" => "desc")) }}">order</a></li>
        <li><a href="{{ route("admin.show", array("pages", "sort" => "views", "sort_direction" => "desc")) }}">views</a></li>
        <li><a href="{{ route("admin.show", array("pages", "sort" => "hidden", "sort_direction" => "desc")) }}">hidden</a></li>
        <li><a href="{{ route("admin.show", array("pages", "sort" => "original", "sort_direction" => "desc")) }}">original</li>
    </ul>
</small>