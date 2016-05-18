<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupMemberAdded extends Event
{
    use SerializesModels;

    public $groupMember;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(GroupMember $groupMember)
    {
        $this->groupMember = $groupMember;
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
        return 'madison.group.member-added';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
