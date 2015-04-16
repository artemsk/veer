<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Image as ShowImage;
use Intervention\Image\ImageManagerStatic as Image;

class ImageController extends Controller {

	protected $showImage;
	
	public function __construct(ShowImage $showImage)
	{
		parent::__construct();
		
		$this->showImage = $showImage;
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($template, $filename)
	{
            if($template != "id") return $this->thumbnails($template, $filename);
            
		$image = $this->showImage->getImageWithSite(app('veer')->siteId, $filename);
			
		if(!is_object($image)) { return Redirect::route('index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting();
		
		$view = viewx($this->template.'.category', array(
			"image" => $image,
			"products" => $this->showImage->withProducts(app('veer')->siteId, $filename, $paginator_and_sorting),
			"pages" => $this->showImage->withPages(app('veer')->siteId, $filename, $paginator_and_sorting),
			"categories" => $this->showImage->withCategories(app('veer')->siteId, $filename),
			"template" => $this->template
		)); 

		$this->view = $view; 

		return $view;
	}

        protected function checkFilename($image_path)
        {
            if (file_exists($image_path) && is_file($image_path)) {
                return true;
            }

            return false;
        }

        protected function checkTemplate($template)
        {
            return in_array($template, array_keys(config('veer.image_templates')));
        }

        protected function thumbnails($template, $filename)
        {
            \Config::set('veer.image_templates.original', null);

            $image_path = public_path().'/'.config('veer.images_path').'/'.str_replace('..', '', $filename);

            if(!$this->checkTemplate($template) || !$this->checkFilename($image_path)) return abort(404);

            $params = config("veer.image_templates.{$template}");

            if (!is_null($params)) {


                //image manipulation based on callback
                $content = Image::cache(function ($image) use ($image_path, $params) {

                    $img = $image->make($image_path);

                    return $img->{array_get($params, 0, 'fit')}(array_get($params, 1), array_get($params,2), function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->encode(null,100);

                }, config('veer.image_lifetime'));


            } else {
                $content = file_get_contents($image_path);
            }

            $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content);

                return response($content, 200, array(
                    'Content-Type' => $mime,
                    'Cache-Control' => 'max-age='.(config('veer.image_lifetime')*60).', public',
                    'Etag' => md5($content)
                ));
        }

}
