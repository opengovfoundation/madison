<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\Annotation;
use App\Notifications\CommentCreatedOnSponsoredDocument;
use App\Notifications\CommentReplied;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Notification;

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
        // notify the sponsors of the document
        $sponsors = $event->comment->rootAnnotatable->sponsors;
        $members = $sponsors
            ->flatMap(function ($sponsor) {
                return $sponsor->members->pluck('user');
            })
            ->unique('id');
        Notification::send($members, new CommentCreatedOnSponsoredDocument($event->comment));

        // if the comment is in reply to something, signal a notification to
        // the parent user
        if ($event->parent instanceof Annotation) {
            $event->parent->user->notify(new CommentReplied($event->comment, $event->parent));
        }
    }
}
