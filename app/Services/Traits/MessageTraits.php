<?php namespace Veer\Services\Traits;

trait MessageTraits {
	
	protected function setMessagingSource($object, $connected = null)
	{
		if(!empty($connected))
		{
			list($model, $id) = explode(":", $connected);
			
			$object->elements_type = elements($model);			
			$object->elements_id = $id;
		}
	}
		
	/**
	 * Parse message
	 * 
	 */
	protected function parseMessage($m)
	{
		$usernames = "/(@[^\\s]+)\\b/i"; // [@:id | @username]		
		$emails = "/(\\[[^\\s]+\\])/i"; // [email]
				
		preg_match_all($emails, $m, $matches_emails);

		$m = preg_replace($emails, "", $m);
		
		preg_match_all($usernames, $m, $matches_usernames);
		
		$m = preg_replace($usernames, "", $m);
		
		$m = preg_replace("/(\\s+)/i", " ", $m);

		return array( trim($m), 
			$this->getEmailsCollection($matches_emails), 
			$this->getUsernamesCollection($matches_usernames)
		);
	}
	
	/**
	 * get collection of emails
	 * 
	 */
	protected function getEmailsCollection($matches)
	{
		foreach($matches[0] as $match) { $emailsCollection[] = substr($match, 1, -1); }
		
		return isset($emailsCollection) ? $emailsCollection : null;
	}
	
	/**
	 * get collection of user ids
	 * 
	 */
	protected function getUsernamesCollection($matches)
	{
		foreach($matches[0] as $match)
		{ 
			$userId = $this->getUserId($match);
			
			if(!empty($userId)) $usernamesCollection[] = $userId;
		}
		
		return $usernamesCollection;
	}
	
	/**
	 * parse username to get userid
	 * 
	 */
	protected function getUserId($username)
	{
		if(starts_with($username, "@:")) return substr($username, 2); 
			
		return \Veer\Models\User::where('username','=', substr($username, 1))->pluck('id');
	}
	
	
	
}