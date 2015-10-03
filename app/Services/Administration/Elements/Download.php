<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Download {

    use HelperTrait, AttachTrait, DeleteTrait;
    
    protected $action;
    protected $data = [];
    protected $uploadedIds;
    protected $type = 'download';

    public function __construct()
    {
        $this->action = Input::get('action');
        $this->data = Input::all();
    }
    
    public function run()
    {
        $this->removeFile($this->action);

        if (starts_with($this->action, 'deleteFile')) {
            $r = explode(".", $this->action);
            $this->deleteFile($r[1]);
            event('veer.message.center', trans('veeradmin.file.delete'));
        }

        $this->makeRealLink();
        $this->copyFile();

        if(!empty($this->data['uploadFiles']) && Input::hasFile($this->data['uploadFiles'])) {
            $this->uploadedIds[] = $this->upload('file', 'uploadFiles', null, null, '', null, true);
            event('veer.message.center', trans('veeradmin.file.upload'));
        }

        $this->attachFiles();
    }
    
    protected function makeRealLink()
    {
        if(!starts_with($this->action, 'makeRealLink')) return null;
        
        $this->data += ['times' => 0, 'expiration_day' => null, 'link_name' => null];
        
        $r = explode(".", $this->action);
        $f = \Veer\Models\Download::find($r[1]);
        if (!is_object($f)) return null;
        
        $new = $f->replicate();
        $new->secret = empty($this->data['link_name']) ? str_random(100) . date("Ymd", time()) : $this->data['link_name'];
        
        if($this->data['times'] > 0 || !empty($this->data['expiration_day'])) {
            $new->expires = 1;
            $new->expiration_times = $this->data['times'];
            if(!empty($this->data['expiration_day'])) {
                $new->expiration_day = \Carbon\Carbon::parse($this->data['expiration_day']);
            }
        }

        $new->original = 0;
        $new->save();
        
        event('veer.message.center', trans('veeradmin.file.download'));
    }
    
    protected function copyFile()
    {
        if(!starts_with($this->action, 'copyFile')) return null;

        $this->data += ['prdId' => [], 'pgId' => []];
        
        $r = explode(".", $this->action);
        $prdIds = explode(",", $this->data['prdId']);
        $pgIds = explode(",", $this->data['pgId']);
        $this->prepareCopying($r[1], $prdIds, $pgIds);

        event('veer.message.center', trans('veeradmin.file.copy'));     
    }
    
    protected function attachFiles()
    {
        if (empty($this->data['attachFiles'])) return null;

        $parseTypes = $this->parseForm($this->data['attachFiles']);
        $attach = [];

        if(!empty($parseTypes['target']) && is_array($parseTypes['target'])) {
            foreach($parseTypes['target'] as $t) {
                $t = trim($t);
                if (empty($t) || $t == "NEW") {
                    if (!empty($this->uploadedIds)) $attach = array_merge($attach, $this->uploadedIds);                    
                    continue;
                }
                $attach[] = $t;
            }
        }

        $prdIds = explode(",", array_get($parseTypes, 'elements.0'));
        $pgIds = explode(",", array_get($parseTypes, 'elements.1'));
        foreach ($attach as $f) {
            $this->prepareCopying($f, $prdIds, $pgIds);
        }
        
        event('veer.message.center', trans('veeradmin.file.attach'));
    }
}
