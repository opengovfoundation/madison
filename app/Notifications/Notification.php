<?php

namespace App\Notifications;

use App\Models\Annotation;
use App\Models\Doc as Document;
use App\Models\NotificationPreference;

use Illuminate\Notifications\Notification as BaseNotification;

abstract class Notification
    extends BaseNotification
    implements \App\Contracts\Notification
{
    public $actionUrl;
    public $subjectText;

    public static function baseMessageLocation()
    {
        return 'messages.notifications.'.static::getName();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $preference = $notifiable->notificationPreferences()->where('event', $this->getName())->first();

        // User not subscribed to this notification? No channels
        if (!$preference) { return []; }

        // If we're not sending this immediately it goes in the database
        if ($preference->frequency === NotificationPreference::FREQUENCY_IMMEDIATELY) {
            return ['mail'];
        }

        return ['database'];
    }

    /**
     * Get the single line representation of the notification. Used in
     * notification batch emails.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toLine()
    {
        return '[' . $this->subjectText . '](' . $this->actionUrl . ')';
    }


    public static function groupAndFormatNotifications($notifications)
    {
        $supportVoteChangedNotifications = $notifications->filter(function ($n) {
            return $n->type === 'App\Notifications\SupportVoteChanged';
        })->groupBy(function ($n) {
            return $n->data['document_id'];
        });

        $newCommentOnDocumentNotifications = $notifications->filter(function ($n) {
            return $n->type === 'App\Notifications\CommentCreatedOnSponsoredDocument';
        })->groupBy(function ($n) {
            return Annotation::find($n->data['comment_id'])->rootAnnotatable->id;
        });

        $commentLikedNotifications = $notifications->filter(function ($n) {
            return $n->type === 'App\Notifications\CommentLiked';
        })->groupBy(function ($n) {
            return Annotation::find($n->data['like_id'])->annotatable->id;
        });

        $commentFlaggedNotifications = $notifications->filter(function ($n) {
            return $n->type === 'App\Notifications\CommentFlagged';
        })->groupBy(function ($n) {
            return Annotation::find($n->data['flag_id'])->annotatable->id;
        });

        $commentRepliedNotifications = $notifications->filter(function ($n) {
            return $n->type === 'App\Notifications\CommentReplied';
        })->groupBy(function ($n) {
            return $n->data['parent_id'];
        });

        $nonGroupedNotifications = $notifications->filter(function ($n) {
            return !in_array($n->type, [
                'App\Notifications\SupportVoteChanged',
                'App\Notifications\CommentCreatedOnSponsoredDocument',
                'App\Notifications\CommentLiked',
                'App\Notifications\CommentFlagged',
                'App\Notifications\CommentReplied',
            ]);
        });

        return collect([
            static::formatGroupedSupportVoteChangedNotifications($supportVoteChangedNotifications),
            static::formatGroupedCommentCreatedOnSponsoredDocumentNotifications($newCommentOnDocumentNotifications),
            static::formatGroupedCommentLikedNotifications($commentLikedNotifications),
            static::formatGroupedCommentFlaggedNotifications($commentFlaggedNotifications),
            static::formatGroupedCommentRepliedNotifications($commentRepliedNotifications),
            static::formatNonGroupedNotifications($nonGroupedNotifications),
        ])->flatten();
    }

    public static function formatGroupedSupportVoteChangedNotifications($notifications)
    {
        // Grouped by document
        return $notifications->map(function ($documentGroup, $documentId) {
            $latestVotesByUniqueUsers = $documentGroup
                ->sortByDesc('created_at') // Newest first
                ->reduce(function ($filtered, $current) {
                    // Only count the latest by each user
                    if ($filtered->where('data.user_id', $current->data['user_id'])->count() > 0) {
                        return $filtered;
                    }
                    return $filtered->push($current);
                }, collect([]))
                ;

            $supports = $latestVotesByUniqueUsers->filter(function ($notification) {
                return $notification->data['new_value'] === true;
            });

            $opposes = $latestVotesByUniqueUsers->filter(function ($notification) {
                return $notification->data['new_value'] === false;
            });

            $messages = [];

            if ($supports->count() > 0) {
                if ($supports->count() > 1) {
                    $messages[] = static::groupedLineForDocumentNotifications('document.support_vote_changed.support', $supports, $documentId);
                } else {
                    $messages[] = $supports->first()->data['line'];
                }
            }

            if ($opposes->count() > 0) {
                if ($opposes->count() > 1) {
                    $messages[] = static::groupedLineForDocumentNotifications('document.support_vote_changed.oppose', $opposes, $documentId);
                } else {
                    $messages[] = $opposes->first()->data['line'];
                }
            }

            return $messages;
        });
    }

    public static function formatGroupedCommentCreatedOnSponsoredDocumentNotifications($groupedNotifications)
    {
        return $groupedNotifications->map(function ($notifications, $documentId) {
            if ($notifications->count() > 1) {
                return static::groupedLineForDocumentNotifications('comment.created_on_sponsored', $notifications, $documentId);
            } else {
                return $notifications->first()->data['line'];
            }
        });
    }

    public static function formatGroupedCommentLikedNotifications($groupedNotifications)
    {
        return $groupedNotifications->map(function ($notifications, $commentId) {
            if ($notifications->count() > 1) {
                return static::groupedLineForCommentNotifications('comment.liked', $notifications, $commentId);
            } else {
                return $notifications->first()->data['line'];
            }
        });
    }

    public static function formatGroupedCommentFlaggedNotifications($groupedNotifications)
    {
        return $groupedNotifications->map(function ($notifications, $commentId) {
            if ($notifications->count() > 1) {
                return static::groupedLineForCommentNotifications('comment.flagged', $notifications, $commentId);
            } else {
                return $notifications->first()->data['line'];
            }
        });
    }

    public static function formatGroupedCommentRepliedNotifications($groupedNotifications)
    {
        return $groupedNotifications->map(function ($notifications, $commentId) {
            if ($notifications->count() > 1) {
                return static::groupedLineForCommentNotifications('comment.replied', $notifications, $commentId);
            } else {
                return $notifications->first()->data['line'];
            }
        });
    }

    public static function formatNonGroupedNotifications($notifications)
    {
        return $notifications->map(function ($n) {
            return $n->data['line'];
        });
    }

    public static function groupedLineForDocumentNotifications($type, $notifications, $documentId)
    {
        $document = Document::find($documentId);
        return '[' . trans('messages.notifications.grouped.' . $type, [
            'document' => $document->title,
            'count' => $notifications->count(),
        ]) . '](' . $document->getLink() . ')';
    }

    public static function groupedLineForCommentNotifications($type, $notifications, $commentId)
    {
        $comment = Annotation::find($commentId);
        return '[' . trans('messages.notifications.grouped.' . $type, [
            'count' => $notifications->count(),
            'comment_type' => $notifications->first()->data['comment_type'],
            'document' => $comment->rootAnnotatable->title,
        ]) . '](' . $comment->getLink() . ')';
    }
}
