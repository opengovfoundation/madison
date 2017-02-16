<?php

namespace Tests\Browser\Pages\User\Settings;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class AccountPage extends Page
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('users.settings.account.edit', $this->user, false);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        //
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
