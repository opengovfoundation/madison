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
                ->assertValue('table select[name=status]', Sponsor::STATUS_PENDING)
                ->select('table select[name=status]', Sponsor::STATUS_ACTIVE)
                ->assertVisible('.alert.alert-info') // some success
                ->assertValue('table select[name=status]', Sponsor::STATUS_ACTIVE)
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
                ->assertValue('table select[name=status]', Sponsor::STATUS_ACTIVE)
                ->select('table select[name=status]', Sponsor::STATUS_PENDING)
                ->assertVisible('.alert.alert-info') // some success
                ->assertValue('table select[name=status]', Sponsor::STATUS_PENDING)
                ;

            $this->sponsor = $this->sponsor->fresh();
            $this->assertTrue($this->sponsor->status === Sponsor::STATUS_PENDING);
        });
    }

    public function testSearchSponsors()
    {
        $sponsor1 = factory(Sponsor::class)->create();
        $sponsor2 = factory(Sponsor::class)->create();

        $this->browse(function ($browser) use ($sponsor1, $sponsor2) {
            $browser
                ->loginAs($this->admin)
                ->visit(new Admin\ManageSponsorsPage)
                ->press(trans('messages.advanced_search'))
                ->waitFor('#queryModal')
                ->pause(500)
                ->type('#q', $sponsor1->name)
                ->press(trans('messages.submit'))
                ->assertSee($sponsor1->name)
                ->assertDontSee($sponsor2->name)
                ;
        });
    }

    public function testFilterSponsorsByStatus()
    {
        $activeSponsor = factory(Sponsor::class)->create([
            'status' => Sponsor::STATUS_ACTIVE
        ]);

        $pendingSponsor = factory(Sponsor::class)->create([
            'status' => Sponsor::STATUS_PENDING
        ]);

        $this->browse(function ($browser) use ($activeSponsor, $pendingSponsor) {
            $browser
                ->loginAs($this->admin)
                ->visit(new Admin\ManageSponsorsPage)
                ->press(trans('messages.advanced_search'))
                ->waitFor('#queryModal')
                ->pause(500)
                ->select('status', Sponsor::STATUS_ACTIVE)
                ->press(trans('messages.submit'))
                ->assertSee($activeSponsor->name)
                ->assertDontSee($pendingSponsor->name)
                ->press(trans('messages.advanced_search'))
                ->waitFor('#queryModal')
                ->pause(500)
                ->select('status', Sponsor::STATUS_PENDING)
                ->press(trans('messages.submit'))
                ->assertSee($pendingSponsor->name)
                ->assertDontSee($activeSponsor->name)
                ;
        });
    }
}
