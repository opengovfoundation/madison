<?php

namespace Tests\Browser;

use App\Http\Controllers\AdminController;
use Tests\Browser\AdminTestCase;
use Tests\Browser\Pages\Admin;
use Laravel\Dusk\Browser;
use Tests\FactoryHelpers;
use App\Models\Sponsor;

class ManageSponsorsTest extends AdminTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->sponsor = FactoryHelpers::createActiveSponsorWithUser($this->user);
    }

    public function testChangeSponsorToActive()
    {
        $this->sponsor->status = Sponsor::STATUS_PENDING;
        $this->sponsor->save();

        $this->browse(function ($browser) {
            $page = new Admin\ManageSponsorsPage;

            $this->assertFalse($this->user->isAdmin());
            $this->assertNonAdminsDenied($browser, $page);

            $this->assertTrue($this->sponsor->status === Sponsor::STATUS_PENDING);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->assertSee($this->sponsor->name)
                ->assertSelected('status', Sponsor::STATUS_PENDING)
                ->select('status', Sponsor::STATUS_ACTIVE)
                ->assertVisible('.alert.alert-info') // some success
                ->assertSelected('status', Sponsor::STATUS_ACTIVE)
                ;

            $this->sponsor = $this->sponsor->fresh();
            $this->assertTrue($this->sponsor->status === Sponsor::STATUS_ACTIVE);
        });
    }

    public function testChangeSponsorToPending()
    {
        $this->browse(function ($browser) {
            $page = new Admin\ManageSponsorsPage;

            $this->assertFalse($this->user->isAdmin());
            $this->assertNonAdminsDenied($browser, $page);

            $this->assertTrue($this->sponsor->status === Sponsor::STATUS_ACTIVE);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ->assertSee($this->sponsor->name)
                ->assertSelected('status', Sponsor::STATUS_ACTIVE)
                ->select('status', Sponsor::STATUS_PENDING)
                ->assertVisible('.alert.alert-info') // some success
                ->assertSelected('status', Sponsor::STATUS_PENDING)
                ;

            $this->sponsor = $this->sponsor->fresh();
            $this->assertTrue($this->sponsor->status === Sponsor::STATUS_PENDING);
        });
    }
}
