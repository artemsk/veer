<?php

namespace Veer\Services\Administration\Elements;

use Illuminate\Support\Facades\Input;

class MessageEntity {

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

    public function __construct()
    {
        $this->data = empty($this->dataKey) ? Input::all() : Input::get($this->dataKey);
        $this->action = Input::get('action');

        foreach($this->inputKeys as $key => $var) {
            if(Input::has($key)) $this->{$var} = head(Input::get($key, []));
        }
    }

    public function run()
    {
        if($this->action == $this->addKey) return $this->add();
        
        if($this->hide) return $this->hide();

        if($this->unhide) return $this->unhide();
        
        if($this->delete) return $this->delete();
    }

    protected function add() // action=addMessage
    {
        event('veer.message.center', trans('veeradmin.' . $this->type . '.new'));

        $command = $this->command;
        return (new $command($this->data))->handle();
    }

    protected function hide() // hideMessage=id
    {
        $model = $this->model;
        $model::where('id', '=', $this->hide)->update(['hidden' => true]);

        event('veer.message.center', trans('veeradmin.' . $this->type . '.hide'));
    }

    protected function unhide() // unhideMessage
    {
        $model = $this->model;
        $model::where('id', '=', $this->unhide)->update(['hidden' => false]);

        event('veer.message.center', trans('veeradmin.' . $this->type . '.unhide'));
    }

    protected function delete()
    {
        $model = $this->model;
        $model::where('id', '=', $this->delete)->delete();
        
		event('veer.message.center', trans('veeradmin.' . $this->type . '.delete'));
    }
}
