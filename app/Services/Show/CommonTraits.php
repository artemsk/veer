<?php namespace Veer\Services\Show;

trait CommonTraits {
	
	/*
	 * Load Images with Elements
	 */
	public function loadImagesWithElements($items, $skipWith = false)
	{
		return $skipWith === false ? $items->load(array('images' => function($q)
			{
				$q->with('pages', 'products', 'categories', 'users');
			})) 
				: $items->load('images');
	}
	
	
}