<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SponsorMemberRemoved extends Event
{
    use SerializesModels;

    public $sponsor;
    public $member;
    public $instigator;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Sponsor $sponsor, User $member, User $instigator)
    {
        $this->sponsor = $sponsor;
        $this->member = $member;
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
        return 'madison.sponsor.member-removed';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
