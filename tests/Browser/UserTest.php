<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\NotificationPreference;
use Tests\DuskTestCase;
use Tests\Browser\Pages\User\Settings\AccountPage;
use Tests\Browser\Pages\User\Settings\PasswordPage;
use Tests\Browser\Pages\User\Settings\NotificationsPage;
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

    public function testPasswordSettingsViewOwn()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $page = new PasswordPage($user);

            $browser
                ->loginAs($user)
                ->visit($page)
                // ensure we don't show passwords
                ->assertInputValue('new_password', '')
                ->assertInputValue('new_password_confirmation', '')
                ;
        });
    }

    public function testPasswordSettingsUpdate()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $page = new PasswordPage($user);

            $oldPasswordHash = $user->password;
            $newPassword = str_random();

            $browser
                ->loginAs($user)
                ->visit($page)
                ->type('new_password', $newPassword)
                ->type('new_password_confirmation', $newPassword)
                ->press('Submit')
                // some success
                ->assertVisible('.alert.alert-info')
                ;

            $user = $user->fresh();
            $this->assertNotEquals($oldPasswordHash, $user->password);
        });
    }

    public function testNotificationsSettingsViewOwn()
    {
        $user = factory(User::class)->create();
        $this->notificationsSettingsViewOwnCommon($user);
    }

    public function testNotificationsSettingsViewOwnAdmin()
    {
        $user = factory(User::class)->create();
        $user->makeAdmin();
        $this->notificationsSettingsViewOwnCommon($user);
    }

    protected function notificationsSettingsViewOwnCommon($user)
    {
        $events = NotificationPreference::getValidNotificationsForUser($user);

        foreach ($events as $eventName => $eventClass) {
            NotificationPreference::addNotificationForUser($eventName, $user->id);
        }

        $this->browse(function ($browser) use ($user, $events) {
            $page = new NotificationsPage($user);

            $browser
                ->loginAs($user)
                ->visit($page)
                ;

            $activePreferences = $user->notificationPreferences->pluck('event');

            foreach ($activePreferences as $eventName) {
                $browser->assertChecked($eventName);
            }

            foreach (collect(array_keys($events))->diff($activePreferences) as $eventName) {
                $browser->assertNotChecked($eventName);
            }
        });
    }

    public function testNotificationsSettingsUpdate()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $page = new NotificationsPage($user);
            $events = array_keys(NotificationPreference::getValidNotificationsForUser($user));

            $browser
                ->loginAs($user)
                ->visit($page)
                ->check($events[0])
                ->press('Submit')
                // some success
                ->assertVisible('.alert.alert-info')
                ->assertChecked($events[0])
                ;
        });
    }
}
