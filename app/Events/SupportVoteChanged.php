<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Doc as Document;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SupportVoteChanged extends Event
{
    use SerializesModels;

    public $oldValue;
    public $newValue;
    public $document;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($oldValue, $newValue, Document $document, User $user)
    {
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
        $this->document = $document;
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
        return 'madison.document.support_vote_changed';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
