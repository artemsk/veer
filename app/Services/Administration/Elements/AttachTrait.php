<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

trait AttachTrait {

    /**
     * Connections
     * 
     */
    protected function connections($object, $id, $type, $attributes = [], $options = [])
    {
        $action = array_get($attributes, 'actionButton');
        
        if(isset($attributes['tags'])) { $this->attachTags(array_get($attributes, 'tags'), $object); }
        if(isset($attributes['attributes'])) { $this->attachAttributes(array_get($attributes, 'attributes'), $object); }
        $this->checkImagesFiles($action, $attributes, $id, $object, $type, $options);
                
        $relations = [
            'images' => ['attachImages', 'removeImage', array_get($options, 'message.images')],
            'categories' => ['attachCategories', 'removeCategory', null, true],
            'pages' => ['attachPages', 'removePage', null],
            'products' => ['attachProducts', 'removeProduct', null],
            'subproducts' => ['attachChildProducts', 'removeChildProduct', null],
            'parentproducts' => ['attachParentProducts', 'removeParentProduct', null],
            'subpages' => ['attachChildPages', 'removeChildPage', null],
            'parentpages' => ['attachParentPages', 'removeParentPage', null]
        ];
        
        foreach($relations as $relation => $fields) {
            if(!isset($fields[3]) || isset($attributes[$fields[0]])) {
                $this->attachElements(array_get($attributes, $fields[0]), $object, $relation, null);
            }            
            $this->detachElements($action, array_get($attributes, $fields[1].'Id', $fields[1]), $object, $relation, $fields[2]);
        }        
        
        $this->detachElements($action, 'removeAllImages', $object, 'images', array_get($options, 'message.images'), true);
    }
    
    /**
     * Check Images and Files
     * 
     */
    protected function checkImagesFiles($action, $attributes, $id, $object, $type, $options)
    {
        if(Input::hasFile(array_get($attributes, 'uploadImageId', 'uploadImage'))) {
            $this->upload('image', array_get($attributes, 'uploadImageId', 'uploadImage'), 
                $id, $type, array_get($options, 'prefix.image'), null);
        }
        
        if(Input::hasFile(array_get($attributes, 'uploadFilesId', 'uploadFiles'))) {
            $this->upload('file', array_get($attributes, 'uploadFilesId', 'uploadFiles'), 
                $id, $object, array_get($options, 'prefix.file'), null);
        }
        
        $this->copyFiles(array_get($attributes, 'attachFiles'), $object);
        $this->removeFile($action);
    }
    
    /**
     * Attach Elements
     * 
     */
    protected function attachElements($ids, $object, $relation, $message = [], $separator = ",", $start = ":", $replace = false)
    {
        $elements = !is_array($ids) ? $this->parseIds($ids, $separator, $start) : $ids;

        if(is_array($elements)) {         
            $method = $replace == true ? 'sync' : 'attach';
            
            $object->{$relation}()->{$method}($elements);

            if(!empty($message)) { event('veer.message.center', trans(array_get($message, 'language', 'veeradmin.empty'))); }
            return true;
        }
    }

    /**
     * Detach Elements
     * TODO: based on faulty html inputs - blablabla.0 <- redo
     */
    protected function detachElements($detachString, $type, $object, $relation, $message = [], $allowEmpty = false)
    {
        if(!starts_with($detachString, $type)) { return null; }

        $r = explode(".", $detachString);

        if(!empty($r[1])) { $detach = $object->{$relation}()->detach($r[1]); }

        if($allowEmpty === true) { $detach = $object->{$relation}()->detach(); }

        if(!empty($message) && !empty($detach)) {
            event('veer.message.center', trans(array_get($message, 'language', 'veeradmin.empty')));
        }        
    }

    /**
     * Attach Attributes
     * 
     */
    protected function attachAttributes($attributes, $object)
    {
        if(!is_array($attributes)) { return null; }

        \Eloquent::unguard();

        $attrArr = [];
        foreach($attributes as $a) {
            $a += ['name' => null, 'val' => '', 'type' => 'descr'];            
            if(empty($a['name'])) { continue; }

            $attr = \Veer\Models\Attribute::firstOrNew([
                "name" => $a['name'],
                "val" => $a['val'],
                "type" => $a['type']
            ]);

            if(!$attr->exists) {
                $attr->name = $a['name'];
                $attr->val = $a['val'];
                $attr->type = $a['type'];
                $attr->descr = array_get($a, 'descr', '');
                $attr->save();
            }
            
            $attrArr[$attr->id] = ["product_new_price" => array_get($a, 'price', '')];
        }

        $this->attachElements($attrArr, $object, 'attributes', null, ",", ":", true);
    }

    /**
     * Attach Tags
     * 
     */
    protected function attachTags($tags, $object)
    {
        \Eloquent::unguard();
        $tagArr = [];
        preg_match_all("/^(.*)$/m", trim($tags), $matches); 

        if(!empty($matches[1]) && is_array($matches[1])) {         
            foreach($matches[1] as $tag) {
                $tag = trim($tag);
                if(empty($tag)) { continue; }

                $tagDb = \Veer\Models\Tag::firstOrNew(['name' => $tag]);
                if(!$tagDb->exists) {
                    $tagDb->name = $tag;
                    $tagDb->save();
                }
                $tagArr[] = $tagDb->id;
            }
        }
        $this->attachElements($tagArr, $object, 'tags', null, ",", ":", true);
    }

    /**
     * Update Attributes Connections
     * TODO: Test
     */
    protected function attachToAttributes($name, $form)
    {
        $new = $this->parseForm($form);
        if(!is_array($new['target'])) { return null; }
        
        foreach($new['target'] as $a) {
            $a = trim($a);            
            if(empty($a)) { continue; }
            
            if(starts_with($a, ":")) {
                // id
                $aDb = \Veer\Models\Attribute::find(substr($a, 1));
                if(!is_object($aDb)) { continue; }
            } else {
                // string values
                $aDb = \Veer\Models\Attribute::firstOrNew([
                    'name' => $name,
                    'val' => $a,
                    'type' => '?'
                ]);                
                $aDb->save();
            }            
            
            $attributes[] = $aDb->id;
        }
        
        if(isset($attributes)) { $this->attachFromForm($new['elements'], $attributes, 'attributes'); }
    }

    /**
     * Parsing free form for tag|image connections
     * TODO: Test
     * Ex.: values, values, values [:id,id:id,id:id,id]
     */
    protected function parseForm($textarea)
    {
        $small = '';
        preg_match("/\[(?s).*\]/", $textarea, $small);
        $parseTypes = explode(":", substr(array_get($small, 0, ''), 2, -1));
        $parseAttach = explode("[", $textarea);
        $attach = explode(",", trim(array_get($parseAttach, 0)));

        return ['target' => $attach, 'elements' => $parseTypes];
    }
    
    /**
     * Attach Based on Form Input
     * TODO: Test
     * [id,id,id] [id,id,id] [id,id,id] 
     */
    protected function attachFromForm($str, $attach, $type)
    {
        $models = ['Product', 'Page', 'Category', 'User'];
        
        foreach(is_array($str) ? $str : [] as $k => $v) {
            if($k > 3) continue;
            $p = explode(",", $v);
            
            foreach($p as $id) {
                $class = "\\Veer\\Models\\".$models[$k];
                $object = $class::find($id);
                                
                if(is_object($object)) { $this->attachElements($attach, $object, $type, null); }
            }
        }
    }

    /**
     * Attach Parent Category
     * 
     */
    protected function attachParentCategory($cid, $parent_id, $category)
    {
        $check = \Veer\Models\CategoryPivot::where('child_id', '=', $cid)
                ->where('parent_id', '=', $parent_id)->first();

        if(!$check) {
            $category->parentcategories()->attach($parent_id);
            event('veer.message.center', trans('veeradmin.category.parent.new'));
        }
    }
    
    /*
    abstract public function upload($type, $files, $id, $relationOrObject, $prefix = null, $message = null, $skipRelation = false);
    
    abstract protected function copyFiles($files, $object);
    
    abstract protected function removeFile($removeFile);
    
    abstract protected function parseIds($ids, $separator = ",", $start = ":");
     */
}
