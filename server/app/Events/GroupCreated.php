<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Group;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupCreated extends Event
{
    use SerializesModels;

    public $group;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
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
        return 'madison.group.created';
    }

    public static function getType()
    {
        return static::TYPE_ADMIN;
    }
}
