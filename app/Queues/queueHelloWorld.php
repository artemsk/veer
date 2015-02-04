<?php namespace Veer\Queues;

class queueHelloWorld {

    public function fire($job, $data)
    {
        //
		app('files')->put(storage_path() . "/hello.txt", "Hello world! " . $data['message'] );
		
		if(isset($data['repeatJob']) && $data['repeatJob'] > 0) {
			
			$job->release( $data['repeatJob'], 'minutes');
		}
    }

}