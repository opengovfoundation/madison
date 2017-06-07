<?php

namespace Tests\Unit\Notifications;

use App\Notifications\Notification;

use App\Models\Doc as Document;
use App\Models\Sponsor;
use App\Models\User;

use Tests\FactoryHelpers;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Notifications\DatabaseNotification;

class NotificationTest extends TestCase
{
    public function testGroupAndFormatNotifications()
    {
        $sponsorUser = factory(User::class)->create([]);
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($sponsorUser);

        $otherUser = factory(User::class)->create([]);
        $userPool = factory(User::class, 20)->create([]);

        $document = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);
        $comment = FactoryHelpers::createComment($otherUser, $document);

        $supportNotifications = $userPool->take(10)->map(function ($user) use ($document) {
            return factory(DatabaseNotification::class)->create([
                'type' => 'App\Notifications\SupportVoteChanged',
                'data' => [
                    'name' => 'madison.document.support_vote_changed',
                    'document_id' => $document->id,
                    'user_id' => $user->id,
                    'old_value' => null,
                    'new_value' => true,
                ],
            ])
            ;
        });

        $opposeNotifications = $userPool->take(-10)->map(function ($user) use ($document) {
            return factory(DatabaseNotification::class)->create([
                'type' => 'App\Notifications\SupportVoteChanged',
                'data' => [
                    'name' => 'madison.document.support_vote_changed',
                    'document_id' => $document->id,
                    'user_id' => $user->id,
                    'old_value' => null,
                    'new_value' => false,
                ],
            ]);
        });

        $commentCreatedNotifications = $userPool->take(5)->map(function ($user) use ($document) {
            $newComment = FactoryHelpers::createComment($user, $document);
            return factory(DatabaseNotification::class)->create([
                'type' => 'App\Notifications\CommentCreatedOnSponsoredDocument',
                'data' => [
                    'name' => 'madison.comment.created_on_sponsored',
                    'comment_id' => $newComment->id
                ]
            ])
            ;
        });

        $commentLikedNotifications = $userPool->take(10)->map(function ($user) use ($comment) {
            $like = FactoryHelpers::createLike($user, $comment);
            return factory(DatabaseNotification::class)->create([
                'type' => 'App\Notifications\CommentLiked',
                'data' => [
                    'name' => 'madison.comment.liked',
                    'like_id' => $like->id,
                    'comment_type' => 'comment',
                ]
            ]);
        });

        $commentFlaggedNotifications = $userPool->take(-10)->map(function ($user) use ($comment) {
            $flag = FactoryHelpers::createFlag($user, $comment);
            return factory(DatabaseNotification::class)->create([
                'type' => 'App\Notifications\CommentFlagged',
                'data' => [
                    'name' => 'madison.comment.flagged',
                    'flag_id' => $flag->id,
                    'comment_type' => 'comment',
                ]
            ]);
        });

        $commentRepliedNotifications = $userPool->take(10)->map(function ($user) use ($comment) {
            $reply = FactoryHelpers::createComment($user, $comment);
            return factory(DatabaseNotification::class)->create([
                'type' => 'App\Notifications\CommentReplied',
                'data' => [
                    'name' => 'madison.comment.replied',
                    'parent_id' => $comment->id,
                    'comment_id' => $reply->id,
                    'comment_type' => 'comment',
                ]
            ]);
        });

        $newDocument = FactoryHelpers::createPublishedDocumentForSponsor($sponsor);
        $documentPublishedNotification = factory(DatabaseNotification::class)->create([
            'type' => 'App\Notifications\DocumentPublished',
            'data' => [
                'line' => 'junkjunkjunk',
                'name' => 'madison.document.published',
                'document_id' => $newDocument->id,
            ]
        ]);

        // This is to test the grouping by document
        $newDocumentSupportNotifications = $userPool->take(4)->map(function ($user) use ($newDocument) {
            return factory(DatabaseNotification::class)->create([
                'type' => 'App\Notifications\SupportVoteChanged',
                'data' => [
                    'name' => 'madison.document.support_vote_changed',
                    'document_id' => $newDocument->id,
                    'user_id' => $user->id,
                    'old_value' => null,
                    'new_value' => true,
                ],
            ]);
        });

        $notifications = collect([
            $supportNotifications, // 10
            $opposeNotifications, // 10
            $commentCreatedNotifications, // 5
            $commentLikedNotifications, // 10
            $commentFlaggedNotifications, // 10
            $commentRepliedNotifications, // 10
            $documentPublishedNotification, // 1
            $newDocumentSupportNotifications, // 4
        ])->flatten();

        $this->assertCount(60, $notifications->toArray());

        $groupedAndFormattedNotifications = Notification::groupAndFormatNotifications($notifications);

        $this->assertCount(8, $groupedAndFormattedNotifications);

        // Number of supports / opposes on a document
        $this->assertContains(
            Notification::groupedLineForDocumentNotifications('document.support_vote_changed.support', $supportNotifications, $document->id),
            $groupedAndFormattedNotifications->toArray()
        );

        $this->assertContains(
            Notification::groupedLineForDocumentNotifications('document.support_vote_changed.oppose', $opposeNotifications, $document->id),
            $groupedAndFormattedNotifications->toArray()
        );

        // Number of comments created on a sponsored document
        $this->assertContains(
            Notification::groupedLineForDocumentNotifications('comment.created_on_sponsored', $commentCreatedNotifications, $document->id),
            $groupedAndFormattedNotifications->toArray()
        );

        // Comment liked by # of users
        $this->assertContains(
            Notification::groupedLineForCommentNotifications('comment.liked', $commentLikedNotifications, $comment->id),
            $groupedAndFormattedNotifications->toArray()
        );

        // Comment flagged by # of users
        $this->assertContains(
            Notification::groupedLineForCommentNotifications('comment.flagged', $commentFlaggedNotifications, $comment->id),
            $groupedAndFormattedNotifications->toArray()
        );

        // Document published line (non-grouped notification)
        $this->assertContains(
            $documentPublishedNotification->data['line'],
            $groupedAndFormattedNotifications->toArray()
        );
    }
}
