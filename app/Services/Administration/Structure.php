<?php namespace Veer\Services\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class Structure {
	
    use Elements\Helper, Elements\Attach, Elements\Delete;
    
    protected $action = null;

    public function __construct($t)
    {
        $this->action = 'update' . ucfirst($t);
        app('veeradmin')->skipShow = false;
    }
    
    public function handle()
    {
        return $this->{$this->action}();
    }
    
	/**
	 * Update Sites
	 * @return void 
	 */
	public function updateSites()
	{
        return (new Elements\Site)->run();
    }	
        
	/**
	 * Update Root Categories
	 */
	public function updateCategories()
	{
		return (new Elements\Category)->run();	
	}	


	/**
	 * update Products
	 * @return type
	 */
	public function updateProducts()
	{
		// if we're working with one product then call another function
		//
		$editOneProduct = Input::get('id');
		if(!empty($editOneProduct)) { 
			
			return $this->updateOneProduct($editOneProduct); 
		}
				
		// quick actions: status etc.
		$this->quickProductsActions(Input::get('action'));

		$all = Input::all();
		
		$title = trim(array_get($all, 'fill.title'));
		$freeForm = trim(array_get($all, 'freeForm'));
		
		if(empty($freeForm) && !empty($title)) {
			
			$prices = explode(":", array_get($all, 'prices'));
			$options =  explode(":", array_get($all, 'options'));
			$categories =  explode(",", array_get($all, 'categories'));
			
			$p = new \Veer\Models\Product;
			$p->title = $title;
			$p->url = trim(array_get($all, 'fill.url', ''));
			$p->price = array_get($prices, 0, 0);
			$p->price_sales = array_get($prices, 1, 0);
			$p->price_opt = array_get($prices, 2, 0);
			$p->price_base = array_get($prices, 3, 0);
			$p->currency = array_get($prices, 4, 0);
			$p->qty = array_get($options, 0, 0);
			$p->weight = array_get($options, 1, 0);
			$p->score = array_get($options, 2, 0);
			$p->star = array_get($options, 3, 0);
			$p->production_code = array_get($options, 4, '');
			$p->status = "hide";
			$p->save();
			
			if(!empty($categories)) {
				$p->categories()->attach($categories);
			}
			
			// images
			if(Input::hasFile('uploadImage')) {
				$this->upload('image', 'uploadImage', $p->id, 'products', 'prd', null);
			}

			//files
			if(Input::hasFile('uploadFile')) {
				$this->upload('file', 'uploadFile', $p->id, $p, 'prd', null);
			}		
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.new'));
				
		}
		
		if(!empty($freeForm)) {
			
			$parseff = preg_split('/[\n\r]+/', trim($freeForm) );
			foreach($parseff as $p) {
				$items = explode("|", $p);

				$p = new \Veer\Models\Product;
				$p->title = array_get($items, 0, '');
				$p->url = array_get($items, 1, '');
				$p->qty = array_get($items, 3, 0);
				$p->weight =  array_get($items, 4, 0);
				$p->currency =  array_get($items, 5, 0);				
				$p->price =  array_get($items, 6, 0);
				$p->price_sales =  array_get($items, 7, 0);
				$p->price_opt =  array_get($items, 8, 0);
				$p->price_base =  array_get($items, 9, 0);
				$p->price_sales_on = array_get($items, 10, 0);
				$p->price_sales_off = array_get($items, 11, 0);
				$p->to_show = array_get($items, 12, 0);				
				$p->score = array_get($items, 13, 0);	
				$p->star = array_get($items, 14, 0);	
				$p->production_code = array_get($items, 17, 0);	
				$p->status = array_get($items, 18, 'hide');					
				$p->descr = substr(array_get($items, 19, ''), 2, -2);
				$p->save();
			
				$categories =  explode(",", array_get($items, 2, ''));
				if(!empty($categories)) {
					$p->categories()->attach($categories);
				}

				$image = array_get($items, 15);
				if(!empty($image)) {
					$new = new \Veer\Models\Image; 
					$new->img = $image;
					$new->save();
					$new->products()->attach($p->id);			
				}
				
				$file= array_get($items, 16);
				if(!empty($file)) {
					$new = new \Veer\Models\Download; 
					$new->original = 1;
					$new->fname= $file;
					$new->expires = 0;
					$new->expiration_day = 0;
					$new->expiration_times = 0;
					$new->downloads = 0;
					$p->downloads()->save($new);		
				}				
			}	
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.new'));
						
		}
	}
	
	
	/**
	 * update One product
	 * @param type $id
	 */
	public function updateOneProduct($id)
	{
		\Eloquent::unguard();
		
		$all = Input::all();
		$action = array_get($all, 'action');
				
		array_set($all, 'fill.star', isset($all['fill']['star']) ? true : 0);
		array_set($all, 'fill.download', isset($all['fill']['download']) ? true : 0);
	
		$salesOn = parse_form_date(array_get($all, 'fill.price_sales_on', 0));
		
		$salesOff = parse_form_date(array_get($all, 'fill.price_sales_off', 0));
					
		$toShow = parse_form_date(array_get($all, 'fill.to_show', 0));
				
		array_set($all, 'fill.price_sales_on', $salesOn);

		array_set($all, 'fill.price_sales_off', $salesOff);
		
		$toShow->hour((int)array_get($all, (int)'to_show_hour', 0));
		$toShow->minute((int)array_get($all, (int)'to_show_minute', 0));
		
		array_set($all, 'fill.to_show', $toShow);
		$all['fill']['url'] = trim($all['fill']['url']);

		if($action == "add" || $action == "saveAs") {
			
			$product = new \Veer\Models\Product;
			$product->fill($all['fill']);
			$product->status = "hide";
			$product->save();
			
			$id = $product->id;
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.new'));
			
		} else {
			$product = \Veer\Models\Product::find($id);
		}
	
		if($action == "update") {
			$product->fill($all['fill']);
			$product->save();
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.update'));
					
		}
		
		//status
		if($action == "updateStatus.".$id) 
		{
			$this->changeProductStatus($product);
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.status'));
			
		}
		
		$this->connections($product, $id, 'products', array(
			"actionButton" => $action,
			"tags" => $all['tags'],
			"attributes" => $all['attribute'],
			"attachImages" => $all['attachImages'],
			"attachFiles" => $all['attachFiles'],
			"attachCategories" => $all['attachCategories'],
			"attachPages" => $all['attachPages'],
			"attachChildProducts" => $all['attachChildProducts'],
			"attachParentProducts" => $all['attachParentProducts']
		), array(
			"prefix" => array("image" => "prd", "file" => "prd")
		));		
			
		// freeform
		if(!empty($all['freeForm'])) {
			$ff = preg_split('/[\n\r]+/', trim($all['freeForm']) );
			foreach ($ff as $freeForm) {
				if(starts_with($freeForm, 'Tag:')) {
					$this->attachElements($freeForm, $product, 'tags', null, ",", "Tag:");
				} else {
					$this->attachElements($freeForm, $product, 'attributes', null, ",", "Attribute:");
				}
			}
		}
		
		if($action == "add" || $action == "saveAs") {
			app('veeradmin')->skipShow = true;
			Input::replace(array('id' => $id));
			return \Redirect::route('admin.show', array('products', 'id' => $id));
		}
	}
	
	/**
	 * update Pages
	 */
	public function updatePages()
	{
		// if we're working with one page then call another function
		//
		$editOnePage = Input::get('id');
		if(!empty($editOnePage)) { 
			
			return $this->updateOnePage($editOnePage); 
		}
		
		//quick actions
		$this->quickPagesActions(Input::get('action'));
	
		$all = Input::all();
		
		$title = trim(array_get($all, 'title'));

		if(!empty($title)) {
			
			$categories =  explode(",", array_get($all, 'categories'));
			
			$p = new \Veer\Models\Page;
			$p->title = $title;
			$p->url = trim(array_get($all, 'url', ''));
			$p->hidden = 1;
			$p->manual_order = 999999;
			$p->users_id = \Auth::id();
			
			$txt= preg_replace("/{{(?s).*}}/", "", array_get($all, 'txt', ''), 1);
			$result = preg_match("/{{(?s).*}}/", array_get($all, 'txt', ''), $small);
			
			$p->small_txt = substr(trim( array_get($small, 0, '') ), 2, -2);
			$p->txt = trim( $txt );
			$p->save();
					
			if(!empty($categories)) {
				$p->categories()->attach($categories);
			}
			
			// images
			if(Input::hasFile('uploadImage')) {
				$this->upload('image', 'uploadImage', $p->id, 'pages', 'pg', null);
			}

			//files
			if(Input::hasFile('uploadFile')) {
				$this->upload('file', 'uploadFile', $p->id, $p, 'pg', null);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.new'));
					
		}
	}

	
	/**
	 * update One Page
	 * @param type $id
	 */
	public function updateOnePage($id)
	{	
		\Eloquent::unguard();
		
		$all = Input::all();
		$action = array_get($all, 'action');
				
		array_set($all, 'fill.original', isset($all['fill']['original']) ? true : 0);
		array_set($all, 'fill.show_small', isset($all['fill']['show_small']) ? true : 0);
		array_set($all, 'fill.show_comments', isset($all['fill']['show_comments']) ? true : 0);
		array_set($all, 'fill.show_title', isset($all['fill']['show_title']) ? true : 0);		
		array_set($all, 'fill.show_date', isset($all['fill']['show_date']) ? true : 0);
		array_set($all, 'fill.in_list', isset($all['fill']['in_list']) ? true : 0);
		array_set($all, 'fill.users_id', empty($all['fill']['users_id']) ? \Auth::id() : $all['fill']['users_id']);
                $all['fill']['url'] = trim($all['fill']['url']);

		if($action == "add" || $action == "saveAs") {
			
			$page = new \Veer\Models\Page;
			$page->fill($all['fill']);
			$page->hidden = true;
			$page->save();
			
			$id = $page->id;
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.new'));
			
		} else {
			$page = \Veer\Models\Page::find($id);
		}
	
		if($action == "update") {
			$page->fill($all['fill']);
			$page->save();
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.update'));
			
		}
		
		//status
		if($action == "changeStatusPage.".$id) 
		{
			if($page->hidden == true) { $page->hidden = false; } else { $page->hidden = true; }
			$page->save();
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.status'));
			
		}		
		
		$this->connections($page, $id, 'pages', array(
			"actionButton" => $action,
			"tags" => $all['tags'],
			"attributes" => $all['attribute'],
			"attachImages" => $all['attachImages'],
			"attachFiles" => $all['attachFiles'],
			"attachCategories" => $all['attachCategories'],
			"attachProducts" => $all['attachProducts'],
			"attachChildPages" => $all['attachChildPages'],
			"attachParentPages" => $all['attachParentPages']
		), array(
			"prefix" => array("image" => "pg", "file" => "pg")
		));		
			
		// freeform
		if(!empty($all['freeForm'])) {
			$ff = preg_split('/[\n\r]+/', trim($all['freeForm']) );
			foreach ($ff as $freeForm) {
				if(starts_with($freeForm, 'Tag:')) {
					$this->attachElements($freeForm, $page, 'tags', null, ",", "Tag:");
				} else {
					$this->attachElements($freeForm, $page, 'attributes', null, ",", "Attribute:");
				}
			}
		}
		
		if($action == "add" || $action == "saveAs") {
			app('veeradmin')->skipShow = true;
			Input::replace(array('id' => $id));
			return \Redirect::route('admin.show', array('pages', 'id' => $id));	
		}
	}
	
	
	
	
	
	
	

	
	
	
	/**
	 * update images 
	 */
	public function updateImages()
	{
		$all = Input::all();
		foreach($all as $k => $v) { 
			if(Input::hasFile($k)) {				
					$newId[] = $this->upload('image', $k, null, null, '', null, true);
					Event::fire('veer.message.center', \Lang::get('veeradmin.image.upload'));
								
			}				
		}
			
		$attachImages = array_get($all, 'attachImages');
		if(!empty($attachImages)) { 
			
			$result = preg_match("/\[(?s).*\]/", $attachImages, $small);
			$parseTypes = explode(":", substr(array_get($small, 0, ''),2,-1));
					
			if(starts_with($attachImages, 'NEW')) {
				$attach = empty($newId) ? null : $newId;
			} else {
				$parseAttach = explode("[", $attachImages);
				$attach = explode(",", array_get($parseAttach, 0));				
			}
			
			$this->attachFromForm($parseTypes, $attach, 'images');
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.image.attach'));
						
		}
		
		if(starts_with(array_get($all, 'action'), 'deleteImage')) {
			$r = explode(".", $all['action']);
			$this->deleteImage($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.image.delete'));
				
		}
		
	}
	
	
	
	
	
	/**
	 * update tags
	 */
	public function updateTags()
	{		
		\Eloquent::unguard();
		
		if(starts_with(Input::get('action'), "deleteTag")) {
			$r = explode(".", Input::get('action'));
			$this->deleteTag($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.tag.delete'));
						
		} else {
		
			$existingTags = Input::get('renameTag');
			if(is_array($existingTags)) {
				foreach($existingTags as $key => $value) { $value = trim($value);
					$tagDb = \Veer\Models\Tag::where('name','=',$value)->first();
					if(!is_object($tagDb)) {
						\Veer\Models\Tag::where('id','=',$key)->update(array('name' => $value));
					}
				}
			}

			$new = $this->parseForm(Input::get('newTag'));

			if(is_array($new['target'])) {
				foreach($new['target'] as $tag) {
					$tag = trim($tag);
					if(empty($tag)) { continue; }
					$tagDb = \Veer\Models\Tag::firstOrNew(array('name' => $tag));
					$tagDb->save();
					$tags[] = $tagDb->id;
				}
				if(isset($tags)) {
					$this->attachFromForm($new['elements'], $tags, 'tags');
				}		
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.tag.update'));
								
		}
	}
	
	/**
	 * update downloads
	 */
	public function updateDownloads()
	{
		$action = Input::get('action');
		
		$this->removeFile($action);
		
		if(starts_with($action, 'deleteFile'))
		{
			$r = explode(".", $action);
			$this->deleteFile($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.delete'));
			
		}
		
		if(starts_with($action, 'makeRealLink')) 
		{
			$times = Input::get('times', 0);
			$exdate = Input::get('expiration_day');
                        $linkname = Input::get('link_name');
			
			$r = explode(".", $action);
			$f = \Veer\Models\Download::find($r[1]);
			if(is_object($f)) {
				$newF = $f->replicate();
				$newF->secret = empty($linkname) ? str_random(100).date("Ymd", time()) : $linkname;
				if($times > 0 || !empty($exdate)) { 
					$newF->expires = 1;
					$newF->expiration_times = $times;
					if(!empty($exdate)) {
						$newF->expiration_day = \Carbon\Carbon::parse($exdate);				
					}
				}
				
				$newF->original = 0;
				$newF->save();
				Event::fire('veer.message.center', \Lang::get('veeradmin.file.download'));
				
			}
		}
		
		if(starts_with($action, 'copyFile')) 
		{
			$r = explode(".", $action);
			$prdIds = explode(",", Input::get('prdId'));
			$pgIds = explode(",", Input::get('pgId'));
			$this->prepareCopying($r[1], $prdIds, $pgIds);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.copy'));
					
		}
		
		if(Input::hasFile(Input::get('uploadFiles'))) {				
			$newId[] = $this->upload('file', 'uploadFiles', null, null, '', null, true);
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.upload'));
						
		}
		
		$attachFiles = Input::get('attachFiles');
		if(!empty($attachFiles)) { 
			
			$parseTypes = $this->parseForm($attachFiles);
			
			$attach = array();
			
			if(is_array($parseTypes['target'])) {
				foreach($parseTypes['target'] as $t) {
					$t = trim($t);
					if(empty($t) || $t == "NEW") { 
						if(!empty($newId)) {
							$attach = array_merge($attach, $newId);
						}
						continue;
					}					
					$attach[] = $t;
				}	
			}
			
			$prdIds = explode(",", array_get($parseTypes, 'elements.0'));
			$pgIds = explode(",",  array_get($parseTypes, 'elements.1'));
			foreach($attach as $f) {
				$this->prepareCopying($f, $prdIds, $pgIds);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.attach'));
						
		}		
	}
	
	
	/**
	 * update attributes
	 */
	public function updateAttributes()
	{
		\Eloquent::unguard();
		
		if(starts_with(Input::get('action'), "deleteAttrValue")) {
			list($act, $id) = explode(".", Input::get('action'));
			$this->deleteAttribute($id);
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.delete'));
				
			
		} elseif(Input::get('action') == "newAttribute") {
		
			$manyValues = preg_split('/[\n\r]+/', trim(Input::get('newValue')) );
			foreach($manyValues as $value) {
				$this->attachToAttributes(Input::get('newName'), $value);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.new'));
					
		} else {
			
			// rename attribute name
			$attrName = Input::get('renameAttrName');
			foreach($attrName as $k => $v) 
			{
				if($k != $v) {
					\Veer\Models\Attribute::where('name', '=', $k)
						->update(array('name' => $v));
				}
			}
			
			// update attribute value & descr
			$attrVal = Input::get('renameAttrValue');
			$attrDescr = Input::get('descrAttrValue');
			$attrType = Input::get('attrType');
			foreach($attrVal as $k => $v) 
			{
				if(array_get($attrType, $k, 0) == 1) { $type = "descr"; } else { $type = "choose"; }
				
				\Veer\Models\Attribute::where('id', '=', $k)
						->update(array('val' => $v, 
							'descr' => array_get($attrDescr, $k, ''),
							'type' => $type));
			}
			
			// add new values to existing name
			$newAttrValue = Input::get('newAttrValue');
			foreach($newAttrValue as $k => $v) 
			{	
				$this->attachToAttributes($k, $v);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.update'));
						
		}
	}
	
	
	
	
	
	
	
}
