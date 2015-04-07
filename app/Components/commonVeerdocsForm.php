<?php

namespace Veer\Components;

class commonVeerdocsForm
{
    public $data;

	public function __construct() {

            $validate = \Validator::make(\Input::all(), array(
                    "email" => "required|email",
            ));

            if(!$validate->fails()) $this->storeEmails();
	}

	protected function storeEmails()
	{ 
            if($this->checkCsrf()) {
                app('files')->append( storage_path() . '/app/collectedEmails.' . date('Y.W', time()) . '.txt',
                        "\"".\Input::get('email') . "\":\"" . time() . "\","
                        );
                info('Added new email: ' . \Input::get('email'));
            }
	}

        protected function checkCsrf()
        {
            return (new \Veer\Commands\CsrfTokenMatchCommand())->handle();
        }
}
