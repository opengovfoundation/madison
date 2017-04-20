<?php

namespace Tests\Browser\Pages\Sponsor;

use App\Models\Doc as Document;
use App\Models\Sponsor;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class DocumentsPage extends Page
{
    public $sponsor;

    public function __construct(Sponsor $sponsor)
    {
        $this->sponsor = $sponsor;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('sponsors.documents.index', [$this->sponsor], false);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    public function clickDeleteDocumentButton(Browser $browser, Document $document)
    {
        $browser->with('tr#document-' . $document->id, function ($documentRow) {
            $documentRow->click('.delete-document');
        });
    }

    public function clickRestoreDocumentButton(Browser $browser, Document $document)
    {
        $browser->with('tr#document-' . $document->id, function ($documentRow) {
            $documentRow->click('.restore-document');
        });
    }

    public function deleteDocumentAndAssertSuccess(Browser $browser, Document $document)
    {
        $browser
            ->clickDeleteDocumentButton($document)
            ->assertVisible('.alert.alert-info') // Success flash
            ->assertDontSee($document->title) // Document not in main list
            ->clickLink(trans('messages.document.view_deleted')) // Go to deleted page
            ->assertSee($document->title) // See in deleted list
            ->visit($this->url()) // go back to where we started
            ;
    }

    public function restoreDocumentAndAssertSuccess(Browser $browser, Document $document)
    {
        $browser
            ->clickRestoreDocumentButton($document)
            ->assertVisible('.alert.alert-info') // Success flash
            ->visit(route('sponsors.documents.index', [$this->sponsor, 'deleted' => true], false))
            ->assertDontSee($document->title) // Document not in main list
            ->clickLink(trans('messages.document.view_documents')) // Go to deleted page
            ->assertSee($document->title) // See in deleted list
            ;
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@newDocumentButton' => '.new-document',
            '@newDocumentModal' => '#new-document-modal',
        ];
    }

}
