<?php

namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Page as ShowPage;

class PageController extends Controller
{
    protected $showPage;

    public function __construct(ShowPage $showPage)
    {
        parent::__construct();

        $this->showPage = $showPage;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $pages = $this->showPage->getPagesWithSite(app('veer')->siteId);

        $pages->load('categories', 'user');

        return $this->viewIndex('pages', $pages);

        // TODO: number of comments?
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        // if exist external content then send it to browser, increment views
        $external_content = $this->showPage->checkAndGetContentFromHtmlFile(app('veer')->siteId,
            $id);

        if (!empty($external_content)) {
            \Veer\Models\Page::where('id', '=', $id)->increment('views');

            return \Response::make($external_content, 200)->header('Content-type',
                    'text/html');
        }

        $page = $this->showPage->getPage($id, app('veer')->siteId);

        if (!is_object($page)) {
            return Redirect::route('page.index');
        }

        $page->increment('views');

        $page->load(array('images' => function($q) {
            return $q->orderBy('pivot_id', 'asc');
        }, 'tags', 'attributes', 'downloads', 'userlists', 'user'));

        if ($page->show_comments == 1)
                $this->showPage->loadComments($page, 'page');

        $paginator_and_sorting = get_paginator_and_sorting();

        $data = array(
            "page" => $page,
            "subpages" => $this->showPage->withChildPages(app('veer')->siteId,
                $page->id, $paginator_and_sorting),
            "parentpages" => $this->showPage->withParentPages(app('veer')->siteId,
                $page->id, $paginator_and_sorting),
            "products" => $this->showPage->withProducts(app('veer')->siteId,
                $page->id, $paginator_and_sorting),
            "categories" => $this->showPage->withCategories(app('veer')->siteId,
                $page->id),
            "data" => $this->veer->loadedComponents,
            "template" => $this->template
        );

        $blade_path = $this->template.'.pages.'.$id;

        $viewLink = $this->template.'.page';

        // page with special design
        if ($page->original == 1 && \View::exists($blade_path))
                $viewLink = $blade_path;

        $view = viewx($viewLink, $data);

        $this->view = $view;

        return $view;
    }
}

// TODO: показывать количество комментариев
// TODO: форма для комментирования
// TODO: добавление комментариев в бд (уведомление в браузере об успешном/неуспешном добавлении)

// TODO: разные title в зависимости от того news или all
// TODO: autocollapse & max limit news
// 
// TODO: title, descrption, keywords

// TODO: компоненты - популярные материалы, комментируемые, активные, галерея картинок