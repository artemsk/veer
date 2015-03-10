<?php

namespace Veer\Services\Administration;

class Configuration
{
    protected $siteid;
    protected $confs;
    protected $new;
    protected $actionSave   = null;
    protected $actionDelete = null;
    protected $cardid;

    public function __construct()
    {
        $this->siteid       = \Input::get('siteid');
        $this->confs        = \Input::get('configuration');
        $this->new          = \Input::get('new');
        $this->actionDelete = \Input::get('dele');
        $this->actionSave   = \Input::get('save');
    }

    public function setParams($siteid, $confs = null, $new = null)
    {
        $this->siteid = $siteid;
        $this->confs  = $confs;
        $this->new    = $new;
    }

    protected function prepareConfs()
    {
        if (!empty($this->confs))
                $this->cardid = head(array_keys($this->confs));

        if (!empty($this->new)) {
            $this->cardid = $this->siteid;
            $this->confs  = $this->new;
        }
    }

    protected function isSiteSet()
    {
        if (!empty($this->siteid)) return true;

        \Event::fire('veer.message.center', \Lang::get('veeradmin.error.reload'));

        return false;
    }

    public function pushDelete($delete)
    {
        $this->actionDelete = $delete;

        return $this->handle();
    }

    public function pushUpdate($update)
    {
        $this->actionSave = $update;

        return $this->handle();
    }

    /**
     * main entry point
     */
    public function handle()
    {
        $this->prepareConfs();

        if ($this->isSiteSet()) {

            if (!empty($this->actionDelete)) $this->deleteConfiguration();

            if (!empty($this->actionSave) && null != array_get($this->confs,
                    $this->cardid.'.key')) $this->updateConfiguration();

            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            if (app('request')->ajax()) return $this->ajaxRequest();
        }
    }

    protected function ajaxRequest()
    {
        $items = (new \Veer\Services\Show\Site)->getConfiguration($this->siteid,
            array('id', 'desc'));

        return view(app('veer')->template.'.lists.configuration-cards',
            array(
            "configuration" => $items[0]->configuration,
            "siteid" => $this->siteid,
        ));
    }

    protected function updateConfiguration()
    {
        \Eloquent::unguard();

        $newc = \Veer\Models\Configuration::firstOrNew(array("conf_key" => array_get($this->confs,
                    $this->cardid.'.key'), "sites_id" => $this->siteid));

        $newc->sites_id = $this->siteid;
        $newc->conf_key = array_get($this->confs, $this->cardid.'.key');
        $newc->conf_val = array_get($this->confs, $this->cardid.'.value');
        $newc->theme    = array_get($this->confs, $this->cardid.'.theme');
        $newc->save();

        $this->cardid     = $newc->id;
        $this->actionSave = null;
    }

    protected function deleteConfiguration()
    {
        \Veer\Models\Configuration::destroy($this->cardid);
        $this->actionDelete = null;
    }
}
