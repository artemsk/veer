<?php namespace Veer\Services\Queuedb;

use Veer\Services\Queuedb\QdbJob;
use Veer\Services\Queuedb\Job;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class QdbCommand extends Command {
	
    /**
     * 
     */
    protected $name = 'queue:qdb';

    /**
     * 
     */
    protected $description = 'Run a queue from the database';

    /**
     * 
     */
    public function fire()
    {
		$jobid = $this->argument('job_id');
		if (empty($jobid)) {
			$item = Job::where('status', '!=', Job::STATUS_FINISHED)->orderBy('available_at', 'asc')->first();
			if(count($item)<1) { $this->info("Queue is empty."); } 
		} else {
			$item = Job::findOrFail($this->argument('job_id'));
		}
		
		if(count($item)>0) { 
			
		$job = new QdbJob($this->laravel, $item);

		$job->fire();
		
		}
		
		if (($this->option('stats'))) {
			$this->info($this->getStats());
		}
	}

    /**
     * 
     */
    protected function getArguments()
    {
        return array(
            array('job_id', InputArgument::OPTIONAL, 'Job Id. If empty will be used the first Id in a row.'),
        );
    }

    /**
     * 
     */	
	protected function getOptions()
	{
		return array(
			array('stats', null, InputOption::VALUE_NONE, 'Statistics.', null),
		);
	}
	
	/**
	 * 
	 */
	protected function getStats() 
	{
		$jobs = Job::select(\Illuminate\Support\Facades\DB::raw('count(*) as jobs_count, status'))->groupBy('status')->get();
		$o = "";		
		foreach($jobs as $j) {
			switch($j->status):
				case Job::STATUS_OPEN:
					$o.= 'Open - ';
					break;
				case Job::STATUS_WAITING:
					$o.= 'Waiting - ';
					break;
				case Job::STATUS_STARTED:
					$o.= 'Started - ';
					break;
				case Job::STATUS_FINISHED:
					$o.= 'Done - ';
					break;		
				case Job::STATUS_FAILED:
					$o.= 'Failed - ';
					break;				
			endswitch;
			$o.= $j->jobs_count. " \n"; 
		}			
		return $o;
	}

}
