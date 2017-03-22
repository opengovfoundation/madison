<?php

namespace App\Events;

use App\Models\Annotation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentCreated
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
}
