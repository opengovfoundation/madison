<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Tests\Browser\Pages\HomePage;

use Carbon\Carbon;
use App\Models\Doc as Document;

class HomeTest extends DuskTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->document1 = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
        ]);

        $this->document2 = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
            'created_at' => Carbon::now()->addDay(),
        ]);
    }

    public function testFeaturedHiddenIfNoneSet()
    {
        $this->browse(function ($browser) {
            $browser->visit(new HomePage);
            $browser->assertDontSee('@featured');
        });
    }

    public function testFeaturedDocument()
    {
        $this->browse(function ($browser) {
            $this->document1->setAsFeatured();

            $browser->visit(new HomePage);

            $browser->assertSeeIn('@featured', $this->document1->title);
            $browser->assertDontSeeIn('@featured', $this->document2->title);
        });
    }

    public function testMultipleFeaturedDocuments()
    {
        $this->browse(function ($browser) {
            $this->document1->setAsFeatured();
            $this->document2->setAsFeatured();

            $browser->visit(new HomePage);

            $browser->assertSeeIn('@featured', $this->document1->title);
            $browser->assertSeeIn('@featured', $this->document2->title);
        });
    }

    public function testUnpublishedWontShowInFeatured()
    {
        $this->browse(function ($browser) {
            $this->document1->setAsFeatured();

            $browser->visit(new HomePage);
            $browser->assertSeeIn('@featured', $this->document1->title);

            // Test setting to unpublished removes from featured
            $this->document1->update(['publish_state' => Document::PUBLISH_STATE_UNPUBLISHED]);

            $browser->visit(new HomePage);
            $browser->assertDontSee('@featured');

            // Test it becomes featured again after republishing
            $this->document1->update(['publish_state' => Document::PUBLISH_STATE_PUBLISHED]);

            $browser->visit(new HomePage);
            $browser->assertSeeIn('@featured', $this->document1->title);
        });
    }

    public function testPrivateWontShowInFeatured()
    {
        $this->browse(function ($browser) {
            $this->document1->setAsFeatured();

            $browser->visit(new HomePage);
            $browser->assertSeeIn('@featured', $this->document1->title);

            // Test setting to unpublished removes from featured
            $this->document1->update(['publish_state' => Document::PUBLISH_STATE_PRIVATE]);

            $browser->visit(new HomePage);
            $browser->assertDontSee('@featured');

            // Test it becomes featured again after republishing
            $this->document1->update(['publish_state' => Document::PUBLISH_STATE_PUBLISHED]);

            $browser->visit(new HomePage);
            $browser->assertSeeIn('@featured', $this->document1->title);
        });
    }

    public function testDeletedRemovesFromFeaturedList()
    {
        $this->browse(function ($browser) {
            $this->document1->setAsFeatured();

            $browser->visit(new HomePage);
            $browser->assertSeeIn('@featured', $this->document1->title);

            // Delete
            $this->document1->update(['publish_state' => Document::PUBLISH_STATE_DELETED_USER]);
            $this->document1->delete();

            $browser->visit(new HomePage);
            $browser->assertDontSee('@featured');

            // Test it isn't featured anymore if restored
            $this->document1->restore();

            $browser->visit(new HomePage);
            $browser->assertDontSee('@featured');
        });
    }
}
