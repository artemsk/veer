<?php namespace Veer\Services\Queuedb;

use Veer\Services\Queuedb\Job;
use Illuminate\Queue\SyncQueue;

class QdbQueue extends SyncQueue {
	
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 
     */
    public function push($job, $data = '', $queue = null)
    {
        $id = $this->addJob($job, $data);
		return $id;
    }

    /**
     * 
     */
    public function addJob($job, $data, $delay = null)
    {
        $payload = $this->createPayload($job, $data);

        $job = new Job();
        $job->status = Job::STATUS_OPEN;
        $job->available_at = empty($delay) ? \Carbon\Carbon::now() : $delay;
        $job->payload = $payload;
        $job->save();

        return $job->id;
    }

    /**
     *
     */
    public function later($delay, $job, $data = '', $queue = null, $type = 'minutes')
    {
		if($type == 'hours') { $delay = \Carbon\Carbon::now()->addHours($delay); }
		if($type == 'minutes') { $delay = \Carbon\Carbon::now()->addMinutes($delay); }
		
        $id = $this->addJob($job, $data, $delay);
        return $id;
    }

}
