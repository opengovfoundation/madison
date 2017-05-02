<?php

namespace Tests\Browser\Pages\Sponsor;

use App\Models\Sponsor;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class CreatePage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('sponsors.create', [], false);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@submitBtn' => '#content form button[type=submit]',
        ];
    }

    public function fillNewSponsorForm(Browser $browser, $sponsorAttrs)
    {
        $browser->type('name', $sponsorAttrs->name)
            ->type('display_name', $sponsorAttrs->display_name)
            ->type('address1', $sponsorAttrs->address1)
            ->type('city', $sponsorAttrs->city)
            ->type('state', $sponsorAttrs->state)
            ->type('postal_code', $sponsorAttrs->postal_code)
            ->type('phone', $sponsorAttrs->phone)
            ;
    }
}
