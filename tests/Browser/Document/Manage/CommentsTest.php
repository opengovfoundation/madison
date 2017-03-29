<?php

namespace Tests\Browser\Document\Manage;

use App;
use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\Sponsor;
use App\Models\User;
use Tests\Browser\Pages\Document\Manage\CommentsPage;
use Tests\DuskTestCase;
use Tests\FactoryHelpers;

class CommentsTest extends DuskTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->sponsorOwner = factory(User::class)->create();
        $this->sponsor = FactoryHelpers::createActiveSponsorWithUser($this->sponsorOwner);

        $this->document = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_UNPUBLISHED,
        ]);
        $this->document->content()->save(factory(DocContent::class)->make());
        $this->document->sponsors()->save($this->sponsor);

        $this->page = new CommentsPage($this->document);
    }

    public function testAdminCanModerateDocumentComments()
    {
        $admin = factory(User::class)->create()->makeAdmin();
        $this->userCanModerateComments($admin);
    }

    public function testSponsorOwnerCanModerateDocumentComments()
    {
        $this->userCanModerateComments($this->sponsorOwner);
    }

    public function testSponsorEditorCanModerateDocumentComments()
    {
        $editor = factory(User::class)->create();
        $this->sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);
        $this->userCanModerateComments($editor);
    }

    public function testSponsorStaffCanModerateDocumentComments()
    {
        $staff = factory(User::class)->create();
        $this->sponsor->addMember($staff->id, Sponsor::ROLE_STAFF);
        $this->userCanModerateComments($staff);
    }

    public function testSponsorCannotEditOtherDocument()
    {
        $otherSponsorOwner = factory(User::class)->create();
        $otherSponsor = FactoryHelpers::createActiveSponsorWithUser($otherSponsorOwner);

        $otherDocument = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_UNPUBLISHED,
        ]);
        $otherDocument->content()->save(factory(DocContent::class)->make());
        $otherDocument->sponsors()->save($otherSponsor);

        $this->browse(function ($browser) use ($otherDocument) {
            $browser
                ->loginAs($this->sponsorOwner)
                ->visit(new CommentsPage($otherDocument))
                // 403 status
                ->assertSee('Whoops, looks like something went wrong')
                ;
        });
    }

    public function testSectionsAreEmptyWithNoFlaggedComments()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->sponsorOwner)
                ->visit($this->page)
                ->assertSeeIn('@unhandledSection', trans('messages.none'))
                ->assertSeeIn('@handledSection', trans('messages.none'))
                ;
        });
    }

    protected function userCanModerateComments($user)
    {
        $comments = collect([]);
        $annotationService = App::make('App\Services\Annotations');

        foreach (range(0, 2) as $idx) {
            $comment = FactoryHelpers::createComment($this->sponsorOwner, $this->document);

            $annotationService->createAnnotationFlag($comment, $this->sponsorOwner, []);
            $comments->push($comment);
        }

        $this->browse(function ($browser) use ($user, $comments) {
            $browser
                ->loginAs($user)
                ->visit($this->page)
                ->with('@unhandledSection', function ($section) use ($comments) {
                    foreach ($comments as $comment) {
                        $section
                            ->assertVisible($this->page->getCommentRowSelector($comment))
                            ;
                    }
                })
                ->onCommentRow($comments[0], function ($row) use ($comments) {
                    $row
                        ->press('Hide')
                        ->assertPathIs($this->page->url())
                        ->assertSee('Hidden')
                        ;

                    $this->assertTrue($comments[0]->isHidden());
                })
                ->onCommentRow($comments[1], function ($row) use ($comments) {
                    $row
                        ->press('Resolve')
                        ->assertPathIs($this->page->url())
                        ->assertSee('Resolved')
                        ;

                    $this->assertTrue($comments[1]->isResolved());
                })
                ->assertVisible(
                    '@unhandledSection '.$this->page->getCommentRowSelector($comments->last())
                )
                ->with('@handledSection', function ($section) use ($comments) {
                    foreach ($comments->take(2) as $comment) {
                        $section
                            ->assertVisible($this->page->getCommentRowSelector($comment))
                            ;
                    }
                })
                ;
        });
    }
}
