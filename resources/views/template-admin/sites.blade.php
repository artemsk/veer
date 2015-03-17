@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'sites'))</div>

            <h1>Sites</h1>

            <p><i class="fa fa-user"></i> {{ data_get(app('veer'), 'online') }}
                <small>online</small>
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block">
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    @foreach ($items as $item)
                    <div class="col-sm-4"><div class="sites-block">
                            @if(file_exists(public_path().'/'.config('veer.images_path').'/site-'.$item->id.'.jpg'))<p><img src="{{ asset(config('veer.images_path').'/site-'.$item->id.'.jpg') }}" class="img-responsive site-snapshot">@endif
                            <p><input type="url" class="form-control text-center" name="site[{{ $item->id }}][url]" placeholder="Site Url" value="{{ $item['url'] }}">
                            <div class="sites-block-half">
                                <p><strong>parent</strong>
                                    <input type="text" class="form-control" name="site[{{ $item->id }}][parent_id]" placeholder="Parent Id"
                                           size="3" value="{{ $item['parent_id'] }}">
                            </div>
                            <div class="sites-block-half sites-block-last">
                                <p><strong>sort</strong>
                                    <input type="text" class="form-control" name="site[{{ $item->id }}][manual_sort]" placeholder="Sort" size="3" value="{{ $item['manual_sort'] }}">
                            </div>
                            <div class="clearfix"></div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="site[{{ $item->id }}][redirect_on]" @if((bool)$item['redirect_on'] == true) checked @endif>
                                </span>
                                <input type="url" class="form-control" name="site[{{ $item->id }}][redirect_url]"
                                       placeholder="Redirect Url" value="{{ $item['redirect_url'] }}">
                            </div><!-- /input-group -->
                            <p></p>
                            <p><small><strong>#{{ $item['id'] }}</strong> | {{ strftime('%d %b %Y %H:%M:%S', strtotime($item['updated_at'])) }} — created
                                    {{ strftime('%d %b %Y', strtotime($item['created_at'])) }}</small>
                            <p>@if ((bool)$item['on_off'] == true)
                                <button type="submit" name="turnoff" class="btn btn-success btn-block" value="{{$item->id}}">Live</button>
                                @else
                                <button type="submit" name="turnon" class="btn btn-danger btn-block" value="{{$item->id}}">On Hold</button>
                                @endif

                            <p><small>
                                    <a href="{{ route("admin.show", array("categories", "#site".$item->id)) }}">{{ $item->categories()->count() }} categories</a>,
                                    <a href="{{ route("admin.show", array("components", "site" => $item->id)) }}">{{ $item->components()->count() }} components</a>,
                                    <a href="{{ route("admin.show", array("configuration", "site" => $item->id)) }}">{{ $item->configuration()->count() }} configurations</a>,
                                    <a href="{{ route("admin.show", array("users", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->users()->count() }} users</a>,
                                    <a href="{{ route("admin.show", array("discounts", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->discounts()->count() }} discounts</a>,
                                    <a href="{{ route("admin.show", array("lists", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->userlists()->count() }} lists</a>,
                                    <a href="{{ route("admin.show", array("orders", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->orders()->count() }} orders</a>,
                                    <a href="{{ route("admin.show", array("shipping", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->delivery()->count() }} shipping</a>,
                                    <a href="{{ route("admin.show", array("payment", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->payment()->count() }} payment</a>,
                                    <a href="{{ route("admin.show", array("communications", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->communications()->count() }} messages</a>,
                                    <a href="{{ route("admin.show", array("roles", "filter" => "site", "filter_id" => $item->id)) }}">{{ $item->roles()->count() }} roles</a>,
                                    {{ $item->elements()->count() }} <a href="{{ route("admin.show", array("products", "filter" => "site", "filter_id" => $item->id)) }}">products</a> & <a href="{{ route("admin.show", array("pages", "filter" => "site", "filter_id" => $item->id)) }}">pages</a>, {{ $item->subsites()->count() }} sub-sites</small>
                        </div>
                    </div>
                    @endforeach

                     <div class="col-sm-4"><div class="sites-block">
                             <p><input type="url" class="form-control text-center" name="site[{{ ($item->id)+1 }}][url]" placeholder="Site Url" value="">
                            <div class="sites-block-half">
                                <p><strong>parent</strong>
                                    <input type="text" class="form-control" name="site[{{ ($item->id)+1 }}][parent_id]" placeholder="Parent Id" size="3">
                            </div>
                            <div class="sites-block-half sites-block-last">
                                <p><strong>sort</strong>
                                    <input type="text" class="form-control" name="site[{{ ($item->id)+1 }}][manual_sort]" placeholder="Sort" size="3">
                            </div>
                            <div class="clearfix"></div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="site[{{ ($item->id)+1 }}][redirect_on]">
                                </span>
                                <input type="url" class="form-control" name="site[{{ ($item->id)+1 }}][redirect_url]" placeholder="Redirect Url" value="">
                            </div><!-- /input-group -->
                     </div></div>
                </div>
                <hr class="hr-narrow">
                <button type="submit" name="update" class="btn btn-default">Update</button>
                <button type="submit" name="snapshots" value="refresh" class="btn btn-default pull-right">Refresh snapshots</button>
                <input type="hidden" name="_action" value="update">
                
            </form>

        </div>
    </div>
</div>

@stop