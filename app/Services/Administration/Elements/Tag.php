<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Tag {
    
    use DeleteTrait, AttachTrait, HelperTrait;
    
    protected $action;
    protected $rename;
    protected $new;
    protected $type = 'tag';
    
    public function __construct()
    {   
        \Eloquent::unguard();
        $this->action = Input::get('action');
        $this->rename = Input::get('renameTag');
        $this->new = Input::get('newTag'); 
    }
    
    public function run()
    {
        if (starts_with($this->action, "deleteTag")) {
            $r = explode(".", $this->action);
            event('veer.message.center', trans('veeradmin.tag.delete'));
            return $this->deleteTag($r[1]);
        }

        $this->renameTags();
        $this->newTags();
        event('veer.message.center', trans('veeradmin.tag.update'));
    }

    protected function renameTags()
    {
		if(!is_array($this->rename)) return null;
        
        foreach($this->rename as $key => $value) { 
            $value = trim($value);
            $tagDb = \Veer\Models\Tag::where('name', '=', $value)->first();            
            if(!is_object($tagDb)) \Veer\Models\Tag::where('id', '=', $key)->update(['name' => $value]);
        }
    }
    
    protected function newTags()
    {
        $new = $this->parseForm($this->new);

        if(!is_array($new['target'])) return null;
        
        foreach($new['target'] as $tag) {
            $tag = trim($tag);
            if(empty($tag)) { continue; }
            $tagDb = \Veer\Models\Tag::firstOrNew(['name' => $tag]);
            $tagDb->save();
            $tags[] = $tagDb->id;
        }
        
        if(isset($tags)) {
            $this->attachFromForm($new['elements'], $tags, 'tags');
        }		
    }
}
