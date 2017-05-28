<?php

namespace Tests\Browser;

use App\Models\User;
use App\Http\Controllers\AdminController;
use Tests\Browser\AdminTestCase;
use Tests\Browser\Pages\Admin;
use Laravel\Dusk\Browser;

class ManageUsersTest extends AdminTestCase
{
    public function testMakeAdmin()
    {
        $this->browse(function ($browser) {
            $page = new Admin\ManageUsersPage;

            $this->assertFalse($this->user->isAdmin());
            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->assertSee(trans('messages.user.make_admin'))
                ->click('@toggleAdminBtn')
                ->assertVisible('.alert.alert-info') // some success
                ->assertSee(trans('messages.user.remove_admin'))
                ;

            $this->assertTrue($this->user->isAdmin());
        });
    }

    public function testRemoveAdmin()
    {
        $this->browse(function ($browser) {
            $page = new Admin\ManageUsersPage;

            $this->assertNonAdminsDenied($browser, $page);
            $this->user->makeAdmin();

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->assertSee(trans('messages.user.remove_admin'))
                ->click('@toggleAdminBtn')
                ->assertVisible('.alert.alert-info') // some success
                ->assertSee(trans('messages.user.make_admin'))
                ;

            $this->assertFalse($this->user->isAdmin());
        });
    }

    public function testSearchUsers()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs($this->admin)
                ->visit(new Admin\ManageUsersPage)
                ->press(trans('messages.advanced_search'))
                ->waitFor('#queryModal')
                ->pause(500)
                ->type('#q', $this->user->fname)
                ->press(trans('messages.submit'))
                ->assertSee($this->user->email)
                ->assertDontSee($this->admin->email)
                ;
        });
    }
}
