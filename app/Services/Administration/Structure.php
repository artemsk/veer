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
	 */
	public function updateProducts()
	{
		return (new Elements\Product)->run();	
	}
		
	/**
	 * update Pages
	 */
	public function updatePages()
	{
		return (new Elements\Page)->run();	
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
