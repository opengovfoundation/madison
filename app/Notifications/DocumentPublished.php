<?php

namespace App\Notifications;

use App\Models\Doc as Document;
use App\Models\User;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $this->actionUrl = $document->url;
        $this->subjectText = trans(static::baseMessageLocation().'.subject', [
            'document' => $this->document->title,
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
        $shareUrl = 'https://twitter.com/intent/tweet/?url=' . urlencode($this->actionUrl) . '&text=' . urlencode($this->document->title);

        return (new MailMessage($this, $notifiable))
                    ->subject($this->subjectText)
                    ->action(trans('messages.notifications.see_document'), $this->actionUrl)
                    ->line('[' . trans('messages.notifications.madison.document.published.share_on_twitter') . '](' . $shareUrl . ')')
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
