<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Category {

    use Helper, Attach;
    
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
        $this->delete = $id;
        return $this->deleteCategory();
    }
    
    public function sort()
    {
        $this->action = 'sort';
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
        if($this->action == 'delete') { $this->deleteCategory(); }
        
        $this->data = Input::all();
        
        if (!empty($this->edit)) {             
            return $this->updateOneCategory(); 
        }
        
        switch ($this->action) {
            case 'add':
                $this->addCategory($this->new, Input::get('siteid', 0));                
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

        return app('view')->make(app('veer')->template . '.lists.categories-category', [
                "categories" => $items[0]->categories,
                "siteid" => Input::get('siteid', 0),
                "child" => view(app('veer')->template . '.elements.asset-delete-categories-script')
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
            $categoryObj->getAllCategories(array_get($this->data, 'image')) : 
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
	 * Delete Category: Category & connections
	 * 
	 */
	protected function deleteCategory()
	{
        if(empty($this->delete)) return null;
        
		\Veer\Models\Category::destroy($this->delete);
		\Veer\Models\CategoryConnect::where('categories_id','=',$this->delete)->forceDelete();
		\Veer\Models\CategoryPivot::where('parent_id','=',$this->delete)->orWhere('child_id','=',$this->delete)->forceDelete();
		\Veer\Models\ImageConnect::where('elements_id','=',$this->delete)
		->where('elements_type','=','Veer\Models\Category')->forceDelete();
		// We do not delete communications for deleted items
	}
    
    /**
     * Edit/update one category
     * 
     */
	public function updateOneCategory()
	{	
        $category = \Veer\Models\Category::find($this->edit);
        
        $messages = [
            'deleteCurrent' => 'veeradmin.category.delete',
            'removeParent' => 'veeradmin.category.parent.detach',
            'updateCurrent' => 'veeradmin.category.update',
            'addChild' => 'veeradmin.category.child.new',
            'updateInChild' => 'veeradmin.category.child.parent',
            'removeInChild' => 'veeradmin.category.child.detach',
        ];
        
        switch ($this->action) {
            case 'deleteCurrent':
                $this->delete = $this->edit;
                $this->deleteCategory();
                Input::replace(['category' => null]);
                app('veeradmin')->skipShow = true;
                return \Redirect::route('admin.show', array('categories'));
                
            case 'saveParent' && isset($this->data['parentId']) && $this->data['parentId'] > 0:
                $category->parentcategories()->attach($this->data['parentId']);			
                break;
                
            case 'updateParent' && isset($this->data['parentId']) && $this->data['parentId'] > 0 && 
                isset($this->data['lastCategoryId']) && $this->data['lastCategoryId'] != $this->data['parentId']:                
                $this->attachParentCategory($this->edit, $this->data['parentId'], $category);                                
                break;
                
            case 'removeParent' && isset($this->data['parentId']):
                $category->parentcategories()->detach($this->data['parentId']);				
                break;
                
            case 'updateCurrent':
                $category->title = $this->data['title'];
                $category->remote_url = $this->data['remoteUrl'];
                $category->description = $this->data['description'];
                $category->save();
                break;
            
            case 'addChild' && isset($this->data['child']):
                $childs = $this->attachElements($this->data['child'], $category, 'subcategories', [
                    "action" => "NEW child categories",
                    "language" => "veeradmin.category.child.attach"
                ]);

                if(!$childs) { // add new			
                    $category->subcategories()->attach( 
                        $this->addCategory($this->data['child'], $category->site->id) 
                    );
                }
                break;
                
            case 'updateInChild' && isset($this->data['parentId']) && $this->data['parentId'] > 0 && 
                isset($this->data['lastCategoryId']) && $this->data['lastCategoryId'] != $this->data['parentId']:
                $check = \Veer\Models\CategoryPivot::where('child_id','=',$this->data['currentChildId'])
                ->where('parent_id','=',$this->data['parentId'])->first();	

                if(!$check) { // update parent in childs
                    $category = \Veer\Models\Category::find($this->data['currentChildId']);
                    $category->parentcategories()->detach($this->data['lastCategoryId']);
                    $category->parentcategories()->attach($this->data['parentId']);
                }                   
                break;
                
            case 'removeInChild':
                $category->subcategories()->detach($this->data['currentChildId']);
                break;
            
            case 'sort':
                $this->data['relationship'] = "subcategories";
                $this->sortCategory();
                break;
            
            case 'updateImages':
                $this->attachElements($this->data['attachImages'], $category, 'images', [
                    "action" => "ATTACH images",
                    "language" => "veeradmin.category.images.attach"
                ]);

                if(Input::hasFile('uploadImage')) {
                    $this->upload('image', 'uploadImage', $this->data['category'], 'categories', 'ct', [
                        "action" => "NEW images",
                        "language" => "veeradmin.category.images.new"
                    ]);
                }
                break;
                
            case 'updateProducts':
                $this->attachElements($this->data['attachProducts'], $category, 'products', [
                    "action" => "ATTACH products",
                    "language" => "veeradmin.category.products.attach"
                ]);
                break;
            
            case 'updatePages':
                $this->attachElements($this->data['attachPages'], $category, 'pages', [
                    "action" => "ATTACH pages",
                    "language" => "veeradmin.category.pages.attach"
                ]);
                break;
            
        }					
		
		$this->detachElements($this->data['action'], 'removeImage', $category, 'images', [
			"action" => "REMOVE images",
			"language" => "veeradmin.category.images.detach"
		]);

        $this->detachElements($this->data['action'], 'removeAllImages', $category, 'images', null, true);
				
		$this->detachElements($this->data['action'], 'removeProduct', $category, 'products', [
			"action" => "REMOVE products",
			"language" => "veeradmin.category.products.detach"
		]);		
		
		$this->quickProductsActions($this->data['action']);	// TODO	

		$this->detachElements($this->data['action'], 'removePage', $category, 'pages', [
			"action" => "REMOVE pages",
			"language" => "veeradmin.category.pages.detach"
		]);				
		
		$this->quickPagesActions($this->data['action']); // TODO
	}
    
}
