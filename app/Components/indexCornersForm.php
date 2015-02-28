<?php
namespace Veer\Components;

class indexCornersForm { 
	
	public $data;
	
	public function __construct() {
	
		$validate = \Validator::make(\Input::all(), array(
			"email" => "required|email",
			"contactName" => "required",
		));
		
		if($validate->fails()) $this->failedForm($validate);
		
		else $this->storeEmails();
	}
	
	/* save fail messages */
	protected function failedForm($validate)
	{
		\Event::fire('veer.message.center', implode('<br>', $validate->messages()->all()));
	}
	
	protected function storeEmails()
	{
		app('files')->append( storage_path() . '/app/emails.' . date('Y.W', time()) . '.txt', 
			\Input::get('email') . ";;;" . \Input::get('contactName') . ";;;" . \Input::get('phone') . "\r\n"
			);
		info('Added new email: ' . \Input::get('email'));
		
		\Event::fire('veer.message.center', \Lang::get('corners.email.success'));
	}
}
