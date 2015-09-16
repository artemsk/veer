<?php namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class Site {
    
    protected $data;
    protected $turnoff;
    protected $turnon;

    public function __construct()
    {
        $this->data = Input::get('site');
		$this->turnoff = Input::get('turnoff');
		$this->turnon = Input::get('turnon');
    }
    
    /* TODO: for security reasons - important to check access for users */

    public function setParams($data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function turnon($siteid)
    {
        $this->turnon = $siteid; 
        return $this;
    }
    
    public function turnoff($siteid)
    {
        $this->turnoff = $siteid;
        return $this;
    }
    
    public function create($url)
    {
        $this->data = [
            'id' => 'dummy', // TODO: test
            'url' => $url,
        ];
        
        return $this->run();
    }
    
    public function refreshSnapshot($siteid)
    {
        $site = \Veer\Models\Site::find($siteid);
        
        return is_object($site) ? $this->refreshSiteSnapshots($site->url, $siteid) : false;
    }
    
    /**
     * Run
     * 
     */
    public function run()
    {
        $message = \Lang::get('veeradmin.sites.update');

        foreach ($this->data as $key => $values) {
            
            $values['url'] = trim($values['url']);
            if (empty($values['url'])) { continue; }

            $site = \Veer\Models\Site::firstOrNew(array("id" => trim($key)));

            if (app('veer')->siteId != $key) { $site->url = $values['url']; }
            
            $site->parent_id = empty($values['parent_id']) ? 0 : $values['parent_id'];
            $site->manual_sort = empty($values['manual_sort']) ? 0 : $values['manual_sort'];

            if (app('veer')->siteId != $key) { 
                $site->redirect_on = empty($values['redirect_on']) ? 0 : true;
                $site->redirect_url = empty($values['redirect_url']) ? '' : $values['redirect_url'];
            }

            $site->on_off = isset($site->on_off) ? $site->on_off : false;

            if ($key == $this->turnoff && app('veer')->siteId != $key) {
                $site->on_off = false;
                $message.= \Lang::get('veeradmin.sites.down', array('site_id' => $site->id));
            }

            if ($key == $this->turnon) {
                $site->on_off = true;
                $message.= \Lang::get('veeradmin.sites.up', array('site_id' => $site->id));
            }

            if (!isset($site->id)) { $message.= \Lang::get('veeradmin.sites.new'); }

            $site->save();
            
            if (Input::has('snapshots')) $this->refreshSiteSnapshots($site->url, $site->id);
        }

        if (app('veer')->siteId == $this->turnoff) { $message.= \Lang::get('veeradmin.sites.error'); }

        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        event('veer.message.center', $message);
    }
    
    /**
     * Refresh Snapshots - uses wkhtmltoimage (bugs warn.)
     * 
     */
    protected function refreshSiteSnapshots($siteUrl, $siteId, $width = 1368, $height = 768)
    {
        if (config('veer.wkhtmltoimage') == null) return false;

        @unlink(public_path() . "/" . config('veer.images_path') . "/site-" . $siteId . ".jpg");

        exec(config('veer.wkhtmltoimage') . " --width " . $width . " --disable-smart-width --height " . $height . " " . $siteUrl . " " . public_path() . "/" . config('veer.images_path') . "/site-" . $siteId . ".jpg");

        sleep(5);
    }
}
