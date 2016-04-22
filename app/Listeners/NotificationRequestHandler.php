<?php

namespace App\Listeners;

use App\Events\NotificationRequest;
use App\Models\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationRequestHandler implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  NotificationRequest  $event
     * @return void
     */
    public function handle(NotificationRequest $event)
    {
        $eventType = $event->event;

        // determine if the recipient wants notifications from this kind of event
        $shouldSendNotification = Notification
            ::where('user_id', $event->message->recipient->id)
            ->where('event', $eventType::getName())
            ->get();

        if (empty($shouldSendNotification)) {
            return;
        }

        foreach ($shouldSendNotification as $item) {
            switch ($item->type) {
                case Notification::TYPE_EMAIL:
                    $this->mailer->raw($event->message->body, function ($message) use ($event) {
                        $message->setContentType('text/html');
                        $message->subject($event->message->subject);
                        $message->from('sayhello@opengovfoundation.org', 'Madison');
                        $message->to($event->message->recipient->email);
                    });
                    break;
                case Notification::TYPE_TEXT:
                    // unsupported
                default:
                    // do nothing
            }
        }
    }
}
