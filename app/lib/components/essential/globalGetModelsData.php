<?php
namespace Veer\Lib\Components;

use Veer\Models\Category;
use Veer\Models\Tag;
use Veer\Models\Attribute;
use Veer\Models\Image;
use Veer\Models\Product;
use Veer\Models\Page;
use Veer\Models\Order;
use Veer\Models\User;

/**
 * 
 * Veer.Components @globalGetModelsData !essential
 * 
 * - collect product & pages for category/tag/attribute/search etc. 
 *   is used everywhere: tag, category, attribute, image, index?, product,
 *   page, search, filter, order, user, array(id), (all)array(id)
 * 
 * @params $group_img_flag = "0", $onlynums_flag = "0"
 * @params $method other: new, popular order, popular viewed
 * 
 * @return 
 */

class globalGetModelsData {   
    
    protected $v;
    
    function __construct($params = null) {

        $this->v = \App::make('veer');
        
        $siteId = $this->v->siteId;
        $queryParams = $this->paramsDefault($params['params']);
        
        $p = null;
        
        switch($params['method']) {
            
            case "category.show":  
                $p = $this->categoryQuery($siteId, $params['id'], $queryParams);
                break;
            
            case "tag.show":  
                $p = $this->tagQuery($siteId, $params['id'], $queryParams);
                break;   

            case "attribute.show":  
                $p = $this->attributeQuery($siteId, explode('.',$params['id']), $queryParams);
                break;    
            
            case "image.show":  
                $p = $this->imageQuery($siteId, $params['id'], $queryParams);
                break;     
            
            case "index":  
                $p = $this->indexQuery($siteId, $params['id'], $queryParams);
                break;   
            
            case "product.show":  
                $p = $this->productQuery($siteId, $params['id'], $queryParams);
                break;  
            
            case "page.show":  
                $p = $this->pageQuery($siteId, $params['id'], $queryParams);
                break;
            
            case "search.store":  
                $p = $this->searchQuery($siteId, $params['id'], $queryParams);
                break;                
            
            case "filter.show":  
                $p = $this->filterQuery($siteId, explode('.',$params['id']), $queryParams);
                break;    
            
            case "order.show":  
                $p = $this->orderQuery($siteId, $params['id'], $queryParams);
                break;      
            
            case "user.show":  
                $p = $this->userQuery($siteId, $params['id'], $queryParams);
                break;       
            
            case "connected":  
                $queryParams['connectedQuery'] = 'connected';
                $p = $this->connectedQuery($siteId, $params['id'], $queryParams);
                break;  
            
            case "connectedEverywhere":  
                $queryParams['connectedQuery'] = 'connectedEverywhere';
                $p = $this->connectedQuery($siteId, $params['id'], $queryParams);
                break;          
            
            case "new":  
                $queryParams['sortingQuery'] = 'new';
                $p = $this->sortingQuery($siteId, $queryParams);
                break;     

            case "popularByOrder": 
                $queryParams['sortingQuery'] = 'popular_ordered';
                $p = $this->sortingQuery($siteId, $queryParams);
                break;  

            case "popularByView": 
                $queryParams['sortingQuery'] = 'popular_viewed';
                $p = $this->sortingQuery($siteId, $queryParams);
                break;         
            
            default:
                break;      
        }
        
        return $p;   
    }
    
    /**
     * Default Params
     * 
     */
    protected function paramsDefault($params = array()) 
    {    
        $defaultParams = array(
            "sort" => "updated_at",
            "direction" => "desc",
            "skip" => 0,
            "take" => 999,
            "skip_pages" => 0,
            "take_pages" => 15,
            "search_field_product" => "title",
            "search_field_page" => "title"
        );
                
        foreach($defaultParams as $k => $v) {
            if( !isset($params[$k]) ) { $params[$k] = $v; }
        }
        
        return $params;
    }    
    
    /**
     * Query Builder: Category
     * 
     * @not: images, tags, attributes, comments
     */
    protected function categoryQuery($siteId, $id, $queryParams)
    {
        return Category::where('sites_id','=',$siteId)->where('id','=',$id)->
                  with( array (
                      'products' => function($query) use ($queryParams) {
                            $query->checked()
                                    ->orderBy($queryParams['sort'], $queryParams['direction'])
                                    ->take($queryParams['take'])->skip($queryParams['skip']);
                      },
                      'pages' => function($query) use ($queryParams) {
                            $query->excludeHidden()->orderBy('created_at','desc')
                              ->take($queryParams['take_pages'])->skip($queryParams['skip_pages']); 
                      },  
                      'subcategories' => function($query) use ($siteId) {
                        $query->where('sites_id','=',$siteId);
                      },
                      'parentcategories' => function($query) use ($siteId) {
                        $query->where('sites_id','=',$siteId);
                      }
                  ))->first();         
    }    
    
    /**
     * Query Builder: Tag
     * 
     * @not: images, comments
     */
    protected function tagQuery($siteId, $id, $queryParams)
    {
        return Tag::where('id','=',$id)->with(
                array( 'pages' => function($query) use ($siteId, $queryParams) {
                        $query->siteValidation($siteId)->excludeHidden()->orderBy('created_at','desc')
                              ->take($queryParams['take_pages'])->skip($queryParams['skip_pages']);
                },
                       'products' => function($query) use ($siteId, $queryParams) {
                        $query->siteValidation($siteId)->checked()
                                ->orderBy($queryParams['sort'], $queryParams['direction'])
                                ->take($queryParams['take'])->skip($queryParams['skip']);
                },
                ))->first();
    }
 
    /**
     * Query Builder: Attribute
     * 
     * @not: images, comments
     */
    protected function attributeQuery($siteId, $id, $queryParams)
    {
        if(!isset($id[1]) || @$id[1] == "") {
           
           $parent_attribute = Attribute::where('id','=',$id[0])->select('name')->first();
           
           $p = Attribute::where('name','like',$parent_attribute->name)->get();
           
       } else {
           
           $p = Attribute::where('id','=',$id[1])->with(
                array(  'pages' => function($query) use ($siteId, $queryParams) {
                        $query->siteValidation($siteId)->excludeHidden()->orderBy('created_at','desc')
                              ->take($queryParams['take_pages'])->skip($queryParams['skip_pages']);
                },
                       'products' => function($query) use ($siteId, $queryParams) {
                        $query->siteValidation($siteId)->checked()
                                ->orderBy($queryParams['sort'], $queryParams['direction'])
                                ->take($queryParams['take'])->skip($queryParams['skip']);
                },
                ))->first();
       }
       
       return $p;       
    }
    
    /**
     * Query Builder: Image
     * 
     * @not: comments
     */
    protected function imageQuery($siteId, $id, $queryParams)
    {
        return Image::where('id','=',$id)->with(
                    array( 'pages' => function($query) use ($siteId, $queryParams) {
                                $query->siteValidation($siteId)->excludeHidden()->orderBy('created_at','desc')
                                ->take($queryParams['take_pages'])->skip($queryParams['skip_pages']);
                        },
                            'products' => function($query) use ($siteId, $queryParams) {
                                $query->siteValidation($siteId)->checked()
                                ->orderBy($queryParams['sort'], $queryParams['direction'])
                                ->take($queryParams['take'])->skip($queryParams['skip']);
                        },
                           'categories' => function($query) use ($siteId) {
                            $query->where('sites_id','=',$siteId);
                        },          
                    ))->first();
    }
    
    /**
     * Query Builder: Index Page
     * 
     */
    protected function indexQuery($siteId, $id, $queryParams)
    {
        // 
    }    
    
    /**
     * Query Builder: Product
     * 
     * @not: tags, attributes, images, comments, downloads, userlists
     */
    protected function productQuery($siteId, $id, $queryParams = null)
    {
        if(is_numeric($id)) { $field = "id"; } else { $field = "url"; }
        
        return Product::sitevalidation($siteId)->where($field,'=',$id)->checked()->with(
                    array( 'categories' => function($query) use ($siteId) {
                            $query->where('sites_id','=',$siteId);
                        },
                            'pages' => function($query) use ($siteId) {
                            $query->sitevalidation($siteId)->excludeHidden();
                        },
                            'subproducts' => function($query) use ($siteId) {
                            $query->sitevalidation($siteId)->checked();
                        },
                            'parentproducts' => function($query) use ($siteId) {
                            $query->sitevalidation($siteId)->checked();
                        }
                    ))->first();
    }    
    
    /**
     * Query Builder: Page
     * 
     * @not: tags, attributes, images, comments
     */
    protected function pageQuery($siteId, $id, $queryParams = null)
    {
        if(is_numeric($id)) { $field = "id"; } else { $field = "url"; }
        
        return Page::sitevalidation($siteId)->where($field,'=',$id)->excludeHidden()->with(
                    array( 'categories' => function($query) use ($siteId) {
                            $query->where('sites_id','=',$siteId);
                        },
                            'products' => function($query) use ($siteId) {
                            $query->sitevalidation($siteId)->checked();
                        },
                            'subpages' => function($query) use ($siteId) {
                            $query->sitevalidation($siteId)->excludeHidden();
                        },
                            'parentpages' => function($query) use ($siteId) {
                            $query->sitevalidation($siteId)->excludeHidden();
                        }
                    ))->first();
    }    
  
    /**
     * Query Builder: Search
     * 
     * 
     * 
     * 
     * @not: tags?, attributes?, images, comments
     */
    protected function searchQuery($siteId, $id, $queryParams)
    {
        $q = explode(' ', $queryParams['q']);

        $field = $queryParams['search_field_product'];
        
        $p['products'] = Product::siteValidation($siteId)->whereNested(function($query) use ($q, $field) {
                foreach($q as $word) {
                    $query->where(function($queryNested) use ($word, $field) { 
                        $queryNested->where($field,'=',$word)->orWhere($field,'like',$word.'%%%')->orWhere($field,'like','%%%'.$word)
                            ->orWhere($field,'like','%%%'.$word.'%%%');                        
                    });
                }           
        })->checked()->orderBy($queryParams['sort'], $queryParams['direction'])
                ->take($queryParams['take'])->skip($queryParams['skip'])->get();
        
        $field = $queryParams['search_field_page'];
        
        $p['pages'] = Page::siteValidation($siteId)->whereNested(function($query) use ($q, $field) {
                foreach($q as $word) {
                    $query->where(function($queryNested) use ($word, $field) { 
                        $queryNested->where($field,'=',$word)->orWhere($field,'like',$word.'%%%')->orWhere($field,'like','%%%'.$word)
                            ->orWhere($field,'like','%%%'.$word.'%%%');                        
                    });
                }           
        })->excludeHidden()->orderBy('created_at','desc')->take($queryParams['take_pages'])
                ->skip($queryParams['skip_pages'])->get();
        
        return $p;
    } 
       
    /**
     * Query Builder: Filter
     * 
     * 
     * 
     * 
     * @params: siteconfig[] FILTER_ATTRS key|value if!exists=only_category 
     * @not: tags, attributes, images, comments
     */
    protected function filterQuery($siteId, $id, $queryParams)
    {
        $category_id = $id[0] ? $id[0] : 0;        
        
        // if category = 0, we will collect all categories 
        $c = "=";         
        if($category_id <= 0) { $c = ">"; }      
        
        $p['products'] = Product::whereHas('categories', function($q) use ($category_id, $siteId, $c) { 
            $q->where('categories_id', $c, $category_id)->where('sites_id','=',$siteId);
        })->where(function($q) use ($id) {
            if(count($id)>1) { 
                foreach($id as $k => $filter) { if($k <= 0 || $filter <= 0) { continue; }
                    $a = Attribute::find($filter)->toArray();
     
                    $q->whereHas('attributes', function($query) use ($a) {
                        $query->where('name','=',$a['name'])->where('val','=',$a['val']);
                    });
                }
            }            
        })->checked()->orderBy($queryParams['sort'], $queryParams['direction'])
                ->take($queryParams['take'])->skip($queryParams['skip'])->get();
        
        $p['pages'] = Page::whereHas('categories', function($q) use ($category_id, $siteId, $c) { 
            $q->where('categories_id', $c, $category_id)->where('sites_id','=',$siteId);
        })->where(function($q) use ($id) {
            if(count($id)>1) { 
                foreach($id as $k => $filter) { if($k <= 0 || $filter <= 0) { continue; }
                    $a = Attribute::find($filter)->toArray();
     
                    $q->whereHas('attributes', function($query) use ($a) {
                        $query->where('name','=',$a['name'])->where('val','=',$a['val']);
                    });
                }
            }            
        })->excludeHidden()->orderBy('created_at','desc')->take($queryParams['take_pages'])
                ->skip($queryParams['skip_pages'])->get();
        
        return $p;
    }
    
    /**
     * @parseFilterStr - parse configuration[FILTER_ATTRS]
     * 
     * @params $parseStr  key|value\n
     * @return $preloaded
     */
    protected function parseFilterStr($parseStr) 
    {
        if($parseStr != "") 
        { 
            $preloaded = explode('\n',$parseStr);
            foreach($preloaded as $v) {
                $filterPair = explode('|',$v);
                $filterPair[2] = Attribute::where('name','=',trim($filterPair[0]))->select('id')->first();                
            }     
            return $filterPair;
        }       
    }
    
    /**
     * Query Builder: Order
     * 
     * @not: images, user; tags; products -> withTrashed! 
     * @not: orderproducts:(longtext)attributes?comments?downloads  
     */
    protected function orderQuery($siteId, $id, $queryParams)
    {
        // TODO: show orders only for its user or administrator
        $userId = 2;
        $o = Order::where('id','=',$id)->where('users_id','=',$userId)
                ->where('hidden','!=',1)->first();
        if(is_object($o)) {
            $o->load('userbook','userdiscount','status','delivery','payment','status_history','bills');
        }
        return $o;
    } 
    
    /**
     * Query Builder: User
     * 
     * @not: -
     */
    protected function userQuery($siteId, $id, $queryParams)
    {
        // TODO: show user only for himself or administrator
        $u = User::where('id','=',$id)->where('sites_id','=',$siteId)
                ->where('banned','!=','1')->first();
        if(is_object($u)) {
            $u->load('role', 'comments', 'books', 'discounts', 'userlists', 'orders', 'bills', 'communications', 'administrator', 'searches');
        }
        // TODO: load info depending on page url
        return $u;
    }     
    
    /**
     * Query Builder: Connected, ConnectedEverywhere
     * 
     * @not: products, pages, category, tags, attributes, images, comments
     */
    protected function connectedQuery($siteId, $id, $queryParams)
    {
        if($queryParams['connectedQuery'] == 'connected') 
        {
                return Product::sitevalidation($siteId)->whereIn('id',$id)->checked()->get();
        }
        
        if($queryParams['connectedQuery'] == 'connectedEverywhere')  
        {
                return Product::whereIn('id',$id)->checked()->get();
        }
    } 
    
    /**
     * Query Builder: New, PopularByOrder, PopularByView
     * 
     * @not: products, pages, category, tags, attributes, images, comments
     */
    protected function sortingQuery($siteId, $queryParams)
    {
        if($queryParams['sortingQuery'] == 'new') 
        {
                $p = Product::sitevalidation($siteId)->checked()->orderBy('created_at', 'desc')
                ->take($queryParams['take'])->skip($queryParams['skip'])->get();
        }
        
        if($queryParams['sortingQuery'] == 'popular_ordered') 
        {
                $p = Product::sitevalidation($siteId)->checked()->orderBy('ordered','desc')
                ->take($queryParams['take'])->skip($queryParams['skip'])->get();
        }

        if($queryParams['sortingQuery'] == 'popular_viewed') 
        {
                $p = Product::sitevalidation($siteId)->checked()->orderBy('viewed','desc')
                ->take($queryParams['take'])->skip($queryParams['skip'])->get();
        }
                
        return $p;
    }     
    
}
