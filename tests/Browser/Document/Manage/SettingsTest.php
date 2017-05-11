<?php

namespace Tests\Browser\Document\Manage;

use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\Sponsor;
use App\Models\User;
use Tests\Browser\Pages\Document\Manage\SettingsPage;
use Tests\DuskTestCase;
use Tests\FactoryHelpers;

class SettingsTest extends DuskTestCase
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
        $this->page = new SettingsPage($this->document);
    }

    public function testAdminCanEditDocument()
    {
        $admin = factory(User::class)->create()->makeAdmin();
        $this->userCanEditDocTest($admin);
    }

    public function testSponsorOwnerCanEditDocument()
    {
        $this->userCanEditDocTest($this->sponsorOwner);
    }

    public function testSponsorEditorCanEditDocument()
    {
        $editor = factory(User::class)->create();
        $this->sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);
        $this->userCanEditDocTest($editor);
    }

    public function testSponsorStaffCannotEditDocument()
    {
        $staff = factory(User::class)->create();
        $this->sponsor->addMember($staff->id, Sponsor::ROLE_STAFF);

        $this->browse(function ($browser) use ($staff) {
            $browser
                ->loginAs($staff)
                ->visit($this->page)
                ->assertVisible('fieldset[disabled]')
                ;
        });
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
                ->visit(new SettingsPage($otherDocument))
                // 403 status
                ->assertSee('403')
                ;
        });
    }

    protected function userCanEditDocTest($user)
    {
        $newData = [
            'title' => 'test',
            'introtext' => 'Some introtext',
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
            'discussion_state' => Document::DISCUSSION_STATE_CLOSED,
            'slug' => 'my-document',
            'new_page_content' => 'Page 2 content',
        ];

        $this->browse(function ($browser) use ($user, $newData) {
            $browser
                ->loginAs($user)
                ->visit($this->page)
                ->type('title', $newData['title'])
                ->waitForCodeMirror()
                ->setCodeMirrorTextForField('introtext', $newData['introtext'])
                ->select('publish_state', $newData['publish_state'])
                ->select('discussion_state', $newData['discussion_state'])
                ->click('@submitBtn')
                ->assertPathIs($this->page->url())
                ->assertVisible('.alert.alert-info')
                ->waitForCodeMirror()
                ->assertInputValue('title', $newData['title'])
                ->assertSeeIn(SettingsPage::codeMirrorSelectorForField('introtext'), $newData['introtext'])
                ->assertSelected('publish_state', $newData['publish_state'])
                ->assertSelected('discussion_state', $newData['discussion_state'])
                ;

            $this->document = $this->document->fresh();
            $this->assertEquals($newData['title'], $this->document->title);
            $this->assertEquals($newData['introtext'], $this->document->introtext);
            $this->assertEquals($newData['publish_state'], $this->document->publish_state);
            $this->assertEquals($newData['discussion_state'], $this->document->discussion_state);

            $browser->driver->executeScript('document.getElementById("add-page-form").submit();');
            $browser
                //->click('@addPageBtn')
                ->assertPathIs($this->page->url())
                ->assertQueryStringHas('page', '2')
                ->assertVisible('.document-pages-toolbar .pagination')
                ->assertInputValue('page_content', '')
                ->waitForCodeMirror()
                ->setCodeMirrorTextForField('page_content', $newData['new_page_content'])
                ->click('@submitBtn')
                ->assertPathIs($this->page->url())
                ->assertQueryStringHas('page', '2')
                ->assertVisible('.alert.alert-info')
                ->waitForCodeMirror()
                ->assertSeeIn(SettingsPage::codeMirrorSelectorForField('page_content'), $newData['new_page_content'])
                ;

            $this->assertEquals($newData['new_page_content'], $this->document->content()->where('page', 2)->first()->content);

            $browser
                ->type('slug', $newData['slug'])
                ->click('@submitBtn')
                ->assertInputValue('slug', $newData['slug'])
                ->assertPathIs("/documents/{$newData['slug']}/manage/settings")
                ;

            $this->document = $this->document->fresh();
            $this->assertEquals($newData['slug'], $this->document->slug);
        });
    }
}
