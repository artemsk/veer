<?php

namespace Veer\Commands;

use Veer\Commands\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class CommunicationSendCommand extends Command implements SelfHandling
{

    use \Veer\Services\Traits\MessageTraits;
    protected $data;
    protected $options;

    /**
     * Create a new command instance.
     *
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

        if (array_get($this->data, 'message') == null) return false;

        list($text, $emails, $recipients) = $this->parseMessage(array_get($this->data,
                'message'));

        $message = $this->saveCommunication($text, $recipients);

        if ($message->email_notify == true || !empty($emails)) {
            ( new \Veer\Commands\PrepareMailMessageCommand(array(
            "object" => $message,
            "emails" => $emails,
            "recipients" => $recipients)))->handle();
        }

        return $message->id;
    }
    /*
     * saving Communication to db
     * 
     */

    protected function saveCommunication($text, $recipients)
    {
        \Eloquent::unguard();

        $message = new \Veer\Models\Communication;

        $this->setParameters($message);

        $this->setMessagingSource($message, array_get($this->data, 'connected'));

        $message->fill(array_get($this->data, 'fill'));

        $message->message = $text;

        $message->recipients = json_encode($recipients);

        $message->save();

        return $message;
    }
    /*
     * set parameters
     * 
     */

    protected function setParameters($message)
    {
        $this->setAuthorName(array_get($this->data, 'fill.users_id'));

        array_set_empty($this->data, 'fill.users_id', \Auth::id());

        array_set_empty($this->data, 'fill.sites_id', app('veer')->siteId);

        if (array_get($this->data, 'fill.url') != null || empty($message->elements_id)) {
            array_set_empty($this->data, 'fill.url', app('url')->current());
        }

        $message->public = $this->checkboxesValidate('checkboxes.public');

        $message->email_notify = $this->checkboxesValidate('checkboxes.email_notify');

        $message->hidden = $this->checkboxesValidate('checkboxes.hidden');

        $message->intranet = $this->checkboxesValidate('checkboxes.intranet');
    }
    /*
     * get checkboxes values right
     * 
     */

    protected function checkboxesValidate($key)
    {
        $checkboxDefault = array_get($this->options, $key, false);

        return array_get($this->data, $key, $checkboxDefault) ? true : false;
    }
    /*
     * set author name and data
     *
     */

    protected function setAuthorName($userId)
    {
        if (!empty($userId)) {
            array_set_empty($this->data, 'fill.sender', \Auth::user()->username);
            array_set_empty($this->data, 'fill.sender_phone',
                \Auth::user()->phone);
            array_set_empty($this->data, 'fill.sender_email',
                \Auth::user()->email);
        }
    }
}
