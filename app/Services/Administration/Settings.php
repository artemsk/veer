<?php namespace Veer\Services\Administration;

class Settings {
    
    protected $siteid;
    protected $confs;
    protected $new;
    protected $actionSave   = null;
    protected $actionDelete = null;
    protected $cardid;
    
    // model, data field, key, firstOrNew, additional fields, method, view, skip ajax 
    protected $dataProvider = [
        'components' => ['Component', 'components', 'name', 
            ['route_name' => 'name', 'components_type' => 'type', 'components_src' => 'src', 'sites_id' => ''], ['theme' => 'theme'], 
            'getComponents', 'components-cards'],
        'configuration' => ['Configuration', 'configuration', 'key', 
            ['conf_key' => 'key', 'sites_id' => ''], ['conf_val' => 'value', 'theme' => 'theme'], 'getConfiguration', 'configuration-cards'],
        'secrets' => ['Secret', 'secrets', 'elements_id', ['id' => 'id'], 
            ['secret' => 'pss', 'elements_id' => 'elements_id', 'elements_type' => 'elements_type'], 'getSecrets', '', true] // no ajax
    ];
    protected $data;

    public function __construct($type = 'configuration')
    {
        $this->data = $this->dataProvider[$type];
        
        $this->siteid       = \Input::get('siteid');
        $this->confs        = \Input::get($this->data[1]);
        $this->new          = \Input::get('new');
        $this->actionDelete = \Input::get('dele');
        $this->actionSave   = \Input::get('save');
    }

    public function setParams($siteid, $confs = null, $new = null)
    {
        $this->siteid = $siteid;
        $this->confs  = $confs;
        $this->new    = $new;
        return $this;
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
        if (!empty($this->siteid) || array_get($this->data, 7) === true) return true;

        \Event::fire('veer.message.center', \Lang::get('veeradmin.error.reload'));

        return false;
    }

    public function pushDelete($delete)
    {
        $this->actionDelete = $delete;
        return $this;
    }

    public function pushUpdate($update)
    {
        $this->actionSave = $update;
        return $this;
    }

    /**
     * main entry point
     */
    public function handle()
    {
        $this->prepareConfs();

        if ($this->isSiteSet()) {

            if (!empty($this->actionDelete)) $this->delete();

            if (!empty($this->actionSave) && null != array_get($this->confs,
                    $this->cardid.'.'.$this->data[2])) $this->update();

            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            //if (app('request')->ajax() && !empty($this->data[6])) return $this->ajaxRequest();
        }
    }
    
    /** 
     * alias 
     */
    public function run() 
    {
        return $this->handle();
    }

    protected function ajaxRequest()
    {
        $items = (new \Veer\Services\Show\Site)->{$this->data[5]}($this->siteid,
            array('id', 'desc'));

        /* for admin we always use 'view' instead of 'viewx' */
        return view(app('veer')->template.'.lists.'.$this->data[6],
            array(
            $this->data[1] => $items[0]->{$this->data[1]},
            "siteid" => $this->siteid,
        ));
    }

    protected function update()
    {
        \Eloquent::unguard();

        $className = '\\Veer\\Models\\' . $this->data[0];
        
        foreach($this->data[3] as $db_field => $input_field) {
            $params[$db_field] = $db_field == 'sites_id' ? $this->siteid : 
                array_get($this->confs, $this->cardid.'.'.$input_field);
        }

        $newc = (count($params) == 1 && isset($params['id']) && empty($params['id'])) ? 
            new $className : $className::firstOrNew($params);        
        unset($params['id']);
        
        foreach($params as $key => $value) { $newc->{$key} = $value; }
        foreach($this->data[4] as $key => $value) { $newc->{$key} = array_get($this->confs, $this->cardid.'.'.$value); }
        $newc->save();

        $this->cardid = $newc->id;
        $this->actionSave = null;
    }

    protected function delete()
    {
        $className = '\\Veer\\Models\\' . $this->data[0];
        $className::destroy($this->cardid);
        $this->actionDelete = null;
    }
}
