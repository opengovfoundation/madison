<?php

namespace Tests\Browser\Pages\Sponsor;

use App\Models\Sponsor;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class MembersPage extends Page
{
    public $sponsor;

    public function __construct(Sponsor $sponsor)
    {
        $this->sponsor = $sponsor;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('sponsors.members.index', [$this->sponsor], false);
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
            '@addMemberButton' => '.add-member',
        ];
    }

}
