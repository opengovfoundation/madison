<?php

namespace Tests\Browser;

use App\Models\Doc as Document;
use Tests\Browser\AdminTestCase;
use Tests\Browser\Pages\Admin;
use Laravel\Dusk\Browser;

class FeaturedDocumentsTest extends AdminTestCase
{
    public function testNone()
    {
        $this->browse(function ($browser) {
            $page = new Admin\FeaturedDocumentsPage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->assertSee(trans('messages.admin.featured_documents'))
                ;
        });
    }

    public function testList()
    {
        $documents = factory(Document::class, 3)->create();

        $this->assertEquals([], Document::getFeaturedDocumentIds()->toArray());

        $featuredDocs = $documents
            ->take(2)
            ->each
            ->setAsFeatured()
            // have the collection be in "featured" order
            ->reverse()
            ->values()
            ;
        $nonFeaturedDocs = $documents->take(-1);

        $this->assertEquals(
            $featuredDocs->pluck('id'),
            Document::getFeaturedDocumentIds()
        );

        $this->browse(function ($browser) use ($featuredDocs, $nonFeaturedDocs) {
            $page = new Admin\FeaturedDocumentsPage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ;

            foreach ($featuredDocs as $document) {
                $browser->onDocumentRow($document, function ($row) use ($document) {
                    $row->assertSee($document->title);
                });
            }

            foreach ($nonFeaturedDocs as $document) {
                $browser->assertDontSeeIn('table', $document->title);
            }

            # Correct buttons are disabled
            $browser
                ->onDocumentRow($featuredDocs->first(), function ($row) {
                    $row
                        ->assertVisible('@moveUpBtnDisabled')
                        ->assertVisible('@moveDownBtn')
                        ->assertVisible('@unfeatureBtn')
                        ;
                })
                ->onDocumentRow($featuredDocs->last(), function ($row) {
                    $row
                        ->assertVisible('@moveDownBtnDisabled')
                        ->assertVisible('@moveUpBtn')
                        ->assertVisible('@unfeatureBtn')
                        ;
                })
                ;
        });
    }

    /**
     * @depends testList
     */
    public function testMove()
    {
        $documents = factory(Document::class, 2)
            ->create()
            ->each
            ->setAsFeatured()
            ->reverse()
            ->values();

        $this->browse(function ($browser) use ($documents) {
            $page = new Admin\FeaturedDocumentsPage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ;

            $browser
                ->onDocumentRow($documents->last(), function ($row) {
                    $row
                        ->click('@moveUpBtn')
                        ;
                })
                ->assertPathIs($page->url())
                ->assertVisible('.alert.alert-info')
                ->onDocumentRow($documents->last(), function ($row) {
                    $row
                        ->assertVisible('@moveUpBtnDisabled')
                        ->assertVisible('@moveDownBtn')
                        ;
                })
                ;
        });
    }

    /**
     * @depends testList
     */
    public function testRemove()
    {
        $documents = factory(Document::class, 2)
            ->create()
            ->each
            ->setAsFeatured()
            ->reverse()
            ->values();

        $this->browse(function ($browser) use ($documents) {
            $page = new Admin\FeaturedDocumentsPage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ;

            $unfeaturedDoc = $documents->shift();
            $browser
                ->onDocumentRow($unfeaturedDoc, function ($row) {
                    $row
                        ->click('@unfeatureBtn')
                        ;
                })
                ->assertPathIs($page->url())
                ->assertVisible('.alert.alert-info')
                ->assertDontSeeIn('table', $unfeaturedDoc->title)
                ;

            foreach ($documents as $document) {
                $browser->onDocumentRow($document, function ($row) use ($document) {
                    $row->assertSee($document->title);
                });
            }

            $this->assertEquals(
                $documents->pluck('id'),
                Document::getFeaturedDocumentIds()
            );
        });
    }

    /**
     * @depends testList
     */
    public function testAdd()
    {
        $documents = factory(Document::class, 3)->create();

        $this->browse(function ($browser) use ($documents) {
            $page = new Admin\FeaturedDocumentsPage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->select('add_featured_doc_id', $documents->last()->id)
                ->click('@addFeaturedDocBtn')
                ->assertPathIs($page->url())
                ->assertVisible('.alert.alert-info')
                ->assertSeeIn('table', $documents->last()->title)
                ;

            $this->assertEquals(
                collect([$documents->last()->id]),
                Document::getFeaturedDocumentIds()
            );
        });
    }
}
