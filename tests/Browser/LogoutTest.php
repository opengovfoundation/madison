<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;

class LogoutTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testUserCanLogOut()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $home = new HomePage;
            $browser
                ->loginAs($user)
                ->assertAuthenticatedAs($user)
                ->visit($home)
                ->clickLink('Logout')
                ->assertPathIs($home->url())
                ->assertGuest()
                ;
        });
    }
}
