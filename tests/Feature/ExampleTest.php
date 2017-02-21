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
        $response = $this->get('/');
        $response->assertStatus(200);
    }

}
