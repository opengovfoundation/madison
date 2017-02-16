<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Tests\Browser\Pages\User\Settings\AccountPage;
use Laravel\Dusk\Browser;

class UserTest extends DuskTestCase
{
    public function testAccountSettingsViewOwn()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $page = new AccountPage($user);

            $browser
                ->loginAs($user)
                ->visit($page)
                ->assertInputValue('fname', $user->fname)
                ->assertInputValue('lname', $user->lname)
                ->assertInputValue('email', $user->email)
                ;
        });
    }

    public function testAccountSettingsNotViewOthers()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $this->browse(function ($browser) use ($user, $otherUser) {
            $page = new AccountPage($otherUser);

            $browser
                ->loginAs($user)
                ->visit($page)
                // 403 status
                ->assertSee('Whoops, looks like something went wrong')
                ;

            // anonymous user
            $browser
                ->visit($page)
                // 403 status
                ->assertSee('Whoops, looks like something went wrong')
                ;
        });
    }

    public function testAccountSettingsUpdate()
    {
        $user = factory(User::class)->create();
        $fakeUser = factory(User::class)->make();

        $this->browse(function ($browser) use ($user, $fakeUser) {
            $page = new AccountPage($user);

            $browser
                ->loginAs($user)
                ->visit($page)
                ->type('fname', $fakeUser->fname)
                ->type('lname', $fakeUser->lname)
                ->type('email', $fakeUser->email)
                ->press('Submit')
                ->assertPathIs($page->url())
                // some success
                ->assertVisible('.alert.alert-info')
                // inputs have correct input
                ->assertInputValue('fname', $fakeUser->fname)
                ->assertInputValue('lname', $fakeUser->lname)
                ->assertInputValue('email', $fakeUser->email)
                ;

            // ensure changes actually happened in db
            $user = $user->fresh();
            $this->assertEquals($fakeUser->fname, $user->fname);
            $this->assertEquals($fakeUser->lname, $user->lname);
            $this->assertEquals($fakeUser->email, $user->email);
        });
    }

    public function testAccountSettingsUniqueEmail()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $this->browse(function ($browser) use ($user, $otherUser) {
            $page = new AccountPage($user);
            $originalEmail = $user->email;

            $browser
                ->loginAs($user)
                ->visit($page)
                ->type('email', $otherUser->email)
                ->press('Submit')
                ->assertPathIs($page->url())
                // some error
                ->assertVisible('.alert.alert-danger')
                // input still has their invalid input
                ->assertInputValue('email', $otherUser->email)
                ;

            // ensure changes did not reach db
            $user = $user->fresh();
            $this->assertEquals($originalEmail, $user->email);
        });
    }
}
