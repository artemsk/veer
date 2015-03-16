@extends($template.'.layout.base')

@section('body')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="breadcumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'category'))</div>
        </div>
        <div class="visible-xs sm-rowdelimiter"></div>
        <div class="col-sm-10 main-content-block categories-page">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-8 ">
                            <ol class="breadcrumb breadcrumb-top-categories">
                                <li><a class="btn btn-info btn-xs" href="{{ route("admin.show", array("categories","#site".$items->sites_id)) }}">{{ empty($items->site_title) ? "Categories" : $items->site_title }}</a></li>
                                @if (count($items->parentcategories)<=0)
                                <li><button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Parent category"
                                            data-content='
                                            <div class="form-inline">
                                            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="text" class="form-control" placeholder="Id" size=2 name="parentId">
                                            <button class="btn btn-info" type="submit" name="action" value="saveParent"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                                            </form>
                                            </div>
                                            ' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>
                                </li>
                                @endif
                                @foreach ($items->parentcategories as $category)
                                <li><button type="button" class="btn btn-xs btn-info" data-toggle="popover" title="Replace parent category"
                                            data-content='
                                            <div class="form-inline">
                                            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="text" class="form-control" placeholder="Id" size=2 name="parentId" value="{{ $category->id }}">
                                            <button class="btn btn-info" type="submit" name="action" value="updateParent">
                                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                                            <button class="btn btn-warning" type="submit" name="action" value="removeParent">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                                            <input type="hidden" name="lastCategoryId" value="{{ $category->id }}"><br>
                                            <small><a href="{{ app('url')->current() }}?category={{ $category->id }}">{{ $category->title }}</a></small>
                                            </form>
                                            </div>
                                            ' data-html="true">{{ $category->title }} <span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>
                                </li>
                                @endforeach
                            </ol>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 text-right">
                            <span class="badge">{{ count($items->subcategories) }} child</span>
                            <span class="badge">{{ $items->views }} views</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                        <input name="_method" type="hidden" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-sm-10 col-md-12">
                                        <h2><input type="text" class="form-control admin-form" placeholder="Title" name="title" value="{{ $items->title }}"></h2>
                                    </div>
                                    <div class="col-sm-2 visible-sm-block visible-xs-block categories-update-button">
                                        <p></p>
                                        <p><button type="submit" class="btn btn-default" name="action" value="updateCurrent">Update</button>
                                    </div>
                                </div>
                                <div class="testajax"></div>
                                <ul class="list-group sortable @if(count($items->subcategories) > 0)categories-child-list @else categories-child-list-no-bottom @endif" data-parentid="{{ $items->id }}">
                                    @foreach ($items->subcategories as $category)
                                    <li class="list-group-item category-item-{{ $category->id }}">
                                        <span class="badge">{{ $category->views }}</span>
                                        <button type="button" class="btn btn-info btn-xs" data-toggle="popover" title="Replace parent category" data-content='
                                                <div class="form-inline">
                                                <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8"><input name="_method" type="hidden" value="PUT"><input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="text" class="form-control" placeholder="Id" size=2 name="parentId" value="{{ $items->id }}">
                                                <button class="btn btn-info" type="submit" name="action" value="updateInChild">
                                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                                                <button class="btn btn-warning" type="submit" name="action" value="removeInChild">
                                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                                                <input type="hidden" name="lastCategoryId" value="{{ $items->id }}">
                                                <input type="hidden" name="currentChildId" value="{{ $category->id }}">
                                                </form>
                                                </div>
                                                ' data-html="true"><span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span></button>&nbsp;
                                        <button type="button" class="btn btn-danger btn-xs category-delete" data-categoryid="{{ $category->id }}">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;
                                        <a href="{{ app('url')->current() }}?category={{ $category->id }}">{{ $category->title }}</a>
                                        <small>{{ $category->remote_url }}
                                            <span class="additional-info">{{ count($category->pages) }} <span class="glyphicon glyphicon-file" aria-hidden="true"></span> &nbsp; {{ count($category->products) }} <i class="fa fa-gift"></i> &nbsp; {{ count($category->subcategories) }} <i class="fa fa-bookmark-o"></i></span>
                                        </small>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="input-group">
                                    <input type="text" data-type="category" class="form-control show-list-of-items suggestions-category" placeholder="Title or :Existing-category-ID" name="child">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit" name="action" value="addChild">Add</button>
                                    </span>
                                </div>
                                <div id="loadedSuggestions-category"></div>
                            </div>
                            <div class="col-md-3 text-right">
                                <input type="url" class="form-control admin-form categories-remote-url" placeholder="Remote Url if exists" name="remoteUrl" value="{{ $items->remote_url }}">
                                <p><textarea class="form-control" placeholder="Description" name="description">{{ $items->description }}</textarea>

                                <p><button type="submit" class="btn btn-default" name="action" value="updateCurrent">Update</button>
                                    &nbsp;<button type="submit" class="btn btn-danger" name="action" value="deleteCurrent">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">

            <div class="rowdelimiter"></div>

            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="row">
                    <div class="col-xs-12 categories-connections-block">
                        <div class="categories-connections-name">Images</div><div class="categories-connections"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-3">
                        <p><input class="form-control show-list-of-items suggestions-image" data-type="image" name="attachImages" placeholder=":Existing Images IDs[,]"></p>
                        <div id="loadedSuggestions-image"></div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <p><input class="input-files-enhance" type="file" id="InFile1" name="uploadImage[]" multiple=true></p>
                    </div>                    
                    <div class="col-lg-2 col-md-2 col-sm-3">
                        <p><button class="btn btn-primary btn-block" type="submit" name="action" value="updateImages">Upload | Update</button></p>
                    </div>
                    @if(count($items->images)>1)
                    <div class="col-lg-2 col-md-2 col-sm-3">
                        <p><button type="submit" class="btn btn-default" name="action" value="removeAllImages"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Remove All</button>
                    </div>
                    @endif
                </div>
                <p></p>
                @if(count($items->images)>0)
                <div class="row">
                    <div class="col-md-12">
                        @include($template.'.lists.images', array('items' => $items->images, 'denyDelete' => true))
                    </div>
                </div>
                @endif
            </form>

            <div class="rowdelimiter"></div>

            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-xs-12 categories-connections-block">
                        <div class="categories-connections-name">Products</div><div class="categories-connections"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-md-2">
                        <p><a class="btn btn-primary btn-block" href="{{ route("admin.show", array("products",
						"id" => "new", "category" => $items->id)) }}" role="button">New product</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="input-group">
                            <input type="text" data-type="product" class="form-control show-list-of-items suggestions-product" name="attachProducts" placeholder=":Existing IDs">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit" name="action" value="updateProducts">Add</button>
                            </span>
                        </div>
                        <div id="loadedSuggestions-product"></div>
                        <p></p>
                    </div>
                </div>
                <p></p>
                @if(count($items->products)>0)
                <div class="row">
                    <div class="col-md-12">
                        @include($template.'.lists.products', array('items' => $items->products, 'denyDelete' => true))
                    </div>
                </div>
                @endif
            </form>

            <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="rowdelimiter"></div>

                <div class="row">
                    <div class="col-xs-12 categories-connections-block">
                        <div class="categories-connections-name">Pages</div><div class="categories-connections"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3 col-md-2">
                        <p><a class="btn btn-primary btn-block" href="{{ route("admin.show", array("pages",
				"id" => "new", "category" => $items->id)) }}" role="button">New page</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="input-group">
                            <input type="text" data-type="page" class="form-control show-list-of-items suggestions-page" name="attachPages" placeholder=":Existing IDs">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit" name="action" value="updatePages">Add</button>
                            </span>
                        </div>
                        <div id="loadedSuggestions-page"></div>
                        <p></p>
                    </div>
                </div>
                <p></p>
                @if(count($items->pages)>0)
                <div class="row">
                    <div class="col-md-12">
                        @include($template.'.lists.pages', array('items' => $items->pages, 'denyDelete' => true))
                    </div>
                </div>
                @endif
            </form>

            @if(count($items->communications)>0)
            <div class="rowdelimiter"></div>
            <a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "categories", "filter_id" => $items->id)) }}"
               role="button">{{ $items->communications()->count() }} communications</a>
            @endif

        </div>
    </div>

@stop
