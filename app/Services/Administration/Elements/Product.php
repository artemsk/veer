<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Product extends Entity {
        
    protected $title = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->title = Input::get('fill.title');
        $this->type = 'product';
    }
    
    public function run()
    {
        if(!empty($this->id)) return $this->updateOne();   
        
        $this->quickProductsActions($this->action);  
        
        if(!empty($this->data['freeFrom'])) return $this->quickFreeForm();
        if(!empty($this->title)) return $this->quickAdd();
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
        preg_match_all("/^(.*)$/m", trim($this->data['freeForm']), $parseff); // TODO: test
        if(empty($parseff[1]) || !is_array($parseff[1])) return null;

        foreach($parseff[1] as $p) {
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
            
            $product = $this->create($fill, 'product');
           
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
}
