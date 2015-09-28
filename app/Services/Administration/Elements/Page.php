<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Page {
    
    use Helper, Attach, Delete;
    
    protected $id;
    protected $action;
    protected $title;
    protected $data = [];
    
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
    
    public function setPageData($fill)
    {
        $this->data['fill'] = $fill;
        return $this;
    }
    
    public function attach($str, $type = 'images')
    {
        $key = ($type == 'tags' || $type == 'attributes') ? $type : ('attach' . ucfirst($type));
        $this->data[$key] = $str;
        return $this;
    }
    
    public function add()
    {
        $this->action = 'add';
        if(!empty($this->data['title'])) {
            return $this->updateOnePage();
        }
    }
    
    public function update($id)
    {
        $this->id = $id;
        $this->action = 'update';
        return $this->updateOnePage();
    } 
    
    public function delete($id)
    {
        $this->action = 'delete';
        return $this->deletePage($id);
    }
    
    public function status($id)
    {
        $this->id = $id;
        $this->action = 'changeStatusPage.' . $id;
        return $this->updateOnePage();
    }
    
    public function run()
    {
        if(!empty($this->id)) return $this->updateOnePage();   
        
        if(!empty($this->title)) $this->quickAdd();   
        
        $this->quickPagesActions($this->action);  
    }
    
    protected function quickAdd()
    {
        $this->data += ['url' => '', 'txt' => '', 'categories' => null];
        
        // TODO: test preg:
        $txt= preg_replace("/{{(?s).*}}/", "", $this->data['txt'], 1);
        preg_match("/{{(?s).*}}/", $this->data['txt'], $small);
        
        $fill = [
            'title' => trim($this->title),
            'url' => $this->data['url'],
            'hidden' => 1,
            'manual_order' => 99999,
            'users_id' => \Auth::id(),
            'small_txt' => !empty($small[0]) ? substr(trim($small[0]), 2, -2) : '',
            'txt' => trim($txt)
        ];
        
        $page = $this->create($fill);        
        
        $categories = explode(',', $this->data['categories']);
        if(!empty($categories)) {
            $page->categories()->attach($categories);
        }

        // images
        if(Input::hasFile('uploadImage')) {
            $this->upload('image', 'uploadImage', $page->id, 'pages', 'pg', null);
        }

        //files
        if(Input::hasFile('uploadFile')) {
            $this->upload('file', 'uploadFile', $page->id, $page, 'pg', null);
        }
        
        event('veer.message.center', trans('veeradmin.page.new'));
    }
    
    protected function create($fill)
    {
        $page = new \Veer\Models\Page;
        $page->fill($fill);
        $page->save();
        return $page;
    }
    
    protected function updateOnePage()
    {	
        $fill = $this->prepareData();       
        
        if($this->action == 'add' || $this->action == 'saveAs') {
            $fill['hidden'] = true;
            $page = $this->create($fill);
            event('veer.message.center', trans('veeradmin.page.new'));
        } else {
            $page = \Veer\Models\Page::find($this->id);
        }
        
		if(!is_object($page)) return event('veer.message.center', trans('veeradmin.error.model.not.found'));
        
        $this->updateDataOrStatus($page, $fill);
        $this->attachments($page);
		$this->freeForm($page);
		
		if($this->action == 'add' || $this->action == 'saveAs') {
            app('veeradmin')->skipShow = true;
            Input::replace(['id' => $page->id]);
            return \Redirect::route('admin.show', ['pages', 'id' => $page->id]);
        }
    }
    
    protected function prepareData()
    {
        $fill = array_get($this->data, 'fill', []);
        
        foreach(['original', 'show_small', 'show_comments', 'show_title', 'show_date', 'in_list'] as $field) {
            $fill[$field] = isset($fill[$field]) ? 1 : 0; 
        }
        
        $fill['users_id'] = empty($fill['users_id']) ? \Auth::id() : $fill['users_id'];
        $fill['url'] = trim($fill['url']); 
        return $fill;
    }
    
    protected function updateDataOrStatus($page, $fill)
    {
        switch($this->action) {
            case 'update':
                $page->fill($fill);
                $page->save();
                event('veer.message.center', trans('veeradmin.page.update'));
                break;
            case 'changeStatusPage.' . $page->id:
                $page->hidden = $page->hidden == true ? false : true;
                $page->save();
                event('veer.message.center', trans('veeradmin.page.status'));
                break;            
        } 
    }
    
    protected function attachments($page)
    {
        $this->data += ['tags' => '', 'attribute' => '', 'attachImages' => '', 
            'attachFiles' => '', 'attachCategories' => '', 'attachProducts' => '', 
            'attachChildPages' => '', 'attachParentPages' => ''];
        
		$this->connections($page, $page->id, 'pages', [
            "actionButton" => $this->action,
            "tags" => $this->data['tags'],
            "attributes" => $this->data['attribute'],
            "attachImages" => $this->data['attachImages'],
            "attachFiles" => $this->data['attachFiles'],
            "attachCategories" => $this->data['attachCategories'],
            "attachProducts" => $this->data['attachProducts'],
            "attachChildPages" => $this->data['attachChildPages'],
            "attachParentPages" => $this->data['attachParentPages']
            ], ["prefix" => ["image" => "pg", "file" => "pg"]]);
    }
    
    protected function freeForm($page)
    {
        if(empty($this->data['freeForm'])) { return null; }
        
        $ff = preg_split('/[\n\r]+/', trim($this->data['freeForm'])); // TODO: test preg
        foreach($ff as $freeForm) {
            if(starts_with($freeForm, 'Tag:')) {
                $this->attachElements($freeForm, $page, 'tags', null, ",", "Tag:");
            } else {
                $this->attachElements($freeForm, $page, 'attributes', null, ",", "Attribute:");
            }
        } 
    }
}
