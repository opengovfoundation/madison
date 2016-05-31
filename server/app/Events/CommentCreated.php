<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Annotation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentCreated extends Event
{
    use SerializesModels;

    public $comment;
    public $parent;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Annotation $comment, $parent)
    {
        $this->comment = $comment;
        $this->parent = $parent;
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
        return 'madison.comment.created';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
