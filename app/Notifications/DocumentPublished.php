<?php

namespace App\Notifications;

use App\Models\Doc as Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public $document;
    public $instigator;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Document $document, User $instigator)
    {
        $this->document = $document;
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
        $url = $this->document->url;

        return (new MailMessage)
                    ->subject(trans(static::baseMessageLocation().'.subject', [
                        'document' => $this->document->title,
                    ]))
                    ->action(trans('messages.notifications.see_document'), $url)
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
            'document_id' => $this->document->id,
            'instigator_id' => $this->instigator->id,
        ];
    }

    public static function getName()
    {
        return 'madison.document.published';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }

    public function getInstigator()
    {
        return $this->instigator;
    }
}
