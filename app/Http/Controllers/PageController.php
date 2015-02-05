<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Page as ShowPage;

class PageController extends Controller {

	protected $showPage;
	
	public function __construct(ShowPage $showPage)
	{
		parent::__construct();
		
		$this->showPage = $showPage;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$pages = $this->showPage->getPagesWithSite(app('veer')->siteId);  
				
		if(!is_object($pages)) { return Redirect::route('index'); }
		
		$pages->load('categories', 'user');
		
		$view = view($this->template.'.pages', array(
			"pages" => $pages,
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		)); 

		$this->view = $view; 

		// TODO: number of comments?

		return $view;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{		
		// if exist external content then send it to browser, increment views
		$external_content = $this->showPage->checkAndGetContentFromHtmlFile(app('veer')->siteId, $id);

        if (!empty($external_content)) 
		{    
			\Veer\Models\Page::where('id','=',$id)->increment('views');

            return \Response::make($external_content, 200)->header('Content-type','text/html');
        }
		
		$page = $this->showPage->getPage($id);                 

		if(!is_object($page)) { return Redirect::route('page.index'); }
		
		$page->increment('views');	

		$page->load('images', 'tags', 'attributes', 'downloads', 'userlists', 'user');
		
		if($page->show_comments == 1) { 
			if(db_parameter('COMMENTS_SYSTEM') == "disqus") { 
				$this->veer->loadedComponents['comments_disqus'] = view('components.disqus', array("identifier" => "page".$page->id));
			} else {
				$page->load('comments');
				$this->veer->loadedComponents['comments_own'] = $page->comments->toArray();
			}
		}	

		$paginator_and_sorting = get_paginator_and_sorting();
		
		$data = array(
			"page" => $page,
			"subpages" => $this->showPage->withChildPages(app('veer')->siteId, $id, $paginator_and_sorting),
			"parentpages" => $this->showPage->withParentPages(app('veer')->siteId, $id, $paginator_and_sorting),
			"products" => $this->showPage->withProducts(app('veer')->siteId, $id, $paginator_and_sorting),
			"categories" => $this->showPage->withCategories(app('veer')->siteId, $id),
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		); 
					
		$blade_path = app_path() . '/views/' . $this->template. '/pages/' . $id . '.blade.php';
		
		if($page->original == 1 && \File::exists( $blade_path )) { // page with special design           
			$view = view($this->template.'.pages.'.$id, $data);
		} else {
			$view = view($this->template.'.page', $data);
		} 

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