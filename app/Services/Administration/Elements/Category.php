<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Category {

    use Helper, Attach, Delete;
    
    protected $action;
    protected $delete;
    protected $new;
    protected $edit;
    protected $data = [];

    public function __construct()
    {
        $this->edit = Input::get('category');
        $this->action = Input::get('action');
        $this->delete = Input::get('deletecategoryid');
        $this->new = Input::get('newcategory');
    }
    
    public function setParams($data)
    {
        $this->data = $data;
        return $this;
    }

    public function add($name, $siteid, $options = [])
    {
        $this->new = $name;      
        return $this->addCategory($name, $siteid, $options);
    }

    public function delete($id)
    {
        $this->action = 'delete';
        return $this->deleteCategory($id);
    }
    
    public function sort($relationship = 'categories')
    {
        $this->action = 'sort';
        $this->data += ['relationship' => $relationship];
        return $this->sortCategory();
    }

    public function edit($id, $action = null)
    {
        $this->edit = $id;
        if(!empty($action)) $this->action = $action;
        return $this->updateOneCategory();
    }

    public function run()
    {
        // delete from parent - subcategories from category; categories from list of all
        if($this->action == 'delete') { 
            $this->deleteCategory($this->delete); 
            event('veer.message.center', trans('veeradmin.category.delete'));
        }
        
        $this->data = Input::all();
        
        if (!empty($this->edit)) {             
            return $this->updateOneCategory(); 
        }
        
        switch ($this->action) {
            case 'add':
                $this->addCategory($this->new, Input::get('siteid', 0)); 
                event('veer.message.center', trans('veeradmin.category.add'));
                if(app('request')->ajax()) return $this->ajaxRequest();
                break;
                
            case 'sort':
                $this->data['relationship'] = "categories";
                $this->sortCategory();
                break;
        }
        $this->action = null;
    }
    
    /**
     * Ajax Request
     * 
     */
    protected function ajaxRequest()
    {
        $items = \Veer\Models\Site::with(['categories' => function($query) {
                    $query->has('parentcategories', '<', 1)->orderBy('manual_sort', 'asc');
                }])->orderBy('manual_sort', 'asc')->where('id', '=', Input::get('siteid', 0))->get();

        /* for admin we always use 'view' instead of 'viewx' */        
        return view(app('veer')->template . '.lists.categories-category', [
            "categories" => $items[0]->categories,
            "siteid" => Input::get('siteid', 0)
        ]);
    }
    
    /**
     * Sort Categories
     * 
     */
    protected function sortCategory()
    {                        
        if(!isset($this->data['parentid'])) { return null; }

        $categoryObj = new \Veer\Services\Show\Category;
       
        $oldsorting = $this->data['relationship'] == 'categories' ? 
            $categoryObj->getAllCategories(array_get($this->data, 'image'), []) : 
            [$categoryObj->getCategoryAdvanced($this->data['parentid'])];
        
        if (is_object($oldsorting) || is_object($oldsorting[0])) {
            foreach($this->sortElements($oldsorting, $this->data) as $sort => $id) {
                \Veer\Models\Category::where('id', '=', $id)->update(['manual_sort' => $sort]);
            }
        }
    }
            
    /**
	 * Add Category
	 *
     */
	protected function addCategory($title, $siteid, $options = [])
	{
        if(empty($title) || empty($siteid)) { return null; }

        $c = new \Veer\Models\Category;
        $c->title = $title;
        $c->description = array_get($options, 'description', '');
        $c->remote_url = array_get($options, 'remote_url', '');
        $c->manual_sort = array_get($options, 'sort', 999999);
        $c->views = array_get($options, 'views', 0);
        $c->sites_id = $siteid;
        $c->save();
        
        return $c->id;		
	}	
	    
    /**
     * Edit/update one category
     * 
     */
	protected function updateOneCategory()
	{	
        $category = \Veer\Models\Category::find($this->edit);
        
        switch ($this->action) {
            case 'deleteCurrent':
                $this->deleteCategory($this->edit);
                Input::replace(['category' => null]);
                app('veeradmin')->skipShow = true;
                event('veer.message.center', trans('veeradmin.category.delete'));
                return \Redirect::route('admin.show', array('categories'));
                
            case 'saveParent': 
                if(!empty($this->data['parentId'])) { 
                    $category->parentcategories()->attach($this->data['parentId']);
                    event('veer.message.center', trans('veeradmin.category.parent.new'));
                }
                break;
                
            case 'updateParent':
                if(!empty($this->data['parentId']) && isset($this->data['lastCategoryId']) && 
                $this->data['lastCategoryId'] != $this->data['parentId']) {  
                    $this->attachParentCategory($this->edit, $this->data['parentId'], $category);   
                    
                }
                break;
                
            case 'removeParent':
                if(isset($this->data['parentId'])) { 
                    $category->parentcategories()->detach($this->data['parentId']);
                    event('veer.message.center', trans('veeradmin.category.parent.detach'));
                }
                break;
                
            case 'updateCurrent':
                $category->title = $this->data['title'];
                $category->remote_url = $this->data['remoteUrl'];
                $category->description = $this->data['description'];
                $category->save();
                event('veer.message.center', trans('veeradmin.category.update'));
                break;
            
            case 'addChild':
                if(isset($this->data['child'])) {
                    $childs = $this->attachElements($this->data['child'], $category, 'subcategories', [
                        "language" => "veeradmin.category.child.attach"
                    ]);

                    if(!$childs) { // add new			
                        $category->subcategories()->attach( 
                            $this->addCategory($this->data['child'], $category->site->id) 
                        );
                        event('veer.message.center', trans('veeradmin.category.child.new'));
                    }
                }
                break;
                
            case 'updateInChild':
                if(!empty($this->data['parentId']) && isset($this->data['lastCategoryId']) && 
                $this->data['lastCategoryId'] != $this->data['parentId']) {
                    $check = \Veer\Models\CategoryPivot::where('child_id','=',$this->data['currentChildId'])
                    ->where('parent_id','=',$this->data['parentId'])->first();	

                    if(!$check) { // update parent in childs
                        $category = \Veer\Models\Category::find($this->data['currentChildId']);
                        $category->parentcategories()->detach($this->data['lastCategoryId']);
                        $category->parentcategories()->attach($this->data['parentId']);
                    }  
                    event('veer.message.center', trans('veeradmin.category.child.parent'));
                }
                break;
                
            case 'removeInChild':
                $category->subcategories()->detach($this->data['currentChildId']);
                event('veer.message.center', trans('veeradmin.category.child.detach'));
                break;
            
            case 'sort':
                $this->data['relationship'] = "subcategories";
                $this->sortCategory();
                break;
            
            case 'updateImages':
                if(Input::hasFile('uploadImage')) {
                    $this->upload('image', 'uploadImage', $this->edit, 'categories', 'ct', [
                        "language" => "veeradmin.category.images.new"
                    ]);
                }  
            case 'updateProducts':
            case 'updatePages':
                $this->attachmentActions($category);
                break;            
        }					
		
		$this->detachmentActions($category);	
		$this->quickProductsActions($this->action);
		$this->quickPagesActions($this->action);        
	}
    
    protected function attachmentActions($category)
    {
        $attachmentActions = [
            'updateImages' => ['attachImages', 'images'],
            'updateProducts' => ['attachProducts', 'products'],
            'updatePages' => ['attachPages', 'pages']
        ];        
        $data = $attachmentActions[$this->action];
        
        $this->attachElements($this->data[$data[0]], $category, $data[1], [
            "language" => "veeradmin.category.".$data[1].".attach"
        ]);
    }
    
    protected function detachmentActions($category)
    {
        $detachmentActions = [
            'removeImage' => ['images', false],
            'removeAllImages' => ['images', true],
            'removeProduct' => ['products', false],
            'removePage' => ['pages', false]
        ];
        
        foreach($detachmentActions as $action => $data) {
            $this->detachElements($this->action, $action, $category, $data[0], [
                "language" => "veeradmin.category.".$data[0].".detach"
            ], $data[1]);            
        }
    }
    
}
