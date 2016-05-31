<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Notification\Notifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentCreatedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
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
            if ($event->parent->isNote()) {
                $parentType = 'note';
            } else {
                $parentType = 'comment';
            }

            $data = [
                'subcomment' => $event->comment,
                'parent' => $event->parent,
                'parentType' => $parentType,
            ];
            $recipient = $event->parent->user;

            $this->notifier->queue('notification.comment.reply-html', $data, function ($message) use ($recipient) {
                $message->setSubject('Activity on something of yours');
                $message->setRecipients($recipient);
            }, $event);
        }
    }
}
