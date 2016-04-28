<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedbackSeen extends Event
{
    use SerializesModels;

    public $feedback;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($feedback, User $user)
    {
        $this->feedback = $feedback; // either an Annotation or Comment
        $this->user = $user;
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
        return 'madison.feedback.seen';
    }

    public static function getDescription()
    {
        return 'When feedback (an annotation or comment) of yours is seen by the document sponsor';
    }
}
