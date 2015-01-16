<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

trait Configuration {
		
	protected $action_performed = array();	

	/**
	 * Show Etc.
	 */
	public function showEtc() 
	{		
		$cache = \Illuminate\Support\Facades\DB::table("cache")->get();
		$migrations = \Illuminate\Support\Facades\DB::table("migrations")->get();
		$reminders = \Illuminate\Support\Facades\DB::table("password_reminders")->get();	

		if(config('database.default') == 'mysql') {
			$trashed = $this->trashedElements(); }

		return array('cache' => $cache, 'migrations' => $migrations, 
			'reminders' => $reminders, 'trashed' => empty($trashed)? null : $trashed);
	}	
	
	
	protected function checkLatestVersion()
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->get(\Veer\VeerApp::VEERCOREURL . "/releases", array('verify' => false));
		$res = json_decode($response->getBody());
				
		return head($res)->tag_name;
	}
	
	
	/**
	 * Show trashedElements (only 'mysql')
	 * @param type $action
	 * @return type
	 */
	protected function trashedElements($action = null)
	{
		$tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
		
		$items = array();
		
		foreach($tables as $table) {
			if (\Illuminate\Support\Facades\Schema::hasColumn(reset($table), 'deleted_at'))
			{
				$check = \Illuminate\Support\Facades\DB::table(reset($table))
					->whereNotNull('deleted_at')->count();
				if($check > 0) {
				$items[reset($table)] = $check;				
					if($action == "delete") {
						\Illuminate\Support\Facades\DB::table(reset($table))
							->whereNotNull('deleted_at')->delete();
						$this->action_performed[] = "DELETE trashed";
					}				
				}
			}
		}
		return $items;
	}	
	
	
	/**
	 * Update Configuration
	 * @return void | (ajax?)view
	 */
	public function updateConfiguration() 
	{
		\Eloquent::unguard();
		
		$siteid = Input::get('siteid');
		$confs = Input::get('configuration');		
		$new = Input::get('new');
		
		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }
		
		if(!empty($siteid)) { 
		
			$save = Input::get('save');
			$delete = Input::get('dele');

			if(!empty($save) && !empty($confs[$cardid]['key'])) {
				$newc = \Veer\Models\Configuration::firstOrNew(array("conf_key" => $confs[$cardid]['key'], "sites_id" => $siteid));
				$newc->sites_id = $siteid;
				$newc->conf_key = $confs[$cardid]['key'];
				$newc->conf_val = $confs[$cardid]['value'];
				$newc->save();

				$cardid = $newc->id;
				$this->action_performed[] = "UPDATE configuration";
			}

			if(!empty($delete)) {
				\Veer\Models\Configuration::destroy($cardid);
				$this->action_performed[] = "DELETE configuration";
			}

			\Illuminate\Support\Facades\Artisan::call('cache:clear');
			$this->action_performed[] = "CLEAR cache";

			// for ajax calls
			if(app('request')->ajax()) {
				$items = $this->showConfiguration($siteid, array('id','desc'));

				return view(app('veer')->template.'.lists.configuration-cards', array(
					"configuration" => $items[0]->configuration,
					"siteid" => $siteid,
				));		
			}
				 
		} else { 
			Event::fire('veer.message.center', \Lang::get('veeradmin.error.reload')); 
		}
	}		
	
	
	/**
	 * Update Components
	 * @return void | (ajax?)view
	 */
	public function updateComponents() 
	{
		\Eloquent::unguard();		
		
		$siteid = Input::get('siteid');
		$confs = Input::get('components');
		$new = Input::get('new');
		
		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }
		
		if(!empty($siteid)) { 
		
			$save = Input::get('save');
			$delete = Input::get('dele');

			// We create new insert id every time
			if(!empty($save) && !empty($confs[$cardid]['name'])) {
				$newc = \Veer\Models\Component::firstOrNew(array("route_name" => $confs[$cardid]['name'], 
					"components_type" => $confs[$cardid]['type'], "components_src" => $confs[$cardid]['src'], "sites_id" => $siteid));
				$newc->route_name = $confs[$cardid]['name'];
				$newc->components_type = $confs[$cardid]['type'];
				$newc->components_src = $confs[$cardid]['src'];
				$newc->sites_id = $siteid;
				$newc->save();

				$cardid = $newc->id;
				$this->action_performed[] = "UPDATE component";
			}

			if(!empty($delete)) {
				\Veer\Models\Component::destroy($cardid);
				$this->action_performed[] = "DELETE component";
			}

			// for ajax calls
			if(app('request')->ajax()) {
				$items = $this->showComponents($siteid, array('id','desc'));
				
				return view(app('veer')->template.'.lists.components-cards', array(
					"components" => $items[0]->components,
					"siteid" => $siteid,
				));		
			}
				 // for error
		} else { 
			Event::fire('veer.message.center', \Lang::get('veeradmin.error.reload')); }
	}		
	

	/**
	 * Update Secrets
	 * @return void
	 */	
	public function updateSecrets() 
	{
		\Eloquent::unguard();	
		
		$save = Input::get('save');
		$delete = Input::get('dele');		
		$cardid = Input::get('secrets');
		
		if(!empty($delete)) {
			\Veer\Models\Secret::destroy(head(array_keys($delete)));
			Event::fire('veer.message.center', \Lang::get('veeradmin.secrets.delete'));
			$this->action_performed[] = "DELETE secret";
		}		

		if(!empty($save)) {			
			$id = head(array_keys($save));
			
			foreach($cardid as $key => $value) 
			{
				if($value['elements_id'] <= 0) { 
					Event::fire('veer.message.center', \Lang::get('veeradmin.secrets.error')); continue; }
				
				if($key != "new") {
					$newc = \Veer\Models\Secret::firstOrNew(array("id" => $id));
				} else {	
					$newc = new \Veer\Models\Secret;
				}
				$newc->secret = $value['pss'];
				$newc->elements_id = $value['elements_id'];
				$newc->elements_type = $value['elements_type'];
				$newc->save();
				$cardid = $newc->id;
				Event::fire('veer.message.center', \Lang::get('veeradmin.secrets.update'));
				$this->action_performed[] = "UPDATE secret";
			}	
		}
	}	
	
	
	/**
	 * Update Jobs
	 */	
	public function updateJobs()
	{
		$run = Input::get('_run');
		$delete = Input::get('dele');
		$save = Input::get('save');
		$pause = Input::get('paus');
		
		if(!empty($delete)) {
			
			\Artemsk\Queuedb\Job::destroy(head(array_keys($delete)));
			Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.delete'));
			$this->action_performed[] = "DELETE job";
		}

		if(!empty($run)) {
			
			$this->runJob( head(array_keys($run)) , Input::get('payload') );
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.done'));
			$this->action_performed[] = "RUN job";			
		}
		
		if(!empty($save)) {		
			
			if($this->saveJob(Input::all())) {	
				Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.new'));
				$this->action_performed[] = "NEW job";
			} else {		
				Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.error')); 
		}	}
		
		if(!empty($pause)) {
			
			\Artemsk\Queuedb\Job::where('id','=', head(array_keys($pause)) )
				->update(array('status' => \Artemsk\Queuedb\Job::STATUS_FINISHED));	
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.pause'));
			$this->action_performed[] = "PAUSE job";
		}		
	}
	
	
	/**
	 * save Job
	 * @param array $q
	 * @return boolean
	 */
	protected function saveJob($q)
	{
		$startc = \Carbon\Carbon::parse(array_get($q, 'jobs.new.start'));
		$repeat = array_get($q, 'jobs.new.repeat');
		$data =  (array)json_decode(array_get($q, 'jobs.new.data'), true);
		$queue = array_get($q, 'jobs.new.classname');

		if($repeat > 0) {
			$data['repeatJob'] = $repeat;
		}

		$classFullName = "\Veer\Queues\\" . $queue;

		if (!class_exists($classFullName)) { 
			//
		} else {			
			if(now() >= $startc) {
				\Queue::push( $classFullName , $data);
			} else {
				$wait = \Carbon\Carbon::now()->diffInMinutes($startc);
				\Queue::later($wait, $classFullName , $data);
			}
			return true;
		}
	}
	
	
	/**
	 * Run Job
	 * @param type $jobid
	 * @param type $payload
	 * @return void
	 */
	protected function runJob($jobid, $payload)
	{
		$item = \Artemsk\Queuedb\Job::where('id','=',$jobid)->first();	

		if(is_object($item)) {						
			$item->payload = $payload;
			$item->status = \Artemsk\Queuedb\Job::STATUS_OPEN;
			$item->scheduled_at = now();
			$item->save();

			$job = new \Artemsk\Queuedb\QdbJob(app(), $item);
			$job->fire();
		}			
	}

	
	/**
	 * update Etc
	 */
	public function updateEtc()
	{
		Event::fire('router.filter: csrf');
		
		$all = Input::all();
		$action = Input::get('action');
	
		if($action == "runRawSql" && array_get($all, 'freeFormSql') != null)
		{
			// TODO: warning! very dangerous!
			\DB::statement( array_get($all, 'freeFormSql') );
			Event::fire('veer.message.center', \Lang::get('veeradmin.etc.sql'));
			$this->action_performed[] = "RUN sql";
		}
	
		if(Input::get('actionButton') == "checkLatestVersion")
		{
			$latest = $this->checkLatestVersion();
			
			// for ajax calls
			if(app('request')->ajax()) {
				return view(app('veer')->template.'.elements.version', array(
					"latest" => $latest,
					"current" => \Veer\VeerApp::VEERVERSION,
				));		
			}
		}
		
		if(Input::get('actionButton') == "sendPingEmail" && config('mail.from.address') != null)
		{
			\Mail::send('emails.ping', array(), function($message)
			{
				$message->to(config('mail.from.address'));
			});
		}
		
		if(Input::get('actionButton') == "clearTrashed" && Input::get('button') != null)
		{
			\Illuminate\Support\Facades\DB::table(Input::get('button'))
				->whereNotNull('deleted_at')->delete();
		}
		
	}	
	
}
