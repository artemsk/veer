<?php

namespace Veer\Services\Administration\Elements;

class Comment extends MessageEntity {

    protected $model = \Veer\Models\Comment::class;
    protected $command = \Veer\Commands\CommentSendCommand::class;
    protected $type = 'comment';

    protected $inputKeys = ['hideComment' => 'hide', 'unhideComment' => 'unhide', 'deleteComment' => 'delete'];
    protected $addKey = 'addComment';
    protected $dataKey = null;

    protected $data;
    protected $action;
    protected $hide;
    protected $unhide;
    protected $delete;

}
