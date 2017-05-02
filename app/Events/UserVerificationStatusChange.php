<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserVerificationStatusChange
{
    use SerializesModels;

    public $oldValue;
    public $newValue;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($oldValue, $newValue, User $user)
    {
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
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
}
