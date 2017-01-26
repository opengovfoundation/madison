<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\Annotation;
use App\Notifications\CommentReplied;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentCreatedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  CommentCreated  $event
     * @return void
     */
    public function handle(CommentCreated $event)
    {
        // if the comment is in reply to something, signal a notification to
        // the parent user
        if ($event->parent instanceof Annotation) {
            $event->parent->user->notify(new CommentReplied($event->comment, $event->parent));
        }
    }
}
