<?php namespace Veer\Services;

trait TemporaryTrait {
	
	/**
	 * Sending mails queue
	 * 
	 */
	protected function message2mail($object, $emails = null, $recipients = null, $type = "communication")
	{		
		if(empty($emails) && empty($recipients)) return false;
		
		$place = $link = $subject = null;
		
		if($type == "communication") {
			$siteUrl = $object->site->url;
			
			if(!empty($object->theme)) 
			{ 
				$subject = \Lang::get('veeradmin.emails.communication.subjectTheme', array(
					'url' => $siteUrl, 'theme' => $object->theme
				)); 
			}
			else {	$subject = \Lang::get('veeradmin.emails.communication.subject', array('url' => $siteUrl)); }
		}
		
		if($type == "comment") {
			$siteUrl = app('veer')->siteUrl;
			$subject = \Lang::get('veeradmin.emails.comment.subject', array('url' => $siteUrl));
		}
		
		switch ($object->elements_type) 
		{
			case "Veer\Models\Product":
				$place = isset($object->elements->title) ? $object->elements->title : null;
				$link = $siteUrl . "/product/" . $object->elements_id;
			break;

			case "Veer\Models\Page":
				$place = isset($object->elements->title) ? $object->elements->title : null;
				$link = $siteUrl . "/page/" . $object->elements_id;
			break;

			case "Veer\Models\Category":
				$place = isset($object->elements->title) ? $object->elements->title : null;
				$link = $siteUrl . "/category/" . $object->elements_id;
			break;

			case "Veer\Models\Order":
				if(is_object($object->elements)) { $place = "#" . 
					app('veershop')->getOrderId($object->elements->cluster, $object->elements->cluster_oid); }
				$link = $siteUrl . "/user/";
			break;

			default:
				$place = $link = $object->url;
			break;
		}

		if(empty($place) && !empty($link)) $place = $link;
		
		$data = array(
			"sender" => isset($object->author) ? $object->author : $object->sender,
			"txt" => isset($object->txt) ? $object->txt : $object->message,
			"place" => $place,
			"link" => $link
		);
		
		$from = $this->getEmailFrom($object->sites_id);
		
		if(is_array($recipients))
		{
			foreach($recipients as $userId)
			{
				$email = \Veer\Models\User::where('id','=',$userId)->pluck('email');
				if(!empty($email)) $emails[] = $email;
			}
		}
		
		if(is_array($emails))
		{
			foreach(array_unique($emails) as $email)
			{
				$this->basicEmailSendQueue('emails.'.str_plural($type), $data, $from, $email, $subject);
			}
		}
	}
	
	/**
	 * Get 'From' field values for specific site
	 */
	public function getEmailFrom($siteId = null)
	{
		return array(
			"address" => db_parameter("EMAIL_ADDRESS", config("mail.from.address"), $siteId),
			"name" => db_parameter("EMAIL_NAME", config("mail.from.name"), $siteId)
		);
	}
	
	
	/**
	 * Basic Email Send Queue
	 */
	public function basicEmailSendQueue($view, $data, $from = null, $to = null, $subject = null)
	{
		if(empty($to)) return false;
		
		\Mail::queue($view, $data, function($message) use ($from, $to, $subject)
		{
			if(!empty($from)) $message->from($from['address'], $from['name']);
			$message->to($to);
			if(!empty($subject)) $message->subject($subject);
		});
		
		return true;
	}
	
}
