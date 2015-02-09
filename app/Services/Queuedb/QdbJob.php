<?php namespace Veer\Services\Queuedb;

use Veer\Services\Queuedb\Job;
use Illuminate\Container\Container;

class QdbJob extends \Illuminate\Queue\Jobs\SyncJob {
	
	/**
	 * The class name of the job.
	 *
	 * @var string
	 */
	protected $job;

	/**
	 * The queue message data.
	 *
	 * @var string
	 */
	protected $payload;
	
	protected $repeat = false;
	
    /**
     * 
     */
    public function __construct(Container $container, Job $job)
    {
        $this->job = $job;
        $this->container = $container;
    }

	/**
     * 
     */
    public function fire()
    {
        $payload = $this->parsePayload($this->job->payload);
		
        // If it is too early to fire job then return		
		if ($this->job->available_at > \Carbon\Carbon::now()) {
			
            $this->job->status = Job::STATUS_WAITING;
            $this->job->save();
            return;
        }

        // Start status
        $this->job->status = Job::STATUS_STARTED;
		$this->job->increment('attempts');	
        $this->job->save();

        // Fire 	
        $this->resolveAndFire($payload);
		
        // If job is not deleted, mark as finished or waiting for released
        if (empty($this->repeat)) {	$this->job->status = Job::STATUS_FINISHED; }

		if(!$this->deleted) { $this->job->save(); }
    }


	/**
	 * Get the raw body string for the job.
	 */
	public function getRawBody()
	{
		//
	}

    /**
     * 
     */
    public function delete()
    {
        parent::delete();
        $this->job->delete();
    }
	
	/**
	 * 
	 */
	public function release($delay = null, $type = 'minutes')
	{
		parent::release($delay);

		if(!empty($delay)) {
			if($type == 'hours') { $delay = \Carbon\Carbon::now()->addHours($delay); }
			if($type == 'minutes') { $delay = \Carbon\Carbon::now()->addMinutes($delay); }
		} 		
		$this->job->available_at = (empty($delay)) ? (\Carbon\Carbon::now()->addSeconds(15)) : $delay;
		$this->job->status = Job::STATUS_WAITING;
		$this->repeat = true;
	}	
	
	/**
	 * 
	 */
	public function fail()
	{
		$this->job->status = Job::STATUS_FAILED;
		$this->repeat = true;
	}	

	/**
	 * Get the number of times the job has been attempted.
	 *
	 */
	public function attempts()
	{
		return 1;
	}

	/**
	 * Get the job identifier.
	 *
	 */
	public function getJobId()
	{
		return '';
	}

	/**
     * 
     */
    protected function parsePayload($payload)
    {
        return json_decode($payload, true);
    }

}
