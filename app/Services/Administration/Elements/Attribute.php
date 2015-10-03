<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Attribute {

    use DeleteTrait, HelperTrait, AttachTrait;
    
    protected $action;
    protected $newValue;
    protected $newName;
    protected $rename;
    protected $data = [];
    protected $type = 'attribute';
    
    public function __construct()
    {
        \Eloquent::unguard();
        $this->action = Input::get('action');
        $this->newValue = Input::get('newValue');
        $this->newName = Input::get('newName');
        $this->rename = Input::get('renameAttrName');
        $this->data = Input::all();
    }
    
    public function run()
    {
        if(starts_with($this->action, "deleteAttrValue")) {
            list(, $id) = explode(".", $this->action);
            event('veer.message.center', trans('veeradmin.attribute.delete'));
            return $this->deleteAttribute($id);
        } 
        
        if($this->action == "newAttribute") return $this->newAttribute();            
        
        if(!empty($this->rename)) $this->renameAttribute();

        $this->updateAttribute();

        event('veer.message.center', trans('veeradmin.attribute.update'));
    }
    
    protected function newAttribute()
    {
        $manyValues = preg_split('/[\n\r]+/', trim($this->newValue));
        
        foreach ($manyValues as $value) {
            $this->attachToAttributes($this->newName, $value);
        }
        
        event('veer.message.center', trans('veeradmin.attribute.new'));
    }
    
    protected function renameAttribute()
    {
        foreach($this->rename as $k => $v) {            
            if ($k != $v) \Veer\Models\Attribute::where('name', '=', $k)->update(['name' => $v]);
        }
    }
    
    protected function updateAttribute()
    {            
        $this->data += ['renameAttrValue' => [], 'descrAttrValue' => [], 'attrType' => [], 'newAttrValue' => []];    
            
        foreach($this->data['renameAttrValue'] as $k => $v) {
            
            \Veer\Models\Attribute::where('id', '=', $k)->update([
                'val' => $v,
                'descr' => array_get($this->data['attrDescr'], $k, ''),
                'type' => array_get($this->data['attrType'], $k) == 1 ? 'descr' : 'choose'
            ]);
        }

        foreach($this->data['newAttrValue'] as $k => $v) { 
            $this->attachToAttributes($k, $v);
        }
    }
}

