<?php namespace Veer\Commands;

use Veer\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class CommentSendCommand extends Command implements SelfHandling {

	use \Veer\Services\TemporaryTrait;
	
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
		
		array_set_empty($this->data, 'fill.users_id', \Auth::id());
		
		$this->getAuthorName();
		
		$this->getVotes();
		
		\Eloquent::unguard();
		
		$comment = new \Veer\Models\Comment;
		$comment->fill( array_get($this->data, 'fill') );
		$comment->hidden = array_get($this->options, 'checkboxes.hidden', false);	
		
		$this->getMessagingSource($comment, array_get($this->data, 'connected'));
			
		list(, $emails, $recipients) = $this->parseMessage( array_get($this->data, 'fill.txt') );
		
		$comment->save();
		
		if(!empty($emails) || !empty($recipients))
		{
			$this->message2mail($comment, $emails, $recipients, 'comment');
		}
		
		return true;
	}

	protected function getAuthorName()
	{
		if(array_get($this->data, 'fill.users_id') != null)
		{
			array_set_empty($this->data, 'fill.author', \Auth::user()->username);		
		}
	}
	
	protected function getVotes()
	{
		if(array_get($this->data, 'vote') == "Yes") array_set($this->data, 'fill.vote_y', true);
		
		if(array_get($this->data, 'vote') == "No") array_set($this->data, 'fill.vote_n', true);
	}
	
}
