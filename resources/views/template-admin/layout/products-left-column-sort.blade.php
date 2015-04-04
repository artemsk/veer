<small>
    <u>sort</u><br/>
    @if(null != (\Input::get('sort')))<mark>sorted by {{ \Input::get('sort') }}</mark><br/><a href="{{ route("admin.show", "products") }}" class="">&times; reset</a><p></p>
    @endif
    <ul class="pages-left-sort-variants">
        <li><a href="{{ route("admin.show", array("products", "sort" => "to_show", "sort_direction" => "desc")) }}">to show</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "updated_at", "sort_direction" => "desc")) }}">updated</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "viewed", "sort_direction" => "desc")) }}">views</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "ordered", "sort_direction" => "desc")) }}">orders</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "price", "sort_direction" => "desc")) }}">price</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "qty", "sort_direction" => "asc")) }}">quantity</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "weight", "sort_direction" => "desc")) }}">weight</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "score", "sort_direction" => "desc")) }}">score</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "status", "sort_direction" => "asc")) }}">status</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "download", "sort_direction" => "desc")) }}">download</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "title", "sort_direction" => "asc")) }}">title</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "price_sales_on", "sort_direction" => "desc")) }}">sales on</a></li>
        <li><a href="{{ route("admin.show", array("products", "sort" => "star", "sort_direction" => "desc")) }}">star</a></li>
    </ul>
</small>
