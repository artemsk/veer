<?php namespace Veer\Services\Administration;

class Job {
    
    protected $data;
    protected $actionSave   = null;
    protected $actionDelete = null;
    protected $actionPause  = null;
    protected $actionRun    = null;
    
    public function __construct()
    {
        $this->data = \Input::all();
        $this->actionDelete = \Input::get('dele');
        $this->actionSave   = \Input::get('save');
        $this->actionPause  = \Input::get('paus');
        $this->actionRun    = \Input::get('_run');
    }
    
    public function setParams($data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function pushDelete($delete)
    {
        $this->actionDelete = $delete;
        return $this;
    }

    public function pushUpdate($update = true)
    {
        $this->actionSave = $update;
        return $this;
    }
    
    public function pushPause($pause)
    {
        $this->actionPause = $pause;
        return $this;
    }

    public function pushRun($run)
    {
        $this->actionRun = $run;
        return $this;
    }
    
    public function handle()
    {
        foreach(['Delete', 'Save', 'Run', 'Pause'] as $action) {
            if(!empty($this->{'action' . $action})) {
                return $this->{lcfirst($action) . 'Job'}();
            }
        }
    }
    
    protected function deleteJob()
    {
        \Veer\Services\Queuedb\Job::destroy(head(array_keys($this->actionDelete)));
        $this->actionDelete = null;
    }
    
    public function saveJob()
    {
        $q = $this->data;
        $startc = \Carbon\Carbon::parse(array_get($q, 'jobs.new.start'));
		$repeat = array_get($q, 'jobs.new.repeat');
		$data =  (array)json_decode(array_get($q, 'jobs.new.data'), true);
		$queue = array_get($q, 'jobs.new.classname');

		if($repeat > 0) {
			$data['repeatJob'] = $repeat;
		}

        $classFullName = starts_with($queue, "\\") ? $queue : "\Veer\Queues\\" . $queue;

		if (!class_exists($classFullName)) { 
			//
		} else {			
			if(now() >= $startc) {
				\Queue::push( $classFullName , $data);
			} else {
				$wait = \Carbon\Carbon::now()->diffInMinutes($startc);
				\Queue::later($wait, $classFullName , $data);
			}
		}
        $this->actionSave = null;
    }
    
    protected function runJob()
    {
        $jobid = head(array_keys($this->actionRun));
        $payload = array_get($this->data, 'payload');
            
        $item = \Veer\Services\Queuedb\Job::where('id','=',$jobid)->first();	

		if(is_object($item)) {						
			$item->payload = $payload;
			$item->status = \Veer\Services\Queuedb\Job::STATUS_OPEN;
			$item->available_at = now();
			$item->save();

			$job = new \Veer\Services\Queuedb\QdbJob(app(), $item);
			$job->fire();
		}
        $this->actionRun = null;
    }
    
    protected function pauseJob()
    {
        \Veer\Services\Queuedb\Job::where('id','=', head(array_keys($this->actionPause)))
				->update(['status' => \Veer\Services\Queuedb\Job::STATUS_FINISHED]);
        $this->actionPause = null;
    }
}
