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

        $this->user = factory(User::class)->create();
        $this->sponsor = FactoryHelpers::createActiveSponsorWithUser($this->user);

        $this->document = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
        ]);

        $this->document->content()->save(factory(DocContent::class)->make());

        $this->document->sponsors()->save($this->sponsor);

        $firstWord = explode(' ', $this->document->content()->first()->content)[0];
        $secondWord = explode(' ', $this->document->content()->first()->content)[1];

        $this->note1 = FactoryHelpers::addNoteToDocument($this->user, $this->document, $firstWord);
        $this->note2 = FactoryHelpers::addNoteToDocument($this->user, $this->document, $secondWord);

        $this->comment1 = FactoryHelpers::createComment($this->user, $this->document);
        $this->comment2 = FactoryHelpers::createComment($this->user, $this->document);

        $this->commentReply = FactoryHelpers::createComment($this->user, $this->comment1);
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
                ->addActionToComment('like', $this->comment1)
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfFlagCommentWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->addActionToComment('flag', $this->comment1)
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfLikeNoteWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
                ->addActionToNote('like', $this->note1)
                ->assertPathIs('/login')
                ;
        });
    }

    public function testLoginRedirectIfFlagNoteWhenNotLoggedIn()
    {
        $this->browse(function ($browser) {
            $browser->visit(new DocumentPage($this->document))
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
}
