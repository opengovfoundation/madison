<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Events\NotificationRequest;
use App\Values\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Events\Dispatcher;

class CommentCreatedNotificationRequest implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

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
        if (!empty($event->parent)) {
            $subject = 'Activity on something of yours';

            // load data for template
            $event->comment->load('user');
            if ($event->parent instanceof \App\Models\Annotation) {
                $event->parent->type = 'annotation';
            } else {
                $event->parent->type = 'comment';
            }
            $data = ['subcomment' => $event->comment, 'parent' => $event->parent];
            $body = view('notification.comment.reply-html', $data)->render();

            $recipient = $event->parent->user;

            $this->dispatcher->fire(new NotificationRequest(new Message($subject, $body, $recipient), $event));
        }
    }
}
