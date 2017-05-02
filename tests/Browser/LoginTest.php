<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Pages\LoginPage;

class LoginTest extends DuskTestCase
{
    public function testUserCanLogIn()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $browser
                ->visit(new LoginPage)
                ->fillAndSubmitLoginForm($user)
                ->assertPathIs('/')
                ->assertAuthenticatedAs($user)
                ;
        });
    }

    /**
     * @depends testUserCanLogIn
     */
    public function testLoginRedirectParameter()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $redirectPath = route('users.settings.account.edit', [$user], false);
            $browser
                ->visit('/login?redirect='.$redirectPath)
                ->fillAndSubmitLoginForm($user)
                ->assertPathIs($redirectPath)
                ->assertAuthenticatedAs($user)
                ;
        });
    }
}
