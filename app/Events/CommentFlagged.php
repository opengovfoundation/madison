<?php

namespace App\Events;

use App\Models\Annotation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentFlagged
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
}
