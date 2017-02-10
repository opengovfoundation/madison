<?php

namespace Tests\Browser;

use App\Models\Doc as Document;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        // Need at least one published document for homepage to load
        $document = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Madison');
        });
    }
}
