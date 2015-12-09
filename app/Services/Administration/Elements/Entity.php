<?php
namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Entity {
    
    protected $id;
    protected $action;
    protected $title;
    protected $data = [];
    protected $type; // entity type: page, product, category
    
    use HelperTrait, AttachTrait, DeleteTrait;
    
    public function __construct()
    {
        \Eloquent::unguard();
        $this->id = Input::get('id');
        $this->action = Input::get('action');
        $this->title = Input::get('title');
        $this->data = Input::all();
    }
    
    public function setParams($data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function setData($fill)
    {
        $this->data['fill'] = $fill;
        return $this;
    }
    
    public function attach($id, $type = 'images')
    {
        $key = ($type == 'tags' || $type == 'attributes') ? $type : ('attach' . ucfirst($type));
        $this->data[$key] = empty($this->data[$key]) ? ':' . $id : $this->data[$key] . ',' . $id ;
        return $this;
    }
    
    public function add($params = null)
    {
        $this->action = 'add';
        if(!empty($this->data['title'])) return $this->updateOne();
    }
    
    public function delete($id)
    {
        $this->action = 'delete';
        $methodName = 'delete' . ucfirst($this->type);
        return $this->{$methodName}($id);
    }
    
    public function update($id, $action = 'update')
    {
        $this->id = $id;
        $this->action = $action;
        return $this->updateOne();
    } 
    
    /* only for product or page */
    public function status($id)
    {
        $this->id = $id;
        $this->action = $this->type == 'page' ? 'changeStatusPage.' . $id : 'updateStatus.' . $id;
        return $this->updateOnePage();
    } 
  
    protected function updateOne()
    {	
        $fill = $this->prepareData(); // ?
        
        if($this->action == 'add' || $this->action == 'saveAs') {
            
            if($this->type == 'page') $fill['hidden'] = true; 
            elseif($this->type == 'product') $fill['status'] = 'hide'; 
            
            $entity = $this->create($fill);
            event('veer.message.center', trans('veeradmin.'. $this->type .'.new'));
        } else {
            $className = '\\Veer\\Models\\' . ucfirst($this->type);
            $entity = $className::find($this->id);
        }
        
		if(!is_object($entity)) return event('veer.message.center', trans('veeradmin.error.model.not.found'));
        
        $this->updateDataOrStatus($entity, $fill);
        $this->attachments($entity);
		$this->freeForm($entity);
		
		if($this->action == 'add' || $this->action == 'saveAs') {
            app('veeradmin')->skipShow = true;
            Input::replace(['id' => $entity->id]);
            return \Redirect::route('admin.show', [str_plural($this->type), 'id' => $entity->id]);
        }
    }
    
    protected function create($fill)
    {
        $className = '\\Veer\\Models\\' . ucfirst($this->type);
        $object = new $className;
        $object->fill($fill);        
        $object->save();
        return $object;
    }
    
    /**
     * Products actions
     * 
     */
    protected function quickProductsActions($action)
    {
        if (starts_with($action, "changeStatusProduct")) {
            $r = explode(".", $action);
            $this->changeProductStatus(\Veer\Models\Product::find($r[1]));
            event('veer.message.center', trans('veeradmin.product.status'));
        }

        if (starts_with($action, "deleteProduct")) {
            $r = explode(".", $action);
            $this->deleteProduct($r[1]);
            event('veer.message.center', trans('veeradmin.product.delete') .
                " " . app('veeradmin')->restore_link('product', $r[1]));
        }

        if (starts_with($action, "showEarlyProduct")) {
            \Eloquent::unguard();
            $r = explode(".", $action);
            \Veer\Models\Product::where('id', '=', $r[1])->update(array("to_show" => now()));
            event('veer.message.center', trans('veeradmin.product.show'));
        }
    }

    /**
     * Pages actions
     * 
     */
    protected function quickPagesActions($action)
    {
        if (starts_with($action, "changeStatusPage")) {
            $r = explode(".", $action);
            $page = \Veer\Models\Page::find($r[1]);
            $page->hidden = $page->hidden == true ? false : true;
            $page->save();
            event('veer.message.center', trans('veeradmin.page.status'));
        }

        if (starts_with($action, "deletePage")) {
            $r = explode(".", $action);
            $this->deletePage($r[1]);
            event('veer.message.center', trans('veeradmin.page.delete') .
                " " . app('veeradmin')->restore_link('page', $r[1]));
        }
    }    
    
    protected function updateDataOrStatus($object, $fill)
    {
        switch($this->action) {
            case 'update':
                $object->fill($fill);
                $object->save();
                event('veer.message.center', trans('veeradmin.'. $this->type . '.update'));
                break;
            
            // page status
            case 'changeStatusPage.' . $object->id:
                $object->hidden = $object->hidden == true ? false : true;
                $object->save();
                event('veer.message.center', trans('veeradmin.'. $this->type . '.status'));
                break;
            
            // product status
            case 'updateStatus.' . $object->id:
                $this->changeProductStatus($object);
                event('veer.message.center', trans('veeradmin.'. $this->type . '.status'));
                break; 
        } 
    }
    
    protected function attachments($object)
    {
        $type = str_plural($this->type);
        $this->data += ['tags' => '', 'attribute' => '', 'attachImages' => '', 
            'attachFiles' => '', 'attachCategories' => '', 'attachPages' => '',
            'attachProducts' => '', 'attachChildPages' => '', 'attachParentPages' => '',
            'attachChildProducts' => '', 'attachParentProducts' => ''];
        
        $params = [
            "actionButton" => $this->action,
            "tags" => $this->data['tags'],
            "attributes" => $this->data['attribute'],
            "attachImages" => $this->data['attachImages'],
            "attachFiles" => $this->data['attachFiles'],
            "attachCategories" => $this->data['attachCategories'],
            "attachChild" . ucfirst($type) => $this->data['attachChild' . ucfirst($type)],
            "attachParent" . ucfirst($type) => $this->data['attachParent' . ucfirst($type)]
        ];
        
        $params += $type == 'pages' ? ["attachProducts" => $this->data['attachProducts']] : 
            ["attachPages" => $this->data['attachPages']];
        
        $prefix = $type == 'pages' ? 'pg' : 'prd';
        
		$this->connections($object, $object->id, $type, $params, [
            "prefix" => ["image" => $prefix, "file" => $prefix]
        ]);
    }    
    
    protected function freeForm($object)
    {
        if(empty($this->data['freeForm'])) { return null; }
        
        preg_match_all("/^(.*)$/m", trim($this->data['freeForm']), $ff); // TODO: test
        if(empty($ff[1]) || !is_array($ff[1])) return null;
        
        foreach($ff[1] as $freeForm) {
            if(starts_with($freeForm, 'Tag:')) {
                $this->attachElements($freeForm, $object, 'tags', null, ",", "Tag:");
            } else {
                $this->attachElements($freeForm, $object, 'attributes', null, ",", "Attribute:");
            }
        } 
    }    
}
