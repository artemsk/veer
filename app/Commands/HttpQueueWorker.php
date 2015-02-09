<?php namespace Veer\Commands;

use Veer\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class HttpQueueWorker extends Command implements SelfHandling {

	protected $driverMethod;
	
	protected $activeJob;
	
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($driver)
	{
		$this->driverMethod = 'runQueue'.ucfirst($driver);
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		if(method_exists($this, $this->driverMethod)) return $this->{$this->driverMethod}();
	}
	
	/*
	 * Queue with Qdb driver 
	 */
	protected function runQueueQdb()
	{		
		if(!$this->isAllowed()) return false;
		
		if(is_object($this->getQdbJob()))
		{
			(new \Artemsk\Queuedb\QdbJob(app(), $this->activeJob))->fire();
		}
		
		$this->setChecked(config('veer.repeatjob'));
	}
	
	/*
	 * Is Allowed to work?
	 */
	protected function isAllowed()
	{
		if(!\Cache::has('queue_checked')) return true;
	}
	
	/*
	 * Set checked mark to cache
	 */
	protected function setChecked($period = 5)
	{
		\Cache::put('queue_checked', true, $period);
	}
	
	/*
	 * Get Qdb Job from database
	 */
	protected function getQdbJob()
	{
		$this->activeJob = \Artemsk\Queuedb\Job::where('status','<=','1')
			->where('scheduled_at','<=',date('Y-m-d H:i:00', time()))
			->orderBy('scheduled_at', 'asc')
			->first();
		
		return $this->activeJob;
	}

}
