<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Sponsor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SponsorCreated extends Event
{
    use SerializesModels;

    public $sponsor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Sponsor $sponsor)
    {
        $this->sponsor = $sponsor;
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
        return 'madison.sponsor.created';
    }

    public static function getType()
    {
        return static::TYPE_ADMIN;
    }
}
