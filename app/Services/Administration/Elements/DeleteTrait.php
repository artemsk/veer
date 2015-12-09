<?php namespace Veer\Services\Administration\Elements;

trait DeleteTrait {
    
    protected function deleteEntity($id, $type)
    {
        $className = '\\Veer\\Models\\' . ucfirst($type); 
        $p = $className::find($id);
		if(is_object($p)) {
            
            switch($type) {
                case 'product':
                    $p->subproducts()->detach();
                    $p->parentproducts()->detach();
                    $p->pages()->detach();
                    break;
                case 'page':
                    $p->subpages()->detach();
                    $p->parentpages()->detach();
                    $p->products()->detach();
                    break;
            }
            
			$p->categories()->detach();
			$p->tags()->detach();
			$p->attributes()->detach();
			$p->images()->detach();
			$p->downloads()->update(["elements_id" => 0]);
			
			$p->userlists()->delete();
			$p->delete();
			// [orders_products], comments, communications skip
		}  
    }
    
    /**
	 * Delete Product & relationships
     * 
	 */
	protected function deleteProduct($id)
	{
		$this->deleteEntity($id, 'product');
	}
    
    /**
	 * Delete Page & relationships
     * 
	 */
	protected function deletePage($id)
	{
		$this->deleteEntity($id, 'page');
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

    /**
     * Restore link
     * 
     */
    protected function restore_link($type, $id)
	{
		return "<a href=". route('admin.update', array('restore', 'type' => $type, 'id' => $id)) .">".
			\Lang::get('veeradmin.undo')."</a>";
	}
    
}