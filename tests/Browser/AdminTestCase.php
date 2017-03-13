<?php

namespace Tests\Browser;

use App\Models\User;
use App\Http\Controllers\AdminController;
use SiteConfigSaver;
use Tests\DuskTestCase;
use Tests\Browser\Pages\Admin;
use Laravel\Dusk\Browser;

class AdminTestCase extends DuskTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->admin = factory(User::class)->create()->makeAdmin();
        $this->user = factory(User::class)->create();
    }

    public function assertNonAdminsDenied($browser, $page)
    {
        // anonymous user
        $browser
            ->visit($page->url())
            ->assertPathIs('/login')
            ;

        // non-admin
        $browser
            ->loginAs($this->user)
            ->visit($page)
            // 403 status
            ->assertSee('Whoops, looks like something went wrong')
            ;
    }
}
