<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Doc as Document;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Example Http test.
     *
     * GET /
     */
    public function testHome()
    {
        // Need at least one published document for homepage to load
        $document = factory(Document::class)->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
        ]);

        // Assert what we expect to be in the database
        $this->assertDatabaseHas('docs', [
            'title' => $document->title,
            'slug' => $document->slug,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
    }

}
