<?php namespace Veer\Services\Administration;

use Veer\Services\VeerApp;

class Utility {
   
    protected $data;
    protected $action = null;
    
    public function __construct()
    {
        //Event::fire('router.filter: csrf');
        $this->data = \Input::all();
        $this->action = \Input::get('actionButton');
    }
    
    public function setParams($data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function setPingEmail($email)
    {
        $this->data['customPingEmail'] = $email;
        return $this;
    }
    
    public function setTable($tableName)
    {
        $this->data['tableName'] = $tableName;
        return $this;
    }
    
    public function handle()
    {
        if(in_array($this->action, ['runRawSql', 'checkLatestVersion', 'sendPingEmail', 'clearTrashed', 'clearCache'])) {
            return $this->{'action' . ucfirst($this->action)}();
        }
    }
    
    public function run()
    {
        return $this->handle();
    }
    
    // TODO: warning! very dangerous!
    protected function actionRunRawSql()
    {        
        $sql = array_get($this->data, 'freeFormSql');
        
		if(!empty($sql)) \DB::statement($sql);
    }
    
    public function actionCheckLatestVersion()
    {
        $latest = $this->_checkLatestVersion();
			
        // for ajax calls
        if(app('request')->ajax()) {
            return view('components.version', array(
                "latest" => $latest,
                "current" => VeerApp::VEERVERSION,
            ));		
        }
        
        return $latest;
    }
    
    public function actionSendPingEmail()
    {
        $pingEmail = array_get($this->data, 'customPingEmail', config('mail.from.address'));
        if(empty($pingEmail)) return null;
        
        \Mail::send('emails.ping', [], function($message) use ($pingEmail) {
            $message->to($pingEmail);
        });
    }
    
    public function actionClearTrashed()
    {
        $tableName = array_get($this->data, 'tableName');
        
        if(!empty($tableName)) {
            \Illuminate\Support\Facades\DB::table($tableName)
                    ->whereNotNull('deleted_at')->delete();
        }
    }
    
    public function actionClearCache()
    {
        \Cache::flush();
    }
    
    protected function _checkLatestVersion()
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->get(VeerApp::VEERCOREURL . "/releases", ['verify' => false]);
		$res = json_decode($response->getBody());
				
		return head($res)->tag_name;
	}
}
