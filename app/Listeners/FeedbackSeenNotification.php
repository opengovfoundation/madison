<?php

namespace App\Listeners;

use App\Events\FeedbackSeen;
use App\Notification\Notifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackSeenNotification implements ShouldQueue
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
     * @param  FeedbackSeen  $event
     * @return void
     */
    public function handle(FeedbackSeen $event)
    {
        $data = [
            'feedback' => $event->feedback,
            'doc' => $event->feedback->doc,
            'user' => $event->user,
        ];
        $recipient = $event->feedback->user;

        if ($event->feedback instanceof Annotation) {
            $data['label'] = 'annotation';
        } else {
            $data['label'] = 'comment';
        }

        $this->notifier->queue('notification.feedback.seen-html', $data, function ($message) use ($recipient) {
            $message->setSubject('Your feedback on Madison was viewed by a sponsor!');
            $message->setRecipients($recipient);
        }, $event);
    }
}
