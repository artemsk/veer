<?php namespace Artemsk\Queuedb;

use Artemsk\Queuedb\Job;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\SyncJob;

class QdbJob extends SyncJob
{
    protected $job;
	
	protected $repeat = false;

    /**
     * 
     *
     * @return void
     */
    public function __construct(Container $container, Job $job)
    {
        $this->job = $job;
        $this->container = $container;
    }

    /**
     * 
     *
     * @return void
     */
    public function fire()
    {
        $payload = $this->parsePayload($this->job->payload);
		
        // If it is too early to fire job then return		
		if ($this->job->scheduled_at > \Carbon\Carbon::now()) {
			
            $this->job->status = Job::STATUS_WAITING;
            $this->job->save();
            return;
        }

        // Start status
        $this->job->status = Job::STATUS_STARTED;
		$this->job->increment('times');	
        $this->job->save();

        // Fire 	
        $this->resolveAndFire($payload);
		
        // If job is not deleted, mark as finished or waiting for released
        if (empty($this->repeat)) {	$this->job->status = Job::STATUS_FINISHED; }

		if(!$this->deleted) { $this->job->save(); }
    }

    /**
     * 
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();
        $this->job->delete();
    }

	/**
	 * 
	 *
	 * @param  int   $delay minutes|hourse|timestamp
	 * @return void
	 */
	public function release($delay = null, $type = 'minutes')
	{
		parent::release();

		if(!empty($delay)) {
			if($type == 'hours') { $delay = \Carbon\Carbon::now()->addHours($delay); }
			if($type == 'minutes') { $delay = \Carbon\Carbon::now()->addMinutes($delay); }
		} 		
		$this->job->scheduled_at = (empty($delay)) ? (\Carbon\Carbon::now()->addSeconds(15)) : $delay;
		$this->job->status = Job::STATUS_WAITING;
		$this->repeat = true;
	}
	
	/**
	 * 
	 *
	 * @param  int   $delay minutes|hourse|timestamp
	 * @return void
	 */
	public function fail()
	{
		$this->job->status = Job::STATUS_FAILED;
		$this->repeat = true;
	}
	
	/**
     * 
     *
     * @param string $payload
     * @return array|null
     */
    protected function parsePayload($payload)
    {
        return json_decode($payload, true);
    }
}
