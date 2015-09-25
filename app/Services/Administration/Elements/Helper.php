<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

trait Helper {

    protected $uploadDataProvider = [
        'image' => ['images', 'images_path', 'public', '\\Veer\\Models\\Image', 'img', []],
        'file' => ['files', 'downloads_path', 'app', '\\Veer\\Models\\Download', 'fname', 
                    ['original' => 1, 'expires' => 0, 'expiration_day' => 0, 'expiration_times' => 0, 'downloads' => 0]]
    ];
    
    /**
     * Upload Image
     * Todo: Test
     */
    public function upload($type, $files, $id, $relationOrObject, $prefix = null, $message = null, $skipRelation = false)
    {
        list($relation, $assets_path, $folder, $model, $field, $default) = $this->uploadDataProvider[$type];
        $path = $type == 'image' ? base_path() : storage_path();
        $newId = null;
        
        foreach(is_array(Input::file($files)) ? Input::file($files) : [Input::file($files)] as $file) {
            $fname = $prefix . $id . "_" . date('YmdHis', time()) . str_random(10) . "." . $file->getClientOriginalExtension();

            $this->uploadingLocalOrCloudFiles($relation, $file, $fname, config('veer.' . $assets_path), $path . "/" . $folder . "/");
            
            $new = new $model;
            $new->{$field} = $fname;
            foreach($default as $key => $value) { $new->{$key} = $value; }
            
            if($type == 'image' || $skipRelation == true) {
                $new->save();
            } 
            
            if($skipRelation == false) {
                if($type == "image") { $new->{$relationOrObject}()->attach($id); }
                if($type == "file") { $relationOrObject->downloads()->save($new); }
            }
            
            $newId = $new->id; // ? 
        }

        if(!empty($message)) { event('veer.message.center', array_get($message, 'language')); }
        return $newId;
    }

    /**
     * Upload to Local or Cloud
     * 
     */
    protected function uploadingLocalOrCloudFiles($type, $file, $fname, $assetPath, $localDestination = "")
    {
        if(!config('veer.use_cloud_' . $type)) {
            return $file->move($localDestination . $assetPath, $fname);
        } 
            
        \Storage::put($assetPath . '/' . $fname, file_get_contents($file->getPathName()));
    }

    /**
     * Copy files to new obj.
     * 
     */
    protected function copyFiles($files, $object)
    {
        $filesDb = $this->parseIds($files);
        if(!is_array($filesDb)) { return null; }
        
        foreach($filesDb as $file) {
            $fileModel = \Veer\Models\Download::find($file);
            if(is_object($fileModel)) {
                $newfile = $fileModel->replicate();
                $object->downloads()->save($newfile);
            }
        }        
    }

    /**
     * Remove (detach) file
     * 
     */
    protected function removeFile($removeFile)
    {
        if(!starts_with($removeFile, 'removeFile')) { return null; }
        
        $r = explode(".", $removeFile);
        if(isset($r[1]) && !empty($r[1])) {
            \Veer\Models\Download::where('id', '=', $r[1])->update(['elements_id' => null, 'elements_type' => '']);
        }
    }

    /**
     * Delete file
     * 
     */
    protected function deleteFile($id)
    {
        $f = \Veer\Models\Download::find($id);
        if(!is_object($f)) { return null; }
        
        $allCopies = \Veer\Models\Download::where('fname', '=', $f->fname)->get();
        
        if (count($allCopies) <= 1) { // last one
            $this->deletingLocalOrCloudFiles('files', $f->fname, config("veer.downloads_path"), storage_path() . '/app/');
        }
        
        $f->delete();
    }

    /** 
     * Delete From Local or Cloud
     * 
     */
    protected function deletingLocalOrCloudFiles($type, $fname, $assetPath, $localDestination = "")
    {
        if(!config('veer.use_cloud_' . $type)) {
            return \File::delete($localDestination . $assetPath . "/" . $fname);
        } 
        
        \Storage::delete($assetPath . '/' . $fname);
    }

    /**
     * Prepare files for copying
     * 
     */
    protected function prepareCopying($fileId, $prds = [], $pgs = [])
    {
        foreach(['Product' => $prds, 'Page' => $pgs] as $type => $ids) {
            if(!is_array($ids)) continue;
            
            $className = '\\Veer\\Models\\' . $type;
            
            foreach($ids as $id) {
                $object = $className::find(trim($id));
                if(is_object($object)) {
                    $this->copyFiles(":" . $fileId, $object);
                }
            }
        }        
    }

    /**
     * Sorting Elements
     * 
     */
    protected function sortElements($elements, $sortingParams)
    {
        $newsort = [];
        foreach($elements as $s) {
            if($s->id != $sortingParams['parentid']) { continue; }            
            $id = $s->{$sortingParams['relationship']}[$sortingParams['oldindex']]->id;
            
            foreach($s->{$sortingParams['relationship']} as $k => $c) {
                
                if($sortingParams['newindex'] > $sortingParams['oldindex'] && $c->id != $id) $newsort[] = $c->id;                
                if($sortingParams['newindex'] == $k) $newsort[] = $id;                
                if($sortingParams['newindex'] < $sortingParams['oldindex'] && $c->id != $id && !in_array($c->id, $newsort)) {
                    $newsort[] = $c->id;
                }
            }
        }
        
        return $newsort;
    }

    /**
     * Change Product Status
     * 
     */
    protected function changeProductStatus($product)
    {
        if (!is_object($product)) { return null; }

        switch ($product->status) {
            case "hide": $product->status = "buy"; break;
            case "sold": $product->status = "hide"; break;
            default: $product->status = "sold"; break;
        }
        
        $product->save();
    }
    
    /**
     * Products actions
     * 
     */
    protected function quickProductsActions($action)
    {
        if (starts_with($action, "changeStatusProduct")) {
            $r = explode(".", $action);
            $this->changeProductStatus(\Veer\Models\Product::find($r[1]));
            event('veer.message.center', trans('veeradmin.product.status'));
        }

        if (starts_with($action, "deleteProduct")) {
            $r = explode(".", $action);
            $this->deleteProduct($r[1]);
            event('veer.message.center', trans('veeradmin.product.delete') .
                " " . app('veeradmin')->restore_link('product', $r[1]));
        }

        if (starts_with($action, "showEarlyProduct")) {
            \Eloquent::unguard();
            $r = explode(".", $action);
            \Veer\Models\Product::where('id', '=', $r[1])->update(array("to_show" => now()));
            event('veer.message.center', trans('veeradmin.product.show'));
        }
    }

    /**
     * Pages actions
     * 
     */
    protected function quickPagesActions($action)
    {
        if (starts_with($action, "changeStatusPage")) {
            $r = explode(".", $action);
            $page = \Veer\Models\Page::find($r[1]);
            $page->hidden = $page->hidden == true ? false : true;
            $page->save();
            event('veer.message.center', trans('veeradmin.page.status'));
        }

        if (starts_with($action, "deletePage")) {
            $r = explode(".", $action);
            $this->deletePage($r[1]);
            event('veer.message.center', trans('veeradmin.page.delete') .
                " " . app('veeradmin')->restore_link('page', $r[1]));
        }
    }

}
