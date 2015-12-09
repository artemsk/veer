<?php

namespace Veer\Services\Administration\Elements;

class Communication extends MessageEntity {

    protected $model = \Veer\Models\Communication::class;
    protected $command = \Veer\Commands\CommunicationSendCommand::class;
    protected $type = 'communication';

    protected $inputKeys = ['hideMessage' => 'hide', 'unhideMessage' => 'unhide', 'deleteMessage' => 'delete'];
    protected $addKey = 'addMessage';
    protected $dataKey = 'communication';

    protected $data;
    protected $action;
    protected $hide;
    protected $unhide;
    protected $delete;

}
