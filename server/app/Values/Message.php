<?php

namespace App\Values;

use App\Models\User;

class Message
{
    public $subject;
    public $body;
    public $recipient;

    public function __construct($subject, $body, User $recipient)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->recipient = $recipient;
    }
}
