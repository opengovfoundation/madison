<?php

namespace App\Notifications;

use App\Models\Sponsor;
use App\Models\User;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SponsorNeedsApproval extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sponsor;
    protected $instigator;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Sponsor $sponsor, User $instigator)
    {
        $this->sponsor = $sponsor;
        $this->instigator = $instigator;
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
        $url = route('admin.sponsors.index');

        return (new MailMessage($this, $notifiable))
                    ->subject(trans(static::baseMessageLocation().'.subject', ['name' => $this->sponsor->name]))
                    ->action(trans('messages.notifications.review_sponsor'), $url)
                    ;
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

    public function getInstigator()
    {
        return $this->instigator;
    }
}
