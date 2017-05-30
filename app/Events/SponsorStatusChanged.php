<?php

namespace App\Events;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SponsorStatusChanged
{
    use SerializesModels;

    public $oldValue;
    public $newValue;
    public $sponsor;
    public $instigator;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($oldValue, $newValue, Sponsor $sponsor, User $instigator)
    {
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
        $this->sponsor = $sponsor;
        $this->instigator = $instigator;
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
