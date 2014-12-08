<?php namespace Veer\Lib\Components;

use Veer\Models\Product;
use Veer\Lib\VeerShop;
use Illuminate\Support\Facades\Config;

/**
 * 
 * Veer.Components @indexProducts
 * 
 * - collect product for specific category; 
 *   should be used for index page.
 * 
 * @params db: CATEGORY_HOME
 * @params $siteId
 * 
 * @return $out[]
 */

class indexProducts {   
    
    public $data = array();
    
    function __construct($params = null) {
        
        $v = app('veer');
        
        $siteId = $v->siteId;
        $homeId = db_parameter('CATEGORY_HOME');     
        
        $p = Product::homepages($siteId, $homeId)->checked()->with(
                    array( 'categories' => function($query) use ($siteId, $homeId) {
                            $query->where('sites_id','=',$siteId)->where('categories.id','!=',$homeId);
                        }))->with(array('images' => function($query) {
                            $query->orderBy('id','asc')->take(1);
                        }))->select('id','url','grp','status','title','grp_ids','currency','price','price_sales','price_sales_on',
                                        'price_sales_off','price_opt','price_base','star','download','created_at')->take(1)->get();
                                      
        $this->data = $this->prepareOutput($p);       
        
    }  
    
    /*
     * Preparing Output
     * 
     */
    protected function prepareOutput($p) 
    {       
        $key = 0; 
        $out = array();        
		$images_path = config('veer.images_path');

		for($j=0;$j<=15;$j++) {
        foreach($p as $product)
        {
            $img = $product->images->toArray();
            $cats = $product->categories->toArray();            
            $out[$key]['img'] = asset($images_path . "/" . $img[0]['img']);
            $out[$key]['title'] = $product->title; 
            $out[$key]['link'] =  route('product.show', $product->id);
            $out[$key]['category'] = $cats[0]['title'];
            $out[$key]['category_link'] = route('category.show', $cats[0]['id']);                  
            $out[$key]['price'] = app('veershop')->getPrice($product);
            $out[$key]['basket'] = view(app('veer')->template . ".elements.shopping-cart-button")
				->with('link', route('user.basket.add',array('id' => $product->id)));      
            $key++;
		}}
        
        return array('products' => $out);         
    }
    
    
}


// TODO: star
// TODO: group
// TODO: basket make - depending on avail.
