<?php namespace Artemsk\Queuedb;

use Artemsk\Queuedb\QdbJob;
use Artemsk\Queuedb\Job;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class QdbCommand extends Command {
    /**
     * 
     *
     * @var string
     */
    protected $name = 'queue:qdb';

    /**
     * 
     *
     * @var string
     */
    protected $description = 'Run a queue from the database';

    /**
     * 
     *
     * @return void
     */
    public function fire()
    {
		if (empty($this->argument('job_id'))) {
			$item = Job::where('status', '!=', Job::STATUS_FINISHED)->orderBy('scheduled_at', 'asc')->first();
			if(count($item)<1) { echo "Queue is empty.\n"; } 
		} else {
			$item = Job::findOrFail($this->argument('job_id'));
		}
		
		if(count($item)>0) { 
			
		$job = new QdbJob($this->laravel, $item);

		$job->fire();
		
		}
		
		if (($this->option('stats'))) {
			$this->getStats();
		}
	}

    /**
     * 
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('job_id', InputArgument::OPTIONAL, 'Job Id. If empty will be used the first Id in a row.'),
        );
    }

    /**
     * 
     *
     * @return array
     */	
	protected function getOptions()
	{
		return array(
			array('stats', null, InputOption::VALUE_NONE, 'Statistics.', null),
		);
	}
	
	/**
	 * 
	 *
	 * @return void
	 */
	protected function getStats() 
	{
		$jobs = Job::select(\Illuminate\Support\Facades\DB::raw('count(*) as jobs_count, status'))->groupBy('status')->get();
				
		foreach($jobs as $j) {
			switch($j->status):
				case Job::STATUS_OPEN:
					echo 'Open - ';
					break;
				case Job::STATUS_WAITING:
					echo 'Waiting - ';
					break;
				case Job::STATUS_STARTED:
					echo 'Started - ';
					break;
				case Job::STATUS_FINISHED:
					echo 'Done - ';
					break;		
				case Job::STATUS_FAILED:
					echo 'Failed - ';
					break;				
			endswitch;
			echo $j->jobs_count. " \n"; 
		}			
	}
	
	
}
