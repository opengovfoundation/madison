<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Annotation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentLiked extends Event
{
    use SerializesModels;

    public $like;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Annotation $like)
    {
        $this->like = $like;
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
        return 'madison.comment.liked';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
