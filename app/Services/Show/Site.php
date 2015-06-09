<?php namespace Veer\Services\Show;

class Site {
	
	use \Veer\Services\Traits\SortingTraits;
	
	protected $site = null;
		
	/**
	 * handle
	 */
	public function handle()
	{
		return $this->getSites();
	}
	
	/**
	 * get Sites
	 */
	public function getSites() 
	{
            app('veer')->online = $this->getUsersOnline();

            return \Veer\Models\Site::orderBy('manual_sort','asc')->get();
	}
	
	/*
	 * get Site Id
	 */
	public function getSiteId()
	{
		return !empty($this->site) ? $this->site : null;
	}
	
	/*
	 * set Site
	 */
	public function setSite($id = null)
	{
		$this->site = empty($id) ? app('veer')->siteId : $id;
	}
		
	/**
	 * 
	 */
	protected function getConfigurationOrComponent($type = 'configuration', $siteId = null, $orderBy = array('id', 'desc')) 
	{	
		$orderBy = $this->replaceSortingBy($orderBy);
		
		if(empty($siteId)) $items = \Veer\Models\Site::select();
		
		else $items = \Veer\Models\Site::where('id', '=', $siteId);
		
		$items = $items->with(array($type => function($query) use ($orderBy, $type) 
		{
			if($type == 'components') $query->orderBy('sites_id');
			$query->orderBy('theme', 'asc')->orderBy($orderBy[0], $orderBy[1]);
		}))->get();
       
		return $items;
	}	
	
	/**
	 * Show Configurations
	 */
	public function getConfiguration($siteId = null, $orderBy = array('id', 'desc')) 
	{            
                return $this->getConfigurationOrComponent('configuration', $siteId, $orderBy);
	}	
	
	/**
	 * Show Components
	 */
	public function getComponents($siteId = null, $orderBy = array('id', 'desc')) 
	{	
		return $this->getConfigurationOrComponent('components', $siteId, $orderBy);
	}	
	
	/**
	 * Show Secrets
	 */
	public function getSecrets() 
	{		
		return \Veer\Models\Secret::orderBy('created_at', 'desc')->get();
	}		
	
	/**
	 * Show Jobs
	 */
	public function getQdbJobs() 
	{		
		$items = \Veer\Services\Queuedb\Job::all();
		
		$items = $items->sortBy('available_at');
		
		$items_failed = \DB::table("failed_jobs")->get();
		
		$statuses = array(
			\Veer\Services\Queuedb\Job::STATUS_OPEN => "Open",
			\Veer\Services\Queuedb\Job::STATUS_WAITING => "Waiting",
			\Veer\Services\Queuedb\Job::STATUS_STARTED => "Started",
			\Veer\Services\Queuedb\Job::STATUS_FINISHED => "Finished",
			\Veer\Services\Queuedb\Job::STATUS_FAILED => "Failed"
		);
			
		return array(
			'jobs' => $items, 
			'failed' => $items_failed, 
			'statuses' => $statuses
		);
	}	

        protected function getUsersOnline()
        {
            /**
             * TODO: only for 'file' session driver (for now)
             */
            $sessions    = \File::allFiles(base_path()."/storage/framework/sessions");
            $fiveminutes = time() - (5 * 60);
            $counted     = 0;

            foreach ($sessions as $s) {

                $lastmodified = filemtime(array_get(pathinfo($s), 'dirname').'/'.array_get(pathinfo($s),
                        'basename'));

                if ($lastmodified >= $fiveminutes) $counted++;
            }

            return $counted;
        }
}
