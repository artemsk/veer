<?php
namespace Veer\Lib\Components;

use Veer\Models\Category;
use Veer\Models\Tag;
use Veer\Models\Attribute;
use Veer\Models\Image;
use Veer\Models\Product;
use Veer\Models\Page;
use Illuminate\Support\Facades\Config;

/**
 * 
 * Veer.Components @globalGetModelsData !essential
 * 
 * - collect product for category/tag/attribute/search etc. 
 *   is used everywhere: tag, category, attribute, image, index?, product,
 *   page
 * 
 * @params $group_img_flag = "0", $onlynums_flag = "0"
 * @params $method by route: search!, filter?, user?, order?
 * @params $method other: id, array(id), (all)array(id), new, popular order, popular viewed
 * 
 * @return 
 */

class globalGetModelsData {   
    
    protected $v;
    
    function __construct($params = null) {

        $this->v = \App::make('veer');
        
        $siteId = $this->v->siteId;
        $queryParams = $this->paramsDefault($params['params']);
        
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
            
            case "search.show":  
                $p = $this->searchQuery($siteId, $params['id'], $queryParams);
                break;              
            
            case "filter.show":  
                $p = $this->filterQuery($siteId, $params['id'], $queryParams);
                break;    
            
            case "order.show":  
                $p = $this->orderQuery($siteId, $params['id'], $queryParams);
                break;      
            
            case "user.show":  
                $p = $this->userQuery($siteId, $params['id'], $queryParams);
                break;       
            
            case "connected":  
                $p = $this->connectedQuery($siteId, $params['id'], $queryParams);
                break;  
            
            case "connectedEverywhere":  
                $p = $this->connectedQuery($siteId, $params['id'], $queryParams);
                break;          
            
            case "new":  
                $p = $this->sortingQuery($siteId, $params['id'], $queryParams);
                break;     

            case "popularByOrder":  
                $p = $this->sortingQuery($siteId, $params['id'], $queryParams);
                break;  

            case "popularByView":  
                $p = $this->sortingQuery($siteId, $params['id'], $queryParams);
                break;         
            
            default:
                break;      
        }
        
             echo "<pre>";
          print_r(\Illuminate\Support\Facades\DB::getQueryLog());
          echo "</pre>";
        
        
    }
    
    /*
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
            "take_pages" => 15
        );
                
        foreach($defaultParams as $k => $v) {
            if( !isset($params[$k]) ) { $params[$k] = $v; }
        }
        
        return $params;
    }    
    
    /*
     * Query Builder: Category
     * 
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
    
    /*
     * Query Builder: Tag
     * 
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
 
    /*
     * Query Builder: Attribute
     * 
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
    
    
    /*
     * Query Builder: Image
     * 
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
    
    /*
     * Query Builder: Index Page
     * 
     */
    protected function indexQuery($siteId, $id, $queryParams)
    {
        // 
    }    
    
    /*
     * Query Builder: Product
     * 
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
    
    /*
     * Query Builder: Page
     * 
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
 
    /*
     * Query Builder: Search
     * 
     */
    protected function searchQuery($siteId, $id, $queryParams)
    {
        
    } 
    
    /*
     * Query Builder: Filter
     * 
     */
    protected function filterQuery($siteId, $id, $queryParams)
    {
        
    } 
 
    /*
     * Query Builder: Order
     * 
     */
    protected function orderQuery($siteId, $id, $queryParams)
    {
        
    } 
    
    /*
     * Query Builder: User
     * 
     */
    protected function userQuery($siteId, $id, $queryParams)
    {
        
    }     
    
    /*
     * Query Builder: Connected, ConnectedEverywhere
     * 
     */
    protected function connectedQuery($siteId, $id, $queryParams)
    {
        
    } 
    
    /*
     * Query Builder: New, PopularByOrder, PopularByView
     * 
     */
    protected function sortingQuery($siteId, $id, $queryParams)
    {
        
    }     
    
}

// TODO: cache?
