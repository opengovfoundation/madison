<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Sponsor;
use Tests\Browser\Pages\Sponsor as SponsorPages;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Event;
use Laravel\Dusk\Browser;

class SponsorTest extends DuskTestCase
{

    public function testMustBeLoggedInToCreateSponsor()
    {
        $this->browse(function ($browser) {
            $browser
                ->visitRoute('sponsors.create')
                ->assertSee('unauthorized');
        });
    }

    public function testUserCanCreateSponsor()
    {
        // TODO: currently doesn't work with Dusk
        //Event::fake();

        $attrs = factory(Sponsor::class)->make()->toArray();
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($attrs, $user) {
            $browser->loginAs($user)
                ->visit(new SponsorPages\CreatePage)
                ->fillNewSponsorForm($attrs)
                ->click('@submitBtn')
                ;

            $sponsor = Sponsor::first();

            $this->assertEquals($sponsor->status, Sponsor::STATUS_PENDING);

            $this->assertEquals($sponsor->name, $attrs['name']);
            $this->assertEquals($sponsor->display_name, $attrs['display_name']);
            $this->assertEquals($sponsor->address1, $attrs['address1']);
            $this->assertEquals($sponsor->city, $attrs['city']);
            $this->assertEquals($sponsor->state, $attrs['state']);
            $this->assertEquals($sponsor->postal_code, $attrs['postal_code']);
            $this->assertEquals($sponsor->phone, $attrs['phone']);

            // TODO: currently doesn't work with Dusk
            //Event::assertDispatched('App\Events\SponsorCreated', function ($e) use ($sponsor) {
            //    return $e->sponsor->id === $sponsor->id;
            //});
        });
    }
}
