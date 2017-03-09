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

    public function fillNewSponsorForm(Browser $browser, $attrs)
    {
        $browser->type('name', $attrs['name'])
            ->type('display_name', $attrs['display_name'])
            ->type('address1', $attrs['address1'])
            ->type('city', $attrs['city'])
            ->type('state', $attrs['state'])
            ->type('postal_code', $attrs['postal_code'])
            ->type('phone', $attrs['phone'])
            ;
    }
}
