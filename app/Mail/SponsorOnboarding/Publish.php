<?php

namespace App\Mail\SponsorOnboarding;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Publish extends Mailable
{
    use Queueable, SerializesModels;

    public $sponsor;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Sponsor $sponsor, User $user)
    {
        $this->sponsor = $sponsor;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(trans('messages.sponsor.onboarding.publish.subject'))
            ->markdown('emails.sponsor_onboarding.publish');
    }
}
