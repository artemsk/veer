<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;

class IndexController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//Event::fire('veer.message.center', 'Hello!');

		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.home', array(
			"data" => $data,
			"template" => $data['template']
			)); 

		$this->view = $view; // to cache

		return $view;
	}


    /**
	 * Display the 404 page
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function show404()
	{
            // TODO: 404 page
	}
        

}
