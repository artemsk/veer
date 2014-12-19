<?php namespace Veer\Lib\Queues;

class queueUnhidePages {

	/* name */
	public $queueName = "Unhide Specific Pages";
	
	/* description */
	public $queueDescription = "Unhide specific pages in (array)ids when chosen time come.";
	
	
    public function fire($job, $data)
    {
        $ids = array_get($data, 'ids', null);
		
		if(empty($ids) || !is_array($ids)) {
			return $job->fail();
		}
		
		$notfound = true;
		
		foreach($ids as $id) {
			$page = \Veer\Models\Page::find($id);
			if(is_object($page)) {
				$page->hidden = false;
				$page->save();
				$notfound = false;
			} 
		}
		
		if($notfound == true) {
			return $job->fail();
		}
		
		// leave it here
		if(isset($data['repeatJob']) && $data['repeatJob'] > 0) {		
			$job->release( $data['repeatJob'], 'minutes');
		}
    }
	
	
}