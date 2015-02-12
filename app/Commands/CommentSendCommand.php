<?php namespace Veer\Commands;

use Veer\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class CommentSendCommand extends Command implements SelfHandling {

	use \Veer\Services\TemporaryTrait, \Veer\Services\Traits\MessageTraits;
	
	protected $data;
	
	protected $options;
	
	/**
	 * Create a new command instance.
	 *
	 * @params array $data request
	 * @params array $options
	 */
	public function __construct($data, $options = null)
	{
		$this->data = $data;
		
		$this->options = $options;
	}

	/**
	 * Execute the command.
	 *
	 */
	public function handle()
	{
		\Event::fire('router.filter: csrf');
		
		if(array_get($this->data, 'fill.txt') == null) return false;
			
		$comment = $this->saveComment();
		
		list(, $emails, $recipients) = $this->parseMessage( array_get($this->data, 'fill.txt') );
		
		$this->message2mail($comment, $emails, $recipients, 'comment');
		
		return $comment->id;
	}

	protected function saveComment()
	{
		\Eloquent::unguard();
		
		$comment = new \Veer\Models\Comment;
		
		$this->setParameters();
		
		$comment->fill( array_get($this->data, 'fill') );
		
		$comment->hidden = array_get($this->options, 'checkboxes.hidden', false);		
		
		$this->setMessagingSource($comment, array_get($this->data, 'connected'));
		
		$comment->save();
		
		return $comment;
	}
	
	protected function setParameters()
	{
		array_set_empty($this->data, 'fill.users_id', \Auth::id());
		
		$this->setAuthorName(array_get($this->data, 'fill.users_id'));
		
		$this->setVotes();
	}
	
	protected function setAuthorName($userId)
	{
		if(!empty($userId))
		{
			array_set_empty($this->data, 'fill.author', \Auth::user()->username);		
		}
	}
	
	protected function setVotes()
	{
		if(array_get($this->data, 'vote') == "Yes") array_set($this->data, 'fill.vote_y', true);
		
		if(array_get($this->data, 'vote') == "No") array_set($this->data, 'fill.vote_n', true);
	}
	
}
