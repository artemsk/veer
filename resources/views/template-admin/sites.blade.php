@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'sites'))</div>

            <h1>{{ trans("themes/".$template.".sites.sites") }}</h1>

            <p><i class="fa fa-user"></i> {{ data_get(app('veer'), 'online') }}
                <small>{{ trans("themes/".$template.".sites.online") }}</small>
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block">
            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    @foreach ($items as $item)
                    <div class="col-sm-4"><div class="sites-block">
                            @if(file_exists(public_path().'/'.config('veer.images_path').'/site-'.$item->id.'.jpg'))<p><img src="{{ asset(config('veer.images_path').'/site-'.$item->id.'.jpg') }}" class="img-responsive site-snapshot">@endif
                            <p><input type="url" class="form-control text-center" name="site[{{ $item->id }}][url]" placeholder="{{ trans("themes/".$template.".sites.siteurl") }}" value="{{ $item['url'] }}">
                            <div class="sites-block-half">
                                <p><strong>{{ trans("themes/".$template.".sites.parent") }}</strong>
                                    <input type="text" class="form-control" name="site[{{ $item->id }}][parent_id]" placeholder="{{ trans("themes/".$template.".sites.parentid") }}"
                                           size="3" value="{{ $item['parent_id'] }}">
                            </div>
                            <div class="sites-block-half sites-block-last">
                                <p><strong>{{ trans("themes/".$template.".sites.sort") }}</strong>
                                    <input type="text" class="form-control" name="site[{{ $item->id }}][manual_sort]" placeholder="{{ ucfirst(trans("themes/".$template.".sites.sort")) }}" size="3" value="{{ $item['manual_sort'] }}">
                            </div>
                            <div class="clearfix"></div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="site[{{ $item->id }}][redirect_on]" @if((bool)$item['redirect_on'] == true) checked @endif>
                                </span>
                                <input type="url" class="form-control" name="site[{{ $item->id }}][redirect_url]"
                                       placeholder="{{ trans("themes/".$template.".sites.redirecturl") }}" value="{{ $item['redirect_url'] }}">
                            </div><!-- /input-group -->
                            <p></p>
                            <p><small><strong>#{{ $item['id'] }}</strong> | {{ strftime('%d %b %Y %H:%M:%S', strtotime($item['updated_at'])) }} — {{ trans("themes/".$template.".sites.created") }}
                                    {{ strftime('%d %b %Y', strtotime($item['created_at'])) }}</small>
                            <p>@if ((bool)$item['on_off'] == true)
                                <button type="submit" name="turnoff" class="btn btn-success btn-block" value="{{$item->id}}">{{ trans("themes/".$template.".sites.live") }}</button>
                                @else
                                <button type="submit" name="turnon" class="btn btn-danger btn-block" value="{{$item->id}}">{{ trans("themes/".$template.".sites.hold") }}</button>
                                @endif

                            <p><small>
                                    <a href="{{ route("admin.show", array("categories", "#site".$item->id)) }}">{{ $categories = $item->categories()->count() }} {{ Lang::choice('themes/'.$template.'.sites.categories', $categories, []) }}</a>,
                                    <a href="{{ route("admin.show", array("components", "site" => $item->id)) }}">{{ $components = $item->components()->count() }} {{ Lang::choice('themes/'.$template.'.sites.components', $components, []) }}</a>,
                                    <a href="{{ route("admin.show", array("configuration", "site" => $item->id)) }}">{{ $configuration = $item->configuration()->count() }} {{ Lang::choice('themes/'.$template.'.sites.configurations', $configuration, []) }}</a>,
                                     {{ $elements = $item->elements()->count() }} <a href="{{ route("admin.show", array("products", "filter" => "site", "filter_id" => $item->id)) }}">{{ Lang::choice('themes/'.$template.'.sites.products', $elements, []) }}</a> & <a href="{{ route("admin.show", array("pages", "filter" => "site", "filter_id" => $item->id)) }}">{{ Lang::choice('themes/'.$template.'.sites.pages', $elements, []) }}</a>,
                                    <a href="{{ route("admin.show", array("users", "filter" => "site", "filter_id" => $item->id)) }}">{{ $users = $item->users()->count() }} {{ Lang::choice('themes/'.$template.'.sites.users', $users, []) }}</a>,
                                    <a href="{{ route("admin.show", array("discounts", "filter" => "site", "filter_id" => $item->id)) }}">{{ $discounts = $item->discounts()->count() }} {{ Lang::choice('themes/'.$template.'.sites.discounts', $discounts, []) }}</a>,
                                    <a href="{{ route("admin.show", array("lists", "filter" => "site", "filter_id" => $item->id)) }}">{{ $userlists = $item->userlists()->count() }} {{ Lang::choice('themes/'.$template.'.sites.lists', $userlists, []) }}</a>,
                                    <a href="{{ route("admin.show", array("orders", "filter" => "site", "filter_id" => $item->id)) }}">{{ $orders = $item->orders()->count() }} {{ Lang::choice('themes/'.$template.'.sites.orders', $orders, []) }}</a>,
                                    <a href="{{ route("admin.show", array("shipping", "filter" => "site", "filter_id" => $item->id)) }}">{{ $delivery = $item->delivery()->count() }} {{ Lang::choice('themes/'.$template.'.sites.shipping', $delivery, []) }}</a>,
                                    <a href="{{ route("admin.show", array("payment", "filter" => "site", "filter_id" => $item->id)) }}">{{ $payment = $item->payment()->count() }} {{ Lang::choice('themes/'.$template.'.sites.payment', $payment, []) }}</a>,
                                    <a href="{{ route("admin.show", array("communications", "filter" => "site", "filter_id" => $item->id)) }}">{{ $communications = $item->communications()->count() }} {{ Lang::choice('themes/'.$template.'.sites.communications', $communications, []) }}</a>,
                                    <a href="{{ route("admin.show", array("roles", "filter" => "site", "filter_id" => $item->id)) }}">{{ $roles = $item->roles()->count() }} {{ Lang::choice('themes/'.$template.'.sites.roles', $roles, []) }}</a>,
                                    {{ $subsites = $item->subsites()->count() }} {{ Lang::choice('themes/'.$template.'.sites.childsites', $subsites, []) }}</small>
                        </div>
                    </div>
                    @endforeach

                     <div class="col-sm-4"><div class="sites-block">
                             <p><input type="url" class="form-control text-center" name="site[{{ ($item->id)+1 }}][url]" placeholder="{{ trans("themes/".$template.".sites.siteurl") }}" value="">
                            <div class="sites-block-half">
                                <p><strong>{{ trans("themes/".$template.".sites.parent") }}</strong>
                                    <input type="text" class="form-control" name="site[{{ ($item->id)+1 }}][parent_id]" placeholder="{{ trans("themes/".$template.".sites.parentid") }}" size="3">
                            </div>
                            <div class="sites-block-half sites-block-last">
                                <p><strong>{{ trans("themes/".$template.".sites.sort") }}</strong>
                                    <input type="text" class="form-control" name="site[{{ ($item->id)+1 }}][manual_sort]" placeholder="{{ ucfirst(trans("themes/".$template.".sites.sort")) }}" size="3">
                            </div>
                            <div class="clearfix"></div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="site[{{ ($item->id)+1 }}][redirect_on]">
                                </span>
                                <input type="url" class="form-control" name="site[{{ ($item->id)+1 }}][redirect_url]" placeholder="{{ trans("themes/".$template.".sites.redirecturl") }}" value="">
                            </div><!-- /input-group -->
                     </div></div>
                </div>
                <hr class="hr-narrow">
                <button type="submit" name="update" class="btn btn-default">{{ trans("themes/".$template.".sites.update") }}</button>
                <button type="submit" name="snapshots" value="refresh" class="btn btn-default pull-right">{{ trans("themes/".$template.".sites.refresh") }}</button>
                <input type="hidden" name="_action" value="update">
                
            </form>

        </div>
    </div>
</div>

@stop