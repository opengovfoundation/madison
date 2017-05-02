<?php

namespace App\Events;

use App\Models\SponsorMember;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SponsorMemberRoleChanged
{
    use SerializesModels;

    public $oldValue;
    public $newValue;
    public $sponsorMember;
    public $instigator;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($oldValue, $newValue, SponsorMember $sponsorMember, User $instigator)
    {
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
        $this->sponsorMember = $sponsorMember;
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
