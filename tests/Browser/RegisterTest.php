<?php

namespace Tests\Browser;

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\RegisterPage;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    public function testUserCanRegister()
    {
        $fakeUser = factory(User::class)->make();

        // TODO: currently doesn't work with Dusk
        // Event::fake();

        $this->browse(function ($browser) use ($fakeUser) {
            $browser
                ->visit(new RegisterPage)
                ->fillAndSubmitRegisterForm($fakeUser)
                ->assertPathIs('/')
                ->assertSee('You haven\'t verified your email')
                ;

            // TODO: currently doesn't work with Dusk
            // Event::assertDispatched('Illuminate\Auth\Events\Registered');

            $user = User::first();
            $this->assertEquals($fakeUser->fname, $user->fname);
            $this->assertEquals($fakeUser->lname, $user->lname);
            $this->assertEquals($fakeUser->email, $user->email);
        });
    }

    /**
     * @depends testUserCanRegister
     */
    public function testRedirectParameter()
    {
        $fakeUser = factory(User::class)->make();

        $this->browse(function ($browser) use ($fakeUser) {
            $redirectPath = route('users.settings.account.edit', ['1'], false);
            $browser
                ->visit('/register?redirect='.$redirectPath)
                ->fillAndSubmitRegisterForm($fakeUser)
                ->assertPathIs($redirectPath)
                ->assertSee('You haven\'t verified your email')
                ;
        });
    }
}
