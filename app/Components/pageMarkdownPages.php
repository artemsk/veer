<?php

namespace Veer\Components;

class pageMarkdownPages
{
    protected $page;

    protected $sourcePath = "docs"; // {theme}/docs

    public $dataPath;

    public $data;

    protected $pageDb;

    public function __construct()
    {
        $checkLocale = new \Veer\Components\commonChangeLocale();
        
        app('view')->addExtension('md', 'php');

        $this->page = app('router')->current()->id;

        $this->sourcePath = app('veer')->template.'/'.$this->sourcePath.'/'.config('app.locale').'/'.$this->page.'';

        if(view()->exists($this->sourcePath)) {
            $this->dataPath = app('view')->getFinder()->find($this->sourcePath);
        }

        if(!empty($this->dataPath)) $this->data = \File::get($this->dataPath);

        $this->updateViews();

        if (app('request')->ajax()) return $this->earlyResponse($this->data);


        return $this->earlyResponse(
            viewx(app('veer')->template.'.page',
                        array(
                        "page" => $this->pageDb,
                        "template" => app('veer')->template
            ))
        );
    }

    protected function earlyResponse($data)
    {
        app('veer')->forceEarlyResponse = true;

        app('veer')->earlyResponseContainer = $data;
    }

    protected function updateViews()
    {
        $this->pageDb = (new \Veer\Services\Show\Page)->getPage($this->page, app('veer')->siteId);

        if(is_object($this->pageDb)) {
            $this->pageDb->increment('views');
           
        }
        
    }
}