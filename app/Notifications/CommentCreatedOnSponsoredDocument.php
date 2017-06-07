<?php

namespace App\Notifications;

use App\Models\Annotation;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentCreatedOnSponsoredDocument extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $commentType;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Annotation $comment)
    {
        $this->comment = $comment;
        $this->actionUrl = $comment->getLink();

        if ($this->comment->isNote()) {
            $this->commentType = trans('messages.notifications.comment_type_note');
        } else {
            $this->commentType = trans('messages.notifications.comment_type_comment');
        }

        $this->subjectText = trans(static::baseMessageLocation().'.subject', [
            'name' => $this->comment->user->getDisplayName(),
            'comment_type' => $this->commentType,
            'document' => $this->comment->rootAnnotatable->title,
        ]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \App\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage($this, $notifiable))
                    ->subject($this->subjectText)
                    ->action(trans('messages.notifications.see_comment', ['comment_type' => $this->commentType]), $this->actionUrl)
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
            'comment_id' => $this->comment->id,
        ];
    }

    public static function getName()
    {
        return 'madison.comment.created_on_sponsored';
    }

    public static function getType()
    {
        return static::TYPE_SPONSOR;
    }

    public function getInstigator()
    {
        return $this->comment->user;
    }
}
