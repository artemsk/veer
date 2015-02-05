<?php namespace Veer\Queues;

class queueHelloWorld {

    public function fire($job, $data)
    {
        //
		app('files')->append(storage_path() . "/hello.txt", "Hello world! " .
			$data['message'] . " " . array_get(app('veer')->statistics, 'memory') . "\r\n");
		
		if(isset($data['repeatJob']) && $data['repeatJob'] > 0) {
			
			$job->release( $data['repeatJob'], 'minutes');
		}
    }

}