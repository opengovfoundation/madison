<?php

namespace App\Events;

use App\Events\Event;
use App\Models\SponsorMember;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SponsorMemberRoleChanged extends Event
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

    public static function getName()
    {
        return 'madison.sponsor.member-role-changed';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
