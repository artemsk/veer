<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

trait Delete {
    
    /**
	 * Delete Product & relationships
     * 
	 */
	protected function deleteProduct($id)
	{
		$p = \Veer\Models\Product::find($id);
		if(is_object($p)) {
			$p->subproducts()->detach();
			$p->parentproducts()->detach();
			$p->pages()->detach();
			$p->categories()->detach();
			$p->tags()->detach();
			$p->attributes()->detach();
			$p->images()->detach();
			$p->downloads()->update(array("elements_id" => 0));
			
			$p->userlists()->delete();
			$p->delete();
			// orders_products, comments, communications skip
		}
	}
    
    /**
	 * Delete Page & relationships
     * 
	 */
	protected function deletePage($id)
	{
		$p = \Veer\Models\Page::find($id);
		if(is_object($p)) {
			$p->subpages()->detach();
			$p->parentpages()->detach();
			$p->products()->detach();
			$p->categories()->detach();
			$p->tags()->detach();
			$p->attributes()->detach();
			$p->images()->detach();
			$p->downloads()->update(array("elements_id" => 0));
			
			$p->userlists()->delete();
			$p->delete();
			// comments, communications skip
		}
	}
    
    /**
	 * Delete Category: Category & connections
	 *
	 */
	protected function deleteCategory($cid)
	{
        if(empty($cid)) return null;
		\Veer\Models\Category::destroy($cid);
		\Veer\Models\CategoryConnect::where('categories_id','=',$cid)->forceDelete();
		\Veer\Models\CategoryPivot::where('parent_id','=',$cid)->orWhere('child_id','=',$cid)->forceDelete();
		\Veer\Models\ImageConnect::where('elements_id','=',$cid)
		->where('elements_type','=','Veer\Models\Category')->forceDelete();
		// We do not delete communications for deleted items
	}
    
    /**
	 * Delete Image function
	 * 
	 */
	protected function deleteImage($id)
	{
		$img = \Veer\Models\Image::find($id);
		if(is_object($img)) {
			$img->pages()->detach();
			$img->products()->detach();
			$img->categories()->detach();
			$img->users()->detach();
            $this->deletingLocalOrCloudFiles('images', $img->img, config("veer.images_path"));
			$img->delete();			
		}
	}
    
    /**
	 * Delete attribute
	 *
     */
	protected function deleteAttribute($id)
	{
		$t = \Veer\Models\Attribute::find($id);
		if(is_object($t)) {
			$t->pages()->detach();
			$t->products()->detach();
			$t->delete();			
		}
	}
    
    /**
	 * Delete Tag
	 * 
	 */
	protected function deleteTag($id)
	{
		$t = \Veer\Models\Tag::find($id);
		if(is_object($t)) {
			$t->pages()->detach();
			$t->products()->detach();
			$t->delete();			
		}
	}
    
}