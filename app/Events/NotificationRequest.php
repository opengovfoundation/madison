<?php

namespace App\Events;

use App\Events\Event;
use App\Values\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationRequest extends Event
{
    use SerializesModels;

    public $message;
    public $event;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, Event $event)
    {
        $this->message = $message;
        $this->event = $event;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    public static function getName()
    {
        return 'madison.notification.request';
    }

    public static function getDescription()
    {
        return 'When a notification is generated to be sent';
    }
}
