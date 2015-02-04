<?php namespace Artemsk\Queuedb;

use Illuminate\Database\Eloquent\Model;

class Job extends Model {
	
    const STATUS_OPEN = 0;
    const STATUS_WAITING = 1;
    const STATUS_STARTED = 2;
    const STATUS_FINISHED = 3;
	const STATUS_FAILED = 4;

    protected $table = 'jobs';
    protected $guarded = array('id', 'created_at', 'updated_at', 'scheduled_at');
	
}
