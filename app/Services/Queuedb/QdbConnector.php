<?php namespace Veer\Services\Queuedb;

use Veer\Services\Queuedb\QdbQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class QdbConnector implements ConnectorInterface {
	
    /**
     * Establish a queue connection.
     *
     */
    public function connect(array $config)
    {
        return new QdbQueue($config);
    }
}
