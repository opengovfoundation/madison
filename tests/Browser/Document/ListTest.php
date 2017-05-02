<?php

namespace Tests\Browser\Document;

use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\User;
use Tests\Browser\Pages\Document\ListPage;
use Tests\DuskTestCase;
use Tests\FactoryHelpers;

class ListTest extends DuskTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->sponsorUser = factory(User::class)->create();
        $this->sponsor = FactoryHelpers::createActiveSponsorWithUser($this->sponsorUser);
    }

    public function testNoDocs()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit(new ListPage)
                ->assertSee(trans('messages.document.list'))
                ;
        });
    }

    public function testAnonCanOnlySeePublished()
    {
        $this->genDocs(3);

        $this->documents[1]->publish_state = Document::PUBLISH_STATE_UNPUBLISHED;
        $this->documents[1]->save();

        $this->documents[2]->publish_state = Document::PUBLISH_STATE_PRIVATE;
        $this->documents[2]->save();

        $this->browse(function ($browser) {
            $browser
                ->visit(new ListPage)
                ;

            $visibleDoc = $this->documents->shift();
            $browser->assertSee($visibleDoc->title);

            foreach ($this->documents as $document) {
                $browser->assertDontSee($document->title);
            }
        });
    }

    public function testNonSponsorCanOnlySeePublished()
    {
        $normalUser = factory(User::class)->create();

        $this->genDocs(3);

        $this->documents[1]->publish_state = Document::PUBLISH_STATE_UNPUBLISHED;
        $this->documents[1]->save();

        $this->documents[2]->publish_state = Document::PUBLISH_STATE_PRIVATE;
        $this->documents[2]->save();

        $this->browse(function ($browser) use ($normalUser) {
            $browser
                ->loginAs($normalUser)
                ->visit(new ListPage)
                ;

            $visibleDoc = $this->documents->shift();
            $browser->assertSee($visibleDoc->title);

            foreach ($this->documents as $document) {
                $browser->assertDontSee($document->title);
            }
        });
    }

    public function testAdminCanSeeAllNonDeletedByDefault()
    {
        $admin = factory(User::class)->create()->makeAdmin();

        $this->genDocs(3);

        $this->documents[1]->publish_state = Document::PUBLISH_STATE_UNPUBLISHED;
        $this->documents[1]->save();

        $this->documents[2]->publish_state = Document::PUBLISH_STATE_PRIVATE;
        $this->documents[2]->save();

        $this->browse(function ($browser) use ($admin) {
            $browser
                ->loginAs($admin)
                ->visit(new ListPage)
                ;

            foreach ($this->documents as $document) {
                $browser->assertSee($document->title);
            }
        });
    }

    public function testSponsorCanSeeAllOfOwnDocuments()
    {
        $this->genDocs(3);

        $this->documents[1]->publish_state = Document::PUBLISH_STATE_UNPUBLISHED;
        $this->documents[1]->save();

        $this->documents[2]->publish_state = Document::PUBLISH_STATE_PRIVATE;
        $this->documents[2]->save();

        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->sponsorUser)
                ->visit(new ListPage)
                ;

            foreach ($this->documents as $document) {
                $browser->assertSee($document->title);
            }
        });
    }

    public function testSponsorCanNotSeeAllOfOtherSponsorDocuments()
    {
        $otherSponsorUser = factory(User::class)->create();
        $otherSponsor = FactoryHelpers::createActiveSponsorWithUser($otherSponsorUser);

        $this->genDocs(3);

        $this->documents[1]->publish_state = Document::PUBLISH_STATE_UNPUBLISHED;
        $this->documents[1]->sponsors()->sync([$otherSponsor->id]);
        $this->documents[1]->save();

        $this->documents[2]->publish_state = Document::PUBLISH_STATE_PRIVATE;
        $this->documents[2]->sponsors()->sync([$otherSponsor->id]);
        $this->documents[2]->save();

        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->sponsorUser)
                ->visit(new ListPage)
                ;

            $visibleDoc = $this->documents->shift();
            $browser->assertSee($visibleDoc->title);

            foreach ($this->documents as $document) {
                $browser->assertDontSee($document->title);
            }
        });
    }

    protected function genDocs($num)
    {
        $this->documents = factory(Document::class, $num)->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
        ])->each(function ($document) {
            $document->sponsors()->save($this->sponsor);
            $document->content()->save(factory(DocContent::class)->make());
            $document->setIntroText('hello world');

            FactoryHelpers::createComment($this->sponsorUser, $document);
        });
    }
}
