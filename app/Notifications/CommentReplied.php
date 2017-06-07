<?php

namespace App\Notifications;

use App\Models\Annotation;
use App\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $parent;
    public $parentType;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Annotation $comment, Annotation $parent)
    {
        $this->comment = $comment;
        $this->parent = $parent;
        $this->actionUrl = $comment->getLink();

        if ($this->parent->isNote()) {
            $this->parentType = trans('messages.notifications.comment_type_note');
        } else {
            $this->parentType = trans('messages.notifications.comment_type_comment');
        }

        $this->subjectText = trans(static::baseMessageLocation().'.subject', [
            'name' => $this->comment->user->getDisplayName(),
            'comment_type' => $this->parentType,
            'document' => $this->comment->rootAnnotatable->title,
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
                    ->action(trans('messages.notifications.see_comment', ['comment_type' => $this->parentType]), $this->actionUrl)
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
            'parent_id' => $this->parent->id,
            'comment_id' => $this->comment->id,
            'comment_type' => $this->parentType,
        ];
    }

    public static function getName()
    {
        return 'madison.comment.replied';
    }

    public static function getType()
    {
        return static::TYPE_USER;
    }

    public function getInstigator()
    {
        return $this->comment->user;
    }
}
