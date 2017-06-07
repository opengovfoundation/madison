<?php

namespace App\Notifications;

use App\Models\SponsorMember;
use App\Models\User;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddedToSponsor extends UserMembershipChanged
{
    public $sponsorMember;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SponsorMember $sponsorMember, User $instigator)
    {
        parent::__construct($instigator);
        $this->sponsorMember = $sponsorMember;
        $this->actionUrl = route('sponsors.documents.index', $sponsorMember->sponsor);
        $this->subjectText = trans(static::baseMessageLocation().'.added_to_sponsor', [
            'name' => $this->instigator->getDisplayName(),
            'sponsor' => $this->sponsorMember->sponsor->display_name,
            'role' => $this->sponsorMember->role,
        ]);
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
                    ->action(trans('messages.notifications.see_sponsor'), $this->actionUrl)
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
            'sponsor_member_id' => $this->sponsorMember->id,
            'instigator_id' => $this->instigator->id,
        ];
    }
}
