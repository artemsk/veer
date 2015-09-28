<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Product {
    
    use Helper, Delete, Attach;
    
    protected $id;
    protected $action;
    protected $title;
    protected $data = [];
    
    public function __construct()
    {
        \Eloquent::unguard();
        $this->id = Input::get('id');
        $this->action = Input::get('action');
        $this->title = Input::get('fill.title');
        $this->data = Input::all();
    }
    
    /*
    public function setParams($data);
    
    public function setProductData($fill);
    
    public function attach($str, $type = 'images');

    public function add();
    
    public function update($id);
    
    public function delete($id);
    
    public function status($id);
    */
    
    public function run()
    {
        if(!empty($this->id)) return $this->updateOnePage();   
        
        $this->quickProductsActions($this->action);  
        
        if(!empty($this->data['freeFrom'])) return $this->quickFreeForm();
        if(!empty($this->title)) return $this->quickAdd();
    }
    
    protected function create($fill)
    {
        $product = new \Veer\Models\Product;
        $product->fill($fill);
        $product->save();
        return $product;
    }
    
    protected function quickAdd()
    {
        $this->data += ['prices' => '', 'options' => '', 'categories' => ''];        
        $prices = explode(":", $this->data['prices']);
        $options = explode(":", $this->data['options']);
        
        $fill = [
            'title' => trim($this->title),
            'url' => !empty($this->data['fill']['url']) ? trim($this->data['fill']['url']) : '',            
        ];

        foreach(['price', 'price_sales', 'price_opt', 'price_base', 'currency'] as $i => $type) {
            $fill[$type] = array_get($prices, $i, 0);
        }

        foreach(['qty', 'weight', 'score', 'star'] as $i => $type) {
            $fill[$type] = array_get($options, $i, 0);
        }
        
        $fill['production_code'] = array_get($options, 4, '');
        $fill['status'] = 'hide';
        
        $product = $this->create($fill);

        $categories =  explode(",", $this->data['categories']);
        if(!empty($categories)) $product->categories()->attach($categories);

        // images
        if(Input::hasFile('uploadImage')) {
            $this->upload('image', 'uploadImage', $product->id, 'products', 'prd', null);
        }

        //files
        if(Input::hasFile('uploadFile')) {
            $this->upload('file', 'uploadFile', $product->id, $product, 'prd', null);
        }		

        event('veer.message.center', trans('veeradmin.product.new'));
    }
    
    protected function quickFreeForm()
    {
        $parseff = preg_split('/[\n\r]+/', trim($this->data['freeForm']));
        
        foreach($parseff as $p) {
            $fields = explode("|", $p);

            $fill = [];
            foreach(['title', 'url', 'categories', 'qty', 'weight', 'currency', 'price',
                'price_sales', 'price_opt', 'price_base', 'price_sales_on', 'price_sales_off',
                'to_show', 'score', 'star', 'image', 'file', 'production_code', 'status', 'descr'] as $i => $type) {
                
                switch($type) {
                    case 'categories':
                        $categories = explode(",", array_get($fields, $i, ''));
                        break;
                    case 'image':
                    case 'file':
                        ${$type} = array_get($fields, $i);
                        break;
                    case 'descr':
                        $fill[$type] = substr(array_get($fields, $i, ''), 2, -2);
                        break;
                    case 'status':
                        $fill[$type] = array_get($fields, $i, 'hide');
                        break;
                    default:
                        $fill[$type] = array_get($fields, $i, 0);
                        break;
                }   
            }
            
            $product = $this->create($fill);
           
            if(!empty($categories)) $product->categories()->attach($categories);
            if(!empty($image)) $this->addImage($image, $product);
            if(!empty($file)) $this->addFile($file, $product);              		
        }	
        
        event('veer.message.center', trans('veeradmin.product.new'));
    }
    
    protected function addImage($image, $product)
    {
        $new = new \Veer\Models\Image; 
        $new->img = $image;
        $new->save();
        $new->products()->attach($product->id);
    }
    
    protected function addFile($file, $product)
    {
        $new = new \Veer\Models\Download; 
        $new->original = 1;
        $new->fname= $file;
        $new->expires = 0;
        $new->expiration_day = 0;
        $new->expiration_times = 0;
        $new->downloads = 0;
        $product->downloads()->save($new); 
    }
    
    protected function updateOnePage()
    {	
        $fill = $this->prepareData();
        
		if($this->action == "add" || $this->action == "saveAs") {			
            $fill['status'] = 'hide';            
			$product = $this->create($fill);
			event('veer.message.center', trans('veeradmin.product.new'));			
		} else {
			$product = \Veer\Models\Product::find($this->id);
		}
        
        if(!is_object($product)) return event('veer.message.center', trans('veeradmin.error.model.not.found'));
	
        $this->updateDataOrStatus($product, $fill);
        $this->attachments($product);
		$this->freeForm($product);
        		
		if($this->action == "add" || $this->action == "saveAs") {
			app('veeradmin')->skipShow = true;
			Input::replace(array('id' => $product->id));
			return \Redirect::route('admin.show', ['products', 'id' => $product->id]);
		}
    }
    
    protected function prepareData()
    {
        $fill = array_get($this->data, 'fill', []);

        $fill['star'] = isset($fill['star']) ? 1 : 0;
        $fill['download'] = isset($fill['download']) ? 1 : 0;
        $fill['url'] = trim($fill['url']);
        $fill['price_sales_on'] = parse_form_date(array_get($fill, 'price_sales_on', 0));
        $fill['price_sales_off'] = parse_form_date(array_get($fill, 'price_sales_off', 0));

        $toShow = parse_form_date(array_get($fill, 'to_show', 0));
        $toShow->hour((int) array_get($this->data, 'to_show_hour', 0));
        $toShow->minute((int) array_get($this->data, 'to_show_minute', 0));

        $fill['to_show'] = $toShow;
        return $fill;
    }

    protected function updateDataOrStatus($product, $fill)
    {
        switch($this->action) {
            case 'update':
                $product->fill($fill);
                $product->save();
                event('veer.message.center', trans('veeradmin.product.update'));
                break;
            case 'updateStatus.' . $product->id:
                $this->changeProductStatus($product);
                event('veer.message.center', trans('veeradmin.product.status'));
                break;            
        } 
    }
    
    protected function attachments($product)
    {
        $this->data += ['tags' => '', 'attribute' => '', 'attachImages' => '', 
            'attachFiles' => '', 'attachCategories' => '', 'attachPages' => '', 
            'attachChildProducts' => '', 'attachParentProducts' => ''];
        
		$this->connections($product, $product->id, 'products', [
            "actionButton" => $this->action,
            "tags" => $this->data['tags'],
            "attributes" => $this->data['attribute'],
            "attachImages" => $this->data['attachImages'],
            "attachFiles" => $this->data['attachFiles'],
            "attachCategories" => $this->data['attachCategories'],
            "attachPages" => $this->data['attachPages'],
            "attachChildProducts" => $this->data['attachChildProducts'],
            "attachParentProducts" => $this->data['attachParentProducts']
            ], ["prefix" => ["image" => "prd", "file" => "prd"]]);
    }
    
    protected function freeForm($product)
    {
        if(empty($this->data['freeForm'])) { return null; }
        
        $ff = preg_split('/[\n\r]+/', trim($this->data['freeForm'])); // TODO: test preg
        foreach($ff as $freeForm) {
            if(starts_with($freeForm, 'Tag:')) {
                $this->attachElements($freeForm, $product, 'tags', null, ",", "Tag:");
            } else {
                $this->attachElements($freeForm, $product, 'attributes', null, ",", "Attribute:");
            }
        } 
    }
}