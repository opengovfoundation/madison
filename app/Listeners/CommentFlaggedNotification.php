<?php

namespace App\Listeners;

use App\Events\CommentFlagged;
use App\Models\Annotation;
use App\Notifications;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Notification;

class CommentFlaggedNotification implements ShouldQueue
{
    public function handle(CommentFlagged $event)
    {
        // notify the sponsors of the document
        $sponsors = $event->flag->rootAnnotatable->sponsors;
        $members = $sponsors
            ->flatMap(function ($sponsor) {
                return $sponsor->members->pluck('user');
            })
            ->unique('id');
        Notification::send($members, new Notifications\CommentFlagged($event->flag));
    }
}
