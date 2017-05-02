<?php

namespace App\Events;

use App\Models\Doc as Document;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DocumentPublished
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
}
