@extends($template.'.layout.base')

@section('body')			

<?php event('lock.for.edit'); ?>

<div class="container-fluid">
    <form method="POST" action="{{ URL::full() }}" accept-charset="UTF-8" enctype="multipart/form-data" >
<input name="_method" type="hidden" value="PUT">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="row">
        <div class="col-md-1 pages-breadcrumb">
            <div class="breadcrumb-block">@include($template.'.layout.breadcrumb-structure', array('place' => 'page'))</div>

            <div class="row pages-column">
                <div class="col-md-12 col-sm-12">
                    <h1>Page <small>#{{ $items->id or '—' }}</small></h1>

                    <small><nobr><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> {{ $items->views or '—' }}</nobr><div class="visible-xs-inline visible-sm-inline">
                    | created at <strong>{{ !empty($items->created_at) ?
			Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') : '—' }}</strong>

                    | updated at <strong>{{ !empty($items->created_at) ? Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') : '—' }}</strong></div>
                    </small>
                    @if(veer_get('event.lock-for-edit') == true)
                    <div class="xs-rowdelimiter"></div>
                    <span class="label label-danger">locked</span>
                    @endif
                    <div class="sm-rowdelimiter"></div>
                    <p>
				@if(isset($items->hidden))
					@if ($items->hidden == false)
					<button type="submit" name="action" value="changeStatusPage.{{ $items->id }}" class="btn btn-success" title="Current: ON (SHOW)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-play" aria-hidden="true"></span> Showing</button>
					@else
					<button type="submit" name="action" value="changeStatusPage.{{ $items->id }}" class="btn btn-warning" title="Current: OFF (HIDDEN)" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-pause" aria-hidden="true"></span> Hidden</button>
					@endif
				@endif
                    <div class="sm-rowdelimiter"></div>
                    <div class="hidden-sm hidden-xs">
                    <p>created at<br/><strong>{{ !empty($items->created_at) ?
			Carbon\Carbon::parse($items->created_at)->format('D, j M Y H:i:s') : '—' }}</strong></p>

                    <p>updated at<br/><strong>{{ !empty($items->created_at) ? Carbon\Carbon::parse($items->updated_at)->format('D, j M Y H:i:s') : '—' }}</strong></p></div>
                </div>
            </div>
        </div>
        <div class="col-md-11 main-content-block pages-main">
            <div class="row">
		<div class="col-sm-12"><p><input type="text" class="form-control transparent-input" name="fill[url]" placeholder="Clean Url" value="{{ $items->url or null }}"></p></div>
            </div>
            <div class="xs-rowdelimiter"></div>
            <div class="row">
		<div class="col-sm-12"><p><strong><input type="text" class="form-control input-lg transparent-input" placeholder="Title" name="fill[title]" value="{{ $items->title or null }}"></strong></p></div>
            </div>
<div class="xs-rowdelimiter"></div>
            <div class="row">
                <div class="col-lg-8 col-md-7 col-sm-7">
                    <div class="pages-page">
                    @if(isset($items->id) && File::exists( config('veer.htmlpages_path') . '/' . $items->id . '.html'))
                    <div class="alert alert-success" role="alert">
                            <strong>{{ $items->id }}.html</strong> file exists in "{{ config('veer.htmlpages_path') }}/.." folder.
                    </div>
                    <p></p>
                    @endif
                    <textarea class="form-control page-small-txt" @if(veer_get('event.lock-for-edit') == true) disabled @endif rows="5" name="fill[small_txt]" placeholder="Introduction text">{{ $items->small_txt or null }}</textarea>
                        <span class="page-small-txt-statistics"><small><strong>chars </strong><span class="statistics-chars"></span> <strong>words </strong><span class="statistics-words"></span> <strong>sentences </strong><span class="statistics-sent"></span> | <strong>average word </strong><span class="statistics-avg-word"></span> chars | <strong>average sentence </strong><span class="statistics-avg-sent"></span> words | <strong>current sentence </strong><span class="statistics-current-sent"></span> words</small></span>
                        <small><span class="page-small-txt-saved text-muted"></span></small>


                    <div class="sm-rowdelimiter"></div>
			<textarea class="form-control page-main-txt" @if(veer_get('event.lock-for-edit') == true) disabled @endif rows="15" name="fill[txt]" placeholder="Text">{{ $items->txt or null }}</textarea>
                        @if(isset($items->id))
                        <a href="#" data-toggle="modal" data-target="#previewText" class="previewMarkdownPage"><i class="fa fa-eye"></i></a>
			<div class="modal fade" id="previewText" tabindex="-1" role="dialog" aria-labelledby="previewTextLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">{{ $items->title or null }}</h4>
						</div>
						<div class="modal-body">

							{{ $items->small_txt or null }}
							<hr>

							{{ $items->txt or null }}
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			@endif
                        <span class="page-main-txt-statistics"><small><strong>chars </strong><span class="statistics-chars"></span> <strong>words </strong><span class="statistics-words"></span> <strong>sentences </strong><span class="statistics-sent"></span> | <strong>average word </strong><span class="statistics-avg-word"></span> chars | <strong>average sentence </strong><span class="statistics-avg-sent"></span> words | <strong>current sentence </strong><span class="statistics-current-sent"></span> words</small></span>
                        <small><span class="page-main-txt-saved text-muted"></span></small>

                    </div>
                    <div class="rowdelimiter-20"></div>
                    <div class="pages-page">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group">
				<span class="input-group-addon"><span class="glyphicon glyphicon-sort" aria-hidden="true"></span></span>
				<input type="text" name="fill[manual_order]" class="form-control" placeholder="Sort" value="{{ $items->manual_order or '999' }}">
                                </div>
                                <p></p>
                                <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                                <div class="input-group">
				<span class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
				<input type="text" name="fill[users_id]" class="form-control" placeholder="User id" value="{{ Input::get('user',
						(isset($items->users_id) ? $items->users_id : \Auth::id())) }}">
                                </div>
			
			@if(isset($items->users_id) && $items->users_id > 0)
			@if(is_object($items->user))
			<a href="{{ route('admin.show', array('users', 'id' => $items->user->id)) }}">{{ '@'.$items->user->username }}</a>
			@else
			<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="bad user!"></span>
			@endif
			@endif
                            </div>
                            <div class="sm-rowdelimiter visible-sm-block visible-xs-block"></div>
                            <div class="col-md-9">
                                <div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[original]" data-on-color="warning" data-on-text="Original" data-off-text="Regular"
				   @if(isset($items->original) && $items->original == true) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[show_small]" data-on-color="info" data-on-text="Intro" data-off-text="No&nbsp;Intro"
					   @if(isset($items->show_small) && $items->show_small == true) checked @elseif(!isset($items->show_small)) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[show_comments]" data-on-color="info" data-on-text="Comments" data-off-text="No&nbsp;Comments&nbsp;"
					   @if(isset($items->show_comments) && $items->show_comments == true) checked @elseif(!isset($items->show_comments)) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[show_title]" data-on-color="info" data-on-text="Title" data-off-text="No&nbsp;Title"
					   @if(isset($items->show_title) && $items->show_title == true) checked @elseif(!isset($items->show_title)) checked @endif></div>
			<div class="page-checkboxes-box">
			<input type="checkbox" class="page-checkboxes" name="fill[show_date]" data-on-color="info" data-on-text="Date" data-off-text="No&nbsp;Date"
					   @if(isset($items->show_date) && $items->show_date == true) checked @elseif(!isset($items->show_date)) checked @endif></div>
			<div class="page-checkboxes-box page-checkboxes-box-last">
			<input type="checkbox" class="page-checkboxes" name="fill[in_list]" data-on-color="info" data-on-text="Listed" data-off-text="No&nbsp;Lists"
					   @if(isset($items->in_list) && $items->in_list == true) checked @endif></div>

                            </div>
                        </div>
                    </div>
                    <div class="rowdelimiter-20"></div>
                    <div class="pages-page">
                        <div class="row">
				<div class="col-lg-6">
					<label>Parent pages</label>
					<ul class="list-group">
						@if(isset($items->parentpages) && count($items->parentpages)>0)
						@foreach ($items->parentpages as $p)
						<li class="list-group-item">
							<span class="badge">{{ $p->views }}</span>
							<button type="submit" name="action" value="removeParentPage.{{ $p->id }}" class="btn btn-warning btn-xs">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
							<a href="{{ route('admin.show', array('pages', 'id' => $p->id)) }}">{{ $p->title }}</a>
							<small>{{ Carbon\Carbon::parse($p->created_at)->format('d M Y'); }}</small>
						</li>
						@endforeach
						@endif
						<li class="list-group-item">
								<input type="text" name="attachParentPages" class="form-control input-no-borders" placeholder=":Existings IDs[,]">
						</li>
					</ul>
				</div>
				<div class="col-lg-6">
					<label>Sub pages</label>
					<ul class="list-group">
						@if(isset($items->subpages) && count($items->subpages)>0)
						@foreach ($items->subpages as $p)
						<li class="list-group-item">
							<span class="badge">{{ $p->views }}</span>
							<button type="submit" name="action" value="removeChildPage.{{ $p->id }}" class="btn btn-warning btn-xs">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
							<a href="{{ route('admin.show', array('pages', 'id' => $p->id)) }}">{{ $p->title }}</a>
							<small>{{ Carbon\Carbon::parse($p->created_at)->format('d M Y'); }}</small>
						</li>
						@endforeach
						@endif
						<li class="list-group-item">
								<input type="text" data-type="page" name="attachChildPages" class="form-control input-no-borders show-list-of-items suggestions-page" placeholder=":Existings IDs[,]">
						</li>
                                                <div id="loadedSuggestions-page"></div>
					</ul>
				</div>
			</div>
                        <div class="rowdelimiter-20 visible-lg-block"></div>
			<div class="row">
				<div class="col-md-12">
					<label>Connected Products</label>
					<ul class="list-group">
						@if(isset($items->products) && count($items->products)>0)
						@foreach ($items->products as $prd)
						<li class="list-group-item">
							<span class="badge">{{ $prd->viewed }}</span>
							<button type="submit" name="action" value="removeProduct.{{ $prd->id }}" class="btn btn-warning btn-xs">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
							<a href="{{ route('admin.show', array('products', 'id' => $prd->id)) }}">{{ $prd->title }}</a>
							<small>{{ app('veershop')->getPrice($prd, true, array('forced_currency' => 1)) }}</small>
						</li>
						@endforeach
						@endif
						<li class="list-group-item">
								<input type="text" name="attachProducts" data-type="product" class="show-list-of-items suggestions-product form-control input-no-borders" placeholder=":Existings IDs[,]">
						</li>
                                                <div id="loadedSuggestions-product"></div>
					</ul>
				</div>
			</div>
                    </div>
                </div>
                <div class="sm-rowdelimiter visible-xs-block"></div>
                <div class="col-lg-4 col-md-5 col-sm-5">
                    <div class="pages-page">
                        <label>Categories</label> @if(!isset($items->categories) || count($items->categories) <= 0)
			<span class="glyphicon glyphicon-warning-sign danger-icon" aria-hidden="true" title="No categories!"></span>
			@endif

			<ul class="list-group">
				@if(isset($items->categories) && count($items->categories)>0)
				@foreach ($items->categories as $category)
				<li class="list-group-item">
					<span class="badge">{{ $category->views }}</span>
					<button type="submit" name="action" value="removeCategory.{{ $category->id }}" class="btn btn-warning btn-xs">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;
					<a href="{{ route('admin.show', array('categories', 'category' => $category->id)) }}">{{ $category->title }}</a>
					<small>{{ $category->remote_url }}</small>
				</li>
				@endforeach
				@endif
				<li class="list-group-item">
						<input type="text" name="attachCategories" data-type="category" class="form-control input-no-borders show-list-of-items suggestions-category" placeholder=":Existings IDs[,]"
							   value="{{ null != (Input::get('category')) ? ':'.Input::get('category') : null }}">
				</li>
                                <div id="loadedSuggestions-category"></div>                                
			</ul>
                    </div>
                    <div class="rowdelimiter-20"></div>
                    <div class="pages-page">
                        <label>Attributes</label>
			@if(isset($items->attributes))
                        <div class="row">
			@foreach($items->attributes as $key => $attribute)
                        <div class="col-xs-6"><strong><input type="text" name="attribute[{{ $key }}][name]" class="form-control input-sm attributes-input" value="{{ $attribute->name }}" placeholder="Name" size="15"></strong>
                            <input type="text" name="attribute[{{ $key }}][val]" class="form-control input-sm attributes-input-val" value="{{ $attribute->val }}" placeholder="Value" size="14">
                            <div class="row">
                            <div class="col-sm-12">
                                <div class="collapse" id="descriptionAttributes{{ $key }}" ><p></p><textarea class="form-control input-sm" name="attribute[{{ $key }}][descr]" placeholder="Description">{{ $attribute->descr }}</textarea></div>
                                <a href="#descriptionAttributes{{ $key }}" data-toggle="collapse"><small>description</small></a>
                            </div>
                        </div>
                            <div class="sm-rowdelimiter"></div>
                        </div>
			@endforeach
                        </div>
			@endif
                        <div class="new-attribute-block">
                        <div class="sm-rowdelimiter"></div>
			<div class="row">
				<div class="col-md-12">
                                    <strong><input type="text" name="attribute[new][name]" data-type="attribute" class="form-control input-sm show-list-of-items suggestions-attribute" placeholder="Name" autocomplete="off" id="attributes-suggestions-id"></strong>
                                 <div id="loadedSuggestions-attribute"></div>
				<p></p><input type="text" name="attribute[new][val]" class="form-control input-sm"placeholder="Value">
				<p></p><textarea  name="attribute[new][descr]" class="form-control input-sm" placeholder="Description"></textarea>
				</div>
			</div>
                        </div>
                        <div class="new-attributes-added"></div>
                        <a class="add-more-attributes"><small>more attributes</small></a>
                    </div>
                    <div class="rowdelimiter-20"></div>
                    <div class="pages-page">
                        <label>Tags</label>
                        <textarea class="form-control" rows="5" name="tags" placeholder="Tags (One per row)">@if(isset($items->tags))
@foreach($items->tags as $tag)
{{ $tag->name }}
@endforeach
@endif</textarea>
                    </div>
                    <div class="rowdelimiter-20"></div>
                    <a href="#freeForm" data-toggle="collapse"><small>free form</small></a>
                    <div class="collapse" id="freeForm"><div class="pages-page">
			<textarea class="form-control" name="freeForm" rows="5" placeholder="[Tag:Ids,] [Attribute:Ids,]"></textarea>
                        </div>
                    </div>
                </div>
            </div>
	</div>
    </div>
<div class="rowdelimiter-20"></div>
    <div class="row">
        <div class="col-xs-12 categories-connections-block">
            <div class="categories-connections-name">Images</div><div class="categories-connections"></div>
        </div>
    </div>
    <div class="row">
        @if(isset($items->images) && count($items->images)>1)
        <div class="col-lg-2 col-md-2 col-sm-2">
            <p><button type="submit" class="btn btn-default btn-block" name="action" value="removeAllImages"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span class="hidden-sm">Remove All</span></button>
        </div>
        @endif
        <div class="col-lg-4 col-md-4 col-sm-3">

            <p><input class="form-control show-list-of-items suggestions-image categories-page-input" data-type="image" name="attachImages" placeholder=":Existing Images IDs[,]"></p>
            <div id="loadedSuggestions-image"></div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 color-upload-form">
            <p><input class="input-files-enhance" type="file" id="InFile1" name="uploadImage[]" multiple=true></p>
        </div>
    </div>
    <p></p>
    @if(isset($items->images) && count($items->images)>0)
    <div class="rowdelimiter-20"></div>
    <div class="row">
        <div class="col-md-12">
	@include($template.'.lists.images', array('items' => $items->images, 'denyDelete' => true))
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-xs-12 categories-connections-block">
            <div class="categories-connections-name">Files</div><div class="categories-connections"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-3">
            <p><input class="form-control categories-page-input" name="attachFiles" placeholder=":Existing Files IDs[,]"></p>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 color-upload-form">
            <p><input class="input-files-enhance" type="file" id="InFile2" name="uploadFiles[]" multiple=true></p>
        </div>
    </div>
    <p></p>
    @if(isset($items->downloads) && count($items->downloads)>0)
    <div class="rowdelimiter-20"></div>
    <div class="row">
        <div class="col-md-12">
	@include($template.'.lists.files', array('files' => $items->downloads, 'denyDelete' => true))
        </div>
    </div>
    @endif

    <div class="rowdelimiter-20"></div>
	@if(isset($items->id))
	<div class="row">
		<div class="col-sm-2 col-xs-6"><button type="submit" name="action" value="saveAs" class="btn btn-warning btn-lg btn-block">Save As</button></div>
		<div class="col-sm-10 col-xs-6"><button type="submit" @if(veer_get('event.lock-for-edit') == true) disabled @endif name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
	</div>
        <div class="rowdelimiter-20"></div>
	<hr class="hr-darker">
	<div class="row">
		<div class="col-xs-12 page-stats">
			@if(isset($items['lists']))
			<a class="btn btn-default" href="{{ route('admin.show', array('lists', "filter" => "pages", "filter_id" => $items->id)) }}"
			   role="button">{{ $items['lists'] }} lists</a>
			@endif
			<a class="btn btn-default" href="{{ route('admin.show', array('comments', "filter" => "pages", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->comments()->count() }} comments</a>
			<a class="btn btn-default"  href="{{ route('admin.show', array('communications', "filter" => "pages", "filter_id" => $items->id)) }}"
			   role="button">{{ $items->communications()->count() }} communications</a>
		</div>
	</div>
	@else
	<button type="submit" name="action" value="add" class="btn btn-danger btn-lg btn-block">Add</button>
	@endif

@if(isset($items->id))
<div class="action-hover-box"><button type="submit" @if(veer_get('event.lock-for-edit') == true) disabled @endif name="action" value="update" class="btn btn-danger btn-lg btn-block">Update</button></div>
@endif
</form>
</div>
@stop
