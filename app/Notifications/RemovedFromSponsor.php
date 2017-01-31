<?php

namespace App\Notifications;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RemovedFromSponsor extends UserMembershipChanged
{
    public $sponsor;
    public $member;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Sponsor $sponsor, User $member, User $instigator)
    {
        parent::__construct($instigator);
        $this->sponsor = $sponsor;
        $this->member = $member;
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
        return (new MailMessage)
                    ->line(trans('messages.notifications.removed_from_sponsor', [
                        'name' => $this->instigator->getDisplayName(),
                        'sponsor' => $this->sponsor->display_name,
                    ]))
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
            'member_id' => $this->member->id,
            'instigator_id' => $this->instigator->id,
        ];
    }
}
