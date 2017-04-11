<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Tests\Browser\Pages\DocumentPage;
use Tests\FactoryHelpers;
use Illuminate\Support\Facades\Notification;
use App\Models\Annotation;
use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\User;
use App\Notifications\CommentCreatedOnSponsoredDocument;
use App\Notifications\CommentReplied;
use App\Notifications\CommentLiked;
use App\Notifications\CommentFlagged;
use App\Notifications\SupportVoteChanged;


class DocumentPageTest extends DuskTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->sponsorUser = factory(User::class)->create();
        $this->user = factory(User::class)->create();
        $this->sponsor = FactoryHelpers::createActiveSponsorWithUser($this->sponsorUser);

        $this->document = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
        ]);

        $this->document->content()->save(factory(DocContent::class)->make());

        $this->document->sponsors()->save($this->sponsor);

        $firstWord = explode(' ', $this->document->content()->first()->content)[0];
        $secondWord = explode(' ', $this->document->content()->first()->content)[1];

        $this->note1 = FactoryHelpers::addNoteToDocument($this->sponsorUser, $this->document, $firstWord);
        $this->note2 = FactoryHelpers::addNoteToDocument($this->sponsorUser, $this->document, $secondWord);

        $this->comment1 = FactoryHelpers::createComment($this->sponsorUser, $this->document);
        $this->comment2 = FactoryHelpers::createComment($this->sponsorUser, $this->document);

        $this->commentReply = FactoryHelpers::createComment($this->sponsorUser, $this->comment1);

        $this->page = new DocumentPage($this->document);

        // TODO: Mocking does not work with Dusk yet!
        // -- ref: https://github.com/laravel/dusk/issues/152

        //FactoryHelpers::subscribeUsersToAllNotifications();
        //Notification::fake();
    }

    public function testCanSeeDocumentContent()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->assertTitleContains($this->document->title)
                ->assertSee($this->document->title)
                ->assertSeeIn('@sponsorList', $this->document->sponsors()->first()->display_name)
                ;

            foreach (explode("\n\n", $this->document->content()->first()->content) as $p) {
                $browser->assertSee($p);
            }
        });
    }

    public function testCanSeeDocumentStats()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->assertSeeIn('@supportBtn', '0')
                ->assertSeeIn('@opposeBtn', '0')
                ;
        });
    }

    public function testDiscussionHiddenHidesAllTheThings()
    {
        $this->document->update(['discussion_state' => Document::DISCUSSION_STATE_HIDDEN]);

        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->assertDontSee('@supportBtn')
                ->assertDontSee('@opposeBtn')
                ->assertDontSee('@annotationGroups')
                ;
        });
    }

    public function testViewDocumentComments()
    {
        /**
         * Not testing for timestamps here because they end up off by a second or so
         */
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->assertSeeComment($this->comment1)
                ->assertSeeComment($this->comment2)
                ->revealCommentReplies($this->comment1)
                ->assertSeeReplyToComment($this->comment1, $this->commentReply)
                ;
        });
    }

    public function testViewDocumentNotes()
    {
        /**
         * Not testing for timestamps here because they end up off by a second or so
         */
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->openNotesPane()
                ->assertSeeNote($this->note1)
                ->assertSeeNote($this->note2)
                ;
        });
    }

    public function testCantViewUnpublishedDocument()
    {
        $this->document->update([
            'publish_state' => Document::PUBLISH_STATE_UNPUBLISHED,
        ]);

        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->assertSee('This action is unauthorized')
                ;
        });
    }

    public function testLoginRedirectIfSupportWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->pause(500)
                ->click('@supportBtn')
                ->assertPathIs('/login')
                ->assertLoginRedirectsBackToPage($this->user)
                ;
        });
    }

    public function testLoginRedirectIfOpposeWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->pause(500)
                ->click('@opposeBtn')
                ->assertPathIs('/login')
                ->assertLoginRedirectsBackToPage($this->user)
                ;
        });
    }

    public function testLoginRedirectIfLikeCommentWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->pause(500)
                ->addActionToComment('like', $this->comment1)
                ->assertPathIs('/login')
                ->assertLoginRedirectsBackToPage($this->user)
                ;
        });
    }

    public function testLoginRedirectIfFlagCommentWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->pause(500)
                ->addActionToComment('flag', $this->comment1)
                ->assertPathIs('/login')
                ->assertLoginRedirectsBackToPage($this->user)
                ;
        });
    }

    public function testLoginRedirectIfLikeNoteWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->openNotesPane()
                ->pause(500)
                ->addActionToNote('like', $this->note1)
                ->assertPathIs('/login')
                ->assertLoginRedirectsBackToPage($this->user)
                ;
        });
    }

    public function testLoginRedirectIfFlagNoteWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->openNotesPane()
                ->pause(500)
                ->addActionToNote('flag', $this->note1)
                ->assertPathIs('/login')
                ->assertLoginRedirectsBackToPage($this->user)
                ;
        });
    }

    public function testLoginToCommentRedirect()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->pause(500)
                ->clickLink(trans('messages.document.login_to_comment'))
                ->assertPathIs('/login')
                ->assertLoginRedirectsBackToPage($this->user)
                ;
        });
    }

    public function testCommentFormsHiddenIfNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->assertDontSee('@commentForm')
                ->with(DocumentPage::commentSelector($this->comment1), function($commentDiv) {
                    $commentDiv->assertDontSee('@commentForm');
                })
                ;
        });
    }

    public function testNoteReplyHiddenIfNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit($this->page)
                ->openNotesPane()
                ->with(DocumentPage::noteSelector($this->note1), function($commentDiv) {
                    $commentDiv
                        ->assertDontSee('@noteReplyForm')
                        ->clickLink(trans('messages.document.add_reply'))
                        ->assertPathIs('/login')
                        ;
                })
                ;
        });
    }

    public function testAddCommentToDocument()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->fillAndSubmitCommentForm()
                ;

            $newComment = $this->document->allComments()
                ->orderBy('created_at', 'desc')
                ->first();

            // Should jump to and highlight new comment
            $browser
                ->waitFor('.comment#' . $newComment->str_id)
                ->assertSeeComment($newComment)
                ;

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentCreatedOnSponsoredDocument::class,
            //    function ($notification, $channels) use ($newComment) {
            //        $notification->comment->id === $newComment->id;
            //    }
            //);
        });
    }

    public function testAddReplyToComment()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->fillAndSubmitCommentReplyForm($this->comment1)
                ;

            $newComment = $this->document->allComments()
                ->orderBy('created_at', 'desc')
                ->first();

            // Should jump to and highlight new comment
            $browser
                ->waitFor('.comment#' . $newComment->str_id)
                ->assertSeeReplyToComment($this->comment1, $newComment)
                ;

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentCreatedOnSponsoredDocument::class,
            //    function ($notification, $channels) use ($newComment) {
            //        $notification->comment->id === $newComment->id;
            //    }
            //);

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentReplied::class,
            //    function ($notification, $channels) use ($newComment) {
            //        $notification->comment->id === $newComment->id;
            //    }
            //);
        });
    }

    public function testAddNoteToDocument()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->addNoteToContent()
                ;

            $newNote = $this->document->allComments()
                ->orderBy('created_at', 'desc')
                ->first();

            $browser
                ->refresh()
                ->openNotesPane()
                ->assertSeeNote($newNote)
                ;

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentCreatedOnSponsoredDocument::class,
            //    function ($notification, $channels) use ($newNote) {
            //        $notification->comment->id === $newNote->id;
            //    }
            //);
        });
    }

    public function testAddReplyToNote()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->openNotesPane()
                ->addReplyToNote($this->note1)
                ;

            $newNote = $this->document->allComments()
                ->orderBy('created_at', 'desc')
                ->first();

            $browser
                ->refresh()
                ->openNotesPane()
                ->assertSeeReplyToNote($this->note1, $newNote)
                ;

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentCreatedOnSponsoredDocument::class,
            //    function ($notification, $channels) use ($newNote) {
            //        $notification->comment->id === $newNote->id;
            //    }
            //);

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentReplied::class,
            //    function ($notification, $channels) use ($newNote) {
            //        $notification->comment->id === $newNote->id;
            //    }
            //);
        });
    }

    public function testLikeCommentOnDocument()
    {
        $commentLikes = $this->comment1->likes_count;

        $this->browse(function ($browser) use ($commentLikes) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->addActionToComment('like', $this->comment1)
                ->assertCommentHasActionCount('like', $this->comment1, $commentLikes + 1)
                ;

            //$newLike = Annotation::where('annotation_type_type', Annotation::TYPE_LIKE)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentLiked::class,
            //    function ($notification, $channels) use ($newLike) {
            //        $notification->like->id === $newLike->id;
            //    }
            //);
        });
    }

    public function testFlagCommentOnDocument()
    {
        $commentFlags = $this->comment1->flags_count;

        $this->browse(function ($browser) use ($commentFlags) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->addActionToComment('flag', $this->comment1)
                ->assertCommentHasActionCount('flag', $this->comment1, $commentFlags + 1)
                ;

            //$newFlag = Annotation::where('annotation_type_type', Annotation::TYPE_FLAG)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentFlagged::class,
            //    function ($notification, $channels) use ($newFlag) {
            //        $notification->flag->id === $newFlag->id;
            //    }
            //);
        });
    }

    public function testLikeCommentReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->comment1);
        $replyLikes = $reply->likes_count;

        $this->browse(function ($browser) use ($reply, $replyLikes) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->revealCommentReplies($this->comment1)
                ->addActionToComment('like', $reply)
                ->assertCommentHasActionCount('like', $reply, $replyLikes + 1)
                ;

            //$newLike = Annotation::where('annotation_type_type', Annotation::TYPE_COMMENT)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentLiked::class,
            //    function ($notification, $channels) use ($newLike) {
            //        $notification->like->id === $newLike->id;
            //    }
            //);
        });
    }

    public function testFlagCommentReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->comment1);
        $replyFlags = $reply->flags_count;

        $this->browse(function ($browser) use ($reply, $replyFlags) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->revealCommentReplies($this->comment1)
                ->addActionToComment('flag', $reply)
                ->assertCommentHasActionCount('flag', $reply, $replyFlags + 1)
                ;

            //$newFlag = Annotation::where('annotation_type_type', Annotation::TYPE_FLAG)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentFlagged::class,
            //    function ($notification, $channels) use ($newFlag) {
            //        $notification->flag->id === $newFlag->id;
            //    }
            //);
        });
    }

    public function testLikeNoteOnDocument()
    {
        $noteLikes = $this->note1->likes_count;

        $this->browse(function ($browser) use ($noteLikes) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->openNotesPane()
                ->addActionToNote('like', $this->note1)
                ->assertNoteHasActionCount('like', $this->note1, $noteLikes +1)
                ;

            //$newLike = Annotation::where('annotation_type_type', Annotation::TYPE_LIKE)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentLiked::class,
            //    function ($notification, $channels) use ($newLike) {
            //        $notification->like->id === $newLike->id;
            //    }
            //);
        });
    }

    public function testFlagNoteOnDocument()
    {
        $noteFlags = $this->note1->flags_count;

        $this->browse(function ($browser) use ($noteFlags) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->openNotesPane()
                ->addActionToNote('flag', $this->note1)
                ->assertNoteHasActionCount('flag', $this->note1, $noteFlags +1)
                ;

            //$newFlag = Annotation::where('annotation_type_type', Annotation::TYPE_FLAG)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentFlagged::class,
            //    function ($notification, $channels) use ($newFlag) {
            //        $notification->flag->id === $newFlag->id;
            //    }
            //);
        });
    }

    public function testLikeNoteReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->note1);
        $replyLikes = $reply->likes_count;

        $this->browse(function ($browser) use ($reply, $replyLikes) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->openNotesPane()
                ->addActionToComment('like', $reply)
                ->assertCommentHasActionCount('like', $reply, $replyLikes + 1)
                ;

            //$newLike = Annotation::where('annotation_type_type', Annotation::TYPE_LIKE)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentLiked::class,
            //    function ($notification, $channels) use ($newLike) {
            //        $notification->like->id === $newLike->id;
            //    }
            //);
        });
    }

    public function testFlagNoteReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->note1);
        $replyFlags = $reply->flags_count;

        $this->browse(function ($browser) use ($reply, $replyFlags) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->openNotesPane()
                ->addActionToComment('flag', $reply)
                ->assertCommentHasActionCount('flag', $reply, $replyFlags + 1)
                ;

            //$newFlag = Annotation::where('annotation_type_type', Annotation::TYPE_FLAG)
            //    ->orderBy('created_at', 'desc')
            //    ->first();

            //Notification::assertSentTo(
            //    $this->user,
            //    CommentFlagged::class,
            //    function ($notification, $channels) use ($newFlag) {
            //        return $notification->flag->id === $newFlag->id;
            //    }
            //);
        });
    }

    public function testSupportDocument()
    {
        $documentSupport = $this->document->support;

        $this->browse(function ($browser) use ($documentSupport) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->click('@supportBtn')
                ->assertDocumentSupportCount($documentSupport + 1)
                ;

            //Notification::assertSentTo(
            //    $this->user,
            //    SupportVoteChanged::class,
            //    function ($notification, $channels) {
            //        return
            //            $notification->document->id === $this->document->id &&
            //            $notification->user->id === $this->user2->id &&
            //            $notification->oldValue === null &&
            //            $notification->newValue === 'support'
            //            ;
            //    }
            //);
        });
    }

    public function testOpposeDocument()
    {
        $documentOppose = $this->document->oppose;

        $this->browse(function ($browser) use ($documentOppose) {
            $browser
                ->loginAs($this->user)
                ->visit($this->page)
                ->click('@opposeBtn')
                ->assertDocumentOpposeCount($documentOppose + 1)
                ;

            //Notification::assertSentTo(
            //    $this->user,
            //    SupportVoteChanged::class,
            //    function ($notification, $channels) {
            //        return
            //            $notification->document->id === $this->document->id &&
            //            $notification->user->id === $this->user2->id &&
            //            $notification->oldValue === null &&
            //            $notification->newValue === 'oppose'
            //            ;
            //    }
            //);
        });
    }
}
