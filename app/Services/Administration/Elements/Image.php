<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Image {
    
    use HelperTrait, AttachTrait, DeleteTrait;
    
    protected $action;
    protected $data = [];
    protected $attach;
    protected $uploadedIds = [];
    protected $type = 'image';    
    
    public function __construct()
    {
        $this->data = Input::all();
        $this->attach = Input::get('attachImages');
        $this->action = Input::get('action');
    }
    
    public function run()
    {
        foreach(is_array($this->data) ? $this->data : [] as $k => $v) {
            if (Input::hasFile($k)) {
                $this->uploadedIds[] = $this->upload('image', $k, null, null, '', null, true);
                event('veer.message.center', trans('veeradmin.image.upload'));
            }
        }

        $this->attachImages();
        
        if (starts_with($this->action, 'deleteImage')) {
            $r = explode(".", $this->action);
            $this->deleteImage($r[1]);
            event('veer.message.center', trans('veeradmin.image.delete'));
        }    
    }
    
    protected function attachImages()
    {
        if (empty($this->attach)) return null;
        
        preg_match("/\[(?s).*\]/", $this->attach, $small);
        $parseTypes = explode(":", substr(array_get($small, 0, ''), 2, -1));

        if (starts_with($this->attach, 'NEW')) {
            $attach = empty($this->uploadedIds) ? null : $this->uploadedIds;
        } else {
            $parseAttach = explode("[", $this->attach); // TODO: test
            $attach = explode(",", array_get($parseAttach, 0));
        }

        $this->attachFromForm($parseTypes, $attach, 'images');
        event('veer.message.center', trans('veeradmin.image.attach'));       
    }    
}
