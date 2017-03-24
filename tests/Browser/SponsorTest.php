<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Sponsor;
use Tests\Browser\Pages\Sponsor as SponsorPages;
use Tests\DuskTestCase;
use Tests\FactoryHelpers;
use Illuminate\Support\Facades\Event;
use Laravel\Dusk\Browser;

class SponsorTest extends DuskTestCase
{

    public function testMustBeLoggedInToCreateSponsor()
    {
        $this->browse(function ($browser) {
            $browser
                ->visit(new SponsorPages\CreatePage)
                ->assertSee('unauthorized')
                ;
        });
    }

    public function testUserCanCreateSponsor()
    {
        // TODO: currently doesn't work with Dusk
        //Event::fake();

        $sponsorAttrs = factory(Sponsor::class)->make();
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($sponsorAttrs, $user) {
            $browser
                ->loginAs($user)
                ->visit(new SponsorPages\CreatePage)
                ->fillNewSponsorForm($sponsorAttrs)
                ->click('@submitBtn')
                ->assertVisible('.alert.alert-info') // some success
                ;

            $sponsor = Sponsor::first();

            $browser->assertRouteIs('sponsors.members.index', $sponsor);

            $this->assertEquals($sponsor->status, Sponsor::STATUS_PENDING);

            $this->assertEquals($sponsor->name, $sponsorAttrs->name);
            $this->assertEquals($sponsor->display_name, $sponsorAttrs->display_name);
            $this->assertEquals($sponsor->address1, $sponsorAttrs->address1);
            $this->assertEquals($sponsor->city, $sponsorAttrs->city);
            $this->assertEquals($sponsor->state, $sponsorAttrs->state);
            $this->assertEquals($sponsor->postal_code, $sponsorAttrs->postal_code);
            $this->assertEquals($sponsor->phone, $sponsorAttrs->phone);

            // TODO: currently doesn't work with Dusk
            //Event::assertDispatched('App\Events\SponsorCreated', function ($e) use ($sponsor) {
            //    return $e->sponsor->id === $sponsor->id;
            //});
        });
    }

    public function testRedirectedToSoleSponsor()
    {
        $user = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($user);

        $this->browse(function ($browser) use ($user, $sponsor) {
            $browser
                ->loginAs($user)
                ->visitRoute('users.sponsors.index', $user)
                ->assertRouteIs('sponsors.documents.index', $sponsor)
                ;
        });
    }

    public function testNotRedirectedWithMultiSponsor()
    {
        $user = factory(User::class)->create();
        $sponsor1 = FactoryHelpers::createActiveSponsorWithUser($user);
        $sponsor2 = FactoryHelpers::createActiveSponsorWithUser($user);

        $this->browse(function ($browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visitRoute('users.sponsors.index', $user)
                ->assertRouteIs('users.sponsors.index', $user)
                ;
        });
    }

    public function testShowRedirectsToDocumentList()
    {
        $user = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($user);

        $this->browse(function ($browser) use ($user, $sponsor) {
            $browser
                ->loginAs($user)
                ->visitRoute('sponsors.show', $sponsor)
                ->waitForText($sponsor->display_name)
                ->assertRouteIs('sponsors.documents.index', $sponsor)
                ;
        });
    }

    public function testSponsorOwnerCanEditSponsorSettings()
    {
        $user = factory(User::class)->create();
        $sponsor = FactoryHelpers::createActiveSponsorWithUser($user);

        $newSponsorData = factory(Sponsor::class)->make();

        $this->browse(function ($browser) use ($user, $sponsor, $newSponsorData) {
            $browser
                ->loginAs($user)
                ->visit(new SponsorPages\EditPage($sponsor))
                ->assertFormHasDataForSponsor($sponsor)
                ->type('name', $newSponsorData->name)
                ->type('display_name', $newSponsorData->display_name)
                ->type('address1', $newSponsorData->address1)
                ->type('city', $newSponsorData->city)
                ->type('state', $newSponsorData->state)
                ->type('postal_code', $newSponsorData->postal_code)
                ->type('phone', $newSponsorData->phone)
                ->press('@submitBtn')
                ->assertRouteIs('sponsors.edit', $sponsor)
                ->assertFormHasDataForSponsor($sponsor->fresh())
                ;

            $sponsor = $sponsor->fresh();

            $this->assertEquals($sponsor->name, $newSponsorData->name);
            $this->assertEquals($sponsor->display_name, $newSponsorData->display_name);
            $this->assertEquals($sponsor->address1, $newSponsorData->address1);
            $this->assertEquals($sponsor->city, $newSponsorData->city);
            $this->assertEquals($sponsor->state, $newSponsorData->state);
            $this->assertEquals($sponsor->postal_code, $newSponsorData->postal_code);
            $this->assertEquals($sponsor->phone, $newSponsorData->phone);
        });
    }

    public function testNonOwnerCantEditSponsorSettings()
    {
        $owner = factory(User::class)->create();
        $editor = factory(User::class)->create();

        $sponsor = FactoryHelpers::createActiveSponsorWithUser($owner);
        $sponsor->addMember($editor->id, Sponsor::ROLE_EDITOR);

        $this->browse(function ($browser) use ($editor, $sponsor) {
            $browser
                ->loginAs($editor)
                ->visitRoute('sponsors.documents.index', $sponsor)
                ->assertDontSeeIn('#content .list-group', trans('messages.settings'))
                ->visitRoute('sponsors.edit', $sponsor)
                ->assertSee('unauthorized')
                ;
        });
    }
}
