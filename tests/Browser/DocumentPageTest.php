<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Tests\Browser\Pages\DocumentPage;
use Tests\FactoryHelpers;

use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\User;

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
    }

    public function testCanSeeDocumentContent()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
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
            $browser->visit(new DocumentPage($this->document))
                ->assertSeeIn('@participantCount', '1')
                ->assertSeeIn('@notesCount', '2')
                ->assertSeeIn('@commentsCount', '3')
                ->assertSeeIn('@supportBtn', '0')
                ->assertSeeIn('@opposeBtn', '0')
                ;
        });
    }

    public function testDiscussionHiddenHidesAllTheThings()
    {
        $this->document->update(['discussion_state' => Document::DISCUSSION_STATE_HIDDEN]);

        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->assertDontSee('@participantCount')
                ->assertDontSee('@notesCount')
                ->assertDontSee('@commentsCount')
                ->assertDontSee('@supportBtn')
                ->assertDontSee('@opposeBtn')
                ->assertDontSee('@contentTab')
                ->assertDontSee('@commentsTab')
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
            $browser->visit(new DocumentPage($this->document))
                ->openCommentsTab()
                ->assertSeeComment($this->comment1)
                ->assertSeeComment($this->comment2)
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
            $browser->visit(new DocumentPage($this->document))
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
            $browser->visit(new DocumentPage($this->document))
                ->assertSee('This action is unauthorized')
                ;
        });
    }

    public function testLoginRedirectIfSupportWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->click('@supportBtn')
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfOpposeWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->click('@opposeBtn')
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfLikeCommentWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->openCommentsTab()
                ->addActionToComment('like', $this->comment1)
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfFlagCommentWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->openCommentsTab()
                ->addActionToComment('flag', $this->comment1)
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfLikeNoteWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->openNotesPane()
                ->addActionToNote('like', $this->note1)
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfFlagNoteWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->openNotesPane()
                ->addActionToNote('flag', $this->note1)
                ->assertPathIs('/login')
                ;
        });
    }

    public function testCommentFormsHiddenIfNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->openCommentsTab()
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
            $browser->visit(new DocumentPage($this->document))
                ->openNotesPane()
                ->with(DocumentPage::noteSelector($this->note1), function($commentDiv) {
                    $commentDiv
                        ->assertDontSee('@noteReplyForm')
                        ->clickLink('Add your reply')
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
                ->visit(new DocumentPage($this->document))
                ->openCommentsTab()
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
        });
    }

    public function testAddReplyToComment()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openCommentsTab()
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
        });
    }

    public function testAddNoteToDocument()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
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
        });
    }

    public function testAddReplyToNote()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
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
        });
    }

    public function testLikeCommentOnDocument()
    {
        $commentLikes = $this->comment1->likes_count;

        $this->browse(function ($browser) use ($commentLikes) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openCommentsTab()
                ->addActionToComment('like', $this->comment1)
                ->assertCommentHasActionCount('like', $this->comment1, $commentLikes + 1)
                ;
        });
    }

    public function testFlagCommentOnDocument()
    {
        $commentFlags = $this->comment1->flags_count;

        $this->browse(function ($browser) use ($commentFlags) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openCommentsTab()
                ->addActionToComment('flag', $this->comment1)
                ->assertCommentHasActionCount('flag', $this->comment1, $commentFlags + 1)
                ;
        });
    }

    public function testLikeCommentReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->comment1);
        $replyLikes = $reply->likes_count;

        $this->browse(function ($browser) use ($reply, $replyLikes) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openCommentsTab()
                ->addActionToComment('like', $reply)
                ->assertCommentHasActionCount('like', $reply, $replyLikes + 1)
                ;
        });
    }

    public function testFlagCommentReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->comment1);
        $replyFlags = $reply->flags_count;

        $this->browse(function ($browser) use ($reply, $replyFlags) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openCommentsTab()
                ->addActionToComment('flag', $reply)
                ->assertCommentHasActionCount('flag', $reply, $replyFlags + 1)
                ;
        });
    }

    public function testLikeNoteOnDocument()
    {
        $noteLikes = $this->note1->likes_count;

        $this->browse(function ($browser) use ($noteLikes) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openNotesPane()
                ->addActionToNote('like', $this->note1)
                ->assertNoteHasActionCount('like', $this->note1, $noteLikes +1)
                ;
        });
    }

    public function testFlagNoteOnDocument()
    {
        $noteFlags = $this->note1->flags_count;

        $this->browse(function ($browser) use ($noteFlags) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openNotesPane()
                ->addActionToNote('flag', $this->note1)
                ->assertNoteHasActionCount('flag', $this->note1, $noteFlags +1)
                ;
        });
    }

    public function testLikeNoteReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->note1);
        $replyLikes = $reply->likes_count;

        $this->browse(function ($browser) use ($reply, $replyLikes) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openNotesPane()
                ->addActionToComment('like', $reply)
                ->assertCommentHasActionCount('like', $reply, $replyLikes + 1)
                ;
        });
    }

    public function testFlagNoteReply()
    {
        $reply = FactoryHelpers::createComment($this->sponsorUser, $this->note1);
        $replyFlags = $reply->flags_count;

        $this->browse(function ($browser) use ($reply, $replyFlags) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->openNotesPane()
                ->addActionToComment('flag', $reply)
                ->assertCommentHasActionCount('flag', $reply, $replyFlags + 1)
                ;
        });
    }

    public function testSupportDocument()
    {
        $documentSupport = $this->document->support;

        $this->browse(function ($browser) use ($documentSupport) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->click('@supportBtn')
                ->assertDocumentSupportCount($documentSupport + 1)
                ;
        });
    }

    public function testOpposeDocument()
    {
        $documentOppose = $this->document->oppose;

        $this->browse(function ($browser) use ($documentOppose) {
            $browser
                ->loginAs($this->user)
                ->visit(new DocumentPage($this->document))
                ->click('@opposeBtn')
                ->assertDocumentOpposeCount($documentOppose + 1)
                ;
        });
    }
}
