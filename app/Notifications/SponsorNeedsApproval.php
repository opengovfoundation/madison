<?php

namespace App\Notifications;

use App\Models\Sponsor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SponsorNeedsApproval extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sponsor;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Sponsor $sponsor)
    {
        $this->sponsor = $sponsor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('sponsors.index', ['id' => [$this->sponsor->id]]);

        return (new MailMessage)
                    ->line(trans('messages.notifications.sponsor_needs_approval', ['name' => $this->sponsor->name]))
                    ->action(trans('messages.notifications.review_sponsor'), $url)
                    ->line(trans('messages.notifications.thank_you'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'name' => static::getName(),
            'sponsor_id' => $this->sponsor->id,
        ];
    }

    public static function getName()
    {
        return 'madison.sponsor.needs_approval';
    }

    public static function getType()
    {
        return static::TYPE_ADMIN;
    }
}
