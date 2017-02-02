<?php

namespace App\Listeners;

use App\Events\CommentLiked;
use App\Models\Annotation;
use App\Notifications;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentLikedNotification implements ShouldQueue
{
    public function handle(CommentLiked $event)
    {
        $event->like->annotatable->user->notify(new Notifications\CommentLiked($event->like));
    }
}
