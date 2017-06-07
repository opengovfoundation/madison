<?php

namespace App\Mail;

use App\Models\User;
use App\Models\NotificationPreference;
use App\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class DailyNotifications extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->unsubscribeMarkdown = NotificationPreference::getUnsubscribeMarkdown(null, $user);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $groupedAndFormattedNotifications = Notification::groupAndFormatNotifications($this->user->notifications);

        return $this->subject(
            trans(
                'messages.notifications.frequencies.' . NotificationPreference::FREQUENCY_DAILY . '.subject',
                ['dateStr' => Carbon::now()->toFormattedDateString()]
            )
        )->markdown('emails.daily_notifications', [
            'groupedAndFormattedNotifications' => $groupedAndFormattedNotifications,
            'unsubscribeMarkdown' => $this->unsubscribeMarkdown,
        ]);
    }
}
