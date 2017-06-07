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
        $this->actionUrl = route('admin.sponsors.index');
        $this->subjectText = trans(static::baseMessageLocation().'.subject', ['name' => $this->sponsor->name]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage($this, $notifiable))
                    ->subject($this->subjectText)
                    ->action(trans('messages.notifications.review_sponsor'), $this->actionUrl)
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
            'line' => $this->toLine(),
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
