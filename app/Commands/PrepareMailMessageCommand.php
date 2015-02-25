<?php

namespace Veer\Commands;

use Veer\Commands\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class PrepareMailMessageCommand extends Command implements SelfHandling
{
    protected $object;
    protected $emails;
    protected $recipients;
    protected $type = "communication";

    /* title of sending place */
    protected $place;

    /* subject */
    protected $subject;

    /* link of sending place */
    protected $link;

    /* site url of sending place */
    protected $url;

    /**
     * Create a new command instance.
     *
     */
    public function __construct($data)
    {
        array_walk($data,
            function($values, $keys) {
            $this->{$keys} = $values;
        });
    }

    protected function isAllowed()
    {
        if (empty($this->emails) && empty($this->recipients)) return false;

        if (empty($this->object)) return false;

        return true;
    }

    /**
     * Execute the command.
     *
     */
    public function handle()
    {
        if (!$this->isAllowed()) return false;

        $this->{'get'.ucfirst($this->type).'Attributes'}();

        $this->getEntityInfo();

        $this->getRecipientsEmails();

        if (is_array($this->emails)) {
            (new \Veer\Commands\SendEmailCommand('emails.'.str_plural($this->type),
            $this->getDataReady(), $this->subject, array_unique($this->emails),
            null, $this->object->sites_id))->handle();
        }
    }
    /*
     * get subject & theme, sending url for communication
     */

    protected function getCommunicationAttributes()
    {
        $this->url = $this->object->site->url;

        if (!empty($this->object->theme)) {
            $this->subject = \Lang::get('veeradmin.emails.communication.subjectTheme',
                    array(
                    'url' => $this->url, 'theme' => $this->object->theme
            ));
        } else {
            $this->subject = \Lang::get('veeradmin.emails.communication.subject',
                    array('url' => $this->url));
        }
    }
    /*
     * get subject & sending url for comment
     */

    protected function getCommentAttributes()
    {
        $this->url = app('veer')->siteUrl;

        $this->subject = Lang::get('veeradmin.emails.comment.subject',
                array('url' => $this->url));
    }

    protected function getEntityInfo()
    {
        $elementsType = mb_strtolower(strtr($this->object->elements_type,
                array("\Veer\Models\\" => "")));

        if (in_array($elementsType,
                array("order", "page", "product", "category")))
                $this->getEntityTitle($elementsType);

        $this->link = $this->getEntityLink($elementsType);
    }

    protected function getEntityTitle($elementsType)
    {
        if ($elementsType != "order") {
            $this->place = isset($this->object->elements->title) ? $this->object->elements->title
                    : null;
        }

        if (is_object($this->object->elements)) {
            $this->place = "#".
                app('veershop')->getOrderId($this->object->elements->cluster,
                    $this->object->elements->cluster_oid);
        }
    }

    protected function getEntityLink($elementsType)
    {
        if ($elementsType == "order") return $this->url."/user/";

        if (in_array($elementsType, array("page", "product", "category")))
                return $this->url."/".$elementsType."/".$this->object->elements_id;

        return isset($this->object->url) ? $this->object->url : null;
    }

    protected function getRecipientsEmails()
    {
        if (is_array($this->recipients)) {
            foreach ($this->recipients as $userId) {
                $email          = \Veer\Models\User::where('id', '=', $userId)->pluck('email');
                if (!empty($email)) $this->emails[] = $email;
            }
        }
    }

    protected function getDataReady()
    {
        return array(
            "sender" => isset($this->object->author) ? $this->object->author : $this->object->sender,
            "txt" => isset($this->object->txt) ? $this->object->txt : $this->object->message,
            "place" => empty($this->place) ? $this->link : $this->place,
            "link" => $this->link
        );
    }
}
