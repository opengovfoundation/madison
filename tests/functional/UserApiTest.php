<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testGetCurrentUser()
    {
        $user = factory(App\Models\User::class)->create();

        $this->actingAs($user)
            ->get('/api/user/current')
            ->seeJson([
                'activeSponsorId' => null,
                'sponsors' => [],
                'user' => $user->toArray()
            ]);
    }
}
