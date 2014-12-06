<?php

class PageController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// TODO: queryParams -> sort, filter
		
		$pages= app('veerdb')->route();   
				
		if(!is_object($pages)) { return Redirect::route('index'); }
		
		$pages->load('categories');
		
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.pages', array(
			"pages" => $pages,
			"data" => $data,
			"template" => $data['template']
		)); 

		$this->view = $view; 

		// TODO: number of comments?

		return $view;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{		
		// 1 file check   
        $p_html = config('veer.htmlpagespath') . '/' . $id . '.html';
        if (File::exists( $p_html )) {
            
            $contents = File::get( $p_html );
            $response = Response::make($contents, 200);
            $response->header('Content-type','text/html');
            return $response;
        }
		
		// 2 db		
		$vdb = app('veerdb');

		$page = $vdb->route($id);                 

		if(!is_object($page)) { return Redirect::route('page.index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting();
		
			$sub = $vdb->pageOnlySubPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$parent = $vdb->pageOnlyParentPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$categories = $vdb->pageOnlyCategoriesQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$products= $vdb->pageOnlyProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

		$page->increment('views');	

		$page->load('images', 'tags', 'attributes', 'downloads', 'userlists');
		
		// 3 blade
		$blade_path = app_path() . '/views/template/' . $this->template. '/pages/' . $id . '.blade.php';
		
		// TODO: comments system
		
		/*if($page->show_comments == 1) { 
			if($comments_system == "disqus") { 

				$data_comments['disqus_shortname'] = array_get(Config::get('veer.site_config'),'COMMENTS_DISQUS_ID'); 
				$data_comments['disqus_identifier'] = '_page_' . $pid;
				$data_comments['disqus_title'] = $p->title;
				$data_comments['disqus_url'] = URL::full();                    
				$path_to_comments_template = 'disqus';
			} else {

				$p->load('comments');

				$data_comments = $p->comments->toArray();
			}
		}*/	
		
		$data = array(
			"page" => $page,
			"subpages" => $sub,
			"parentpages" => $parent,
			"products" => $products,
			"categories" => $categories,
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		); 
					
		if($page->original == 1 && File::exists( $blade_path )) { // page with special design           
			$view = view($this->template.'.pages.'.$id, $data);
		} else {
			$view = view($this->template.'.page', $data);
		} 

		$this->view = $view; 

		return $view;			   
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
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