<?php

namespace App\Events;

use App\Models\Annotation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentLiked
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
}
