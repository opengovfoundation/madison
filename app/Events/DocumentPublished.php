<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Doc as Document;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DocumentPublished extends Event
{
    use SerializesModels;

    public $document;
    public $instigator;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document, User $instigator)
    {
        $this->document = $document;
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
        return 'madison.document.published';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }
}
