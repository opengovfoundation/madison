<?php

namespace App\Listeners;

use App\Models\NotificationPreference;
use App\Models\Sponsor;
use App\Models\User;

use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShouldSendNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotificationSending  $event
     * @return void
     */
    public function handle(NotificationSending $event)
    {
        $recipient = $event->notifiable;
        $notification = $event->notification;

        // determine if the recipient *is allowed to receive* notifications from
        // this kind of event, i.e., if the event is not in the set of valid
        // notifications for the recipient, then skip it
        if (empty(NotificationPreference::getValidNotificationsForUser($recipient)[$notification::getName()])) {
            return false;
        }

        // filter out notifications for a user's own actions
        if ($recipient->id === $notification->getInstigator()->id) {
            return false;
        }

        // determine if the recipient *wants* notifications from this kind of event
        $recipientNotificationPreferenceQuery = $recipient
            ->notificationPreferences()
            ->where('event', $notification::getName());

        switch ($event->channel) {
            case 'mail':
                // if their email is not verified, don't send a message to it
                // regardless of if the user has it selected
                if (!empty($recipient->token) || empty($recipient->email)) {
                    return false;
                }

                $recipientNotificationPreferenceQuery
                    ->where('type', NotificationPreference::TYPE_EMAIL);
                break;
            case 'database':
                // unsupported at the moment
                // $recipientNotificationPreferenceQuery
                //     ->where('type', NotificationPreference::TYPE_IN_APP);
                return false;
                break;
            case 'nexmo':
                // unsupported at the moment
                // $recipientNotificationPreferenceQuery
                //     ->where('type', NotificationPreference::TYPE_TEXT);
                return false;
                break;
            default:
                return false;
        }

        if ($recipientNotificationPreferenceQuery->get()->isEmpty()) {
            // they don't want notifications for this event type
            return false;
        }
    }
}
