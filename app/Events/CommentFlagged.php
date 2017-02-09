<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Annotation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentFlagged extends Event
{
    use SerializesModels;

    public $flag;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Annotation $flag)
    {
        $this->flag = $flag;
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
        return 'madison.comment.flagged';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
