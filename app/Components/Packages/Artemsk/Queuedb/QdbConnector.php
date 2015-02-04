<?php namespace Artemsk\Queuedb;

use Artemsk\Queuedb\QdbQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class QdbConnector implements ConnectorInterface
{
    protected $defaults = array();

    /**
     * 
     *
     * @param array $config
     * @return \Illuminate\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        //$config = array_merge($this->defaults, $config);
        return new QdbQueue($config);
    }
}
