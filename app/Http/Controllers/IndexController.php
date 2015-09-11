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
		$view = viewx($this->template.'.home', array(
			"template" => $this->template
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
    
    /**
     * Custom empty route for components & jobs
     * 
     * @param params
     * @return null
     */
    public function custom($params = null)
    {
        // Empty.
    }
        

}
