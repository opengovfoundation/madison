<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class LoginPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('login', [], false);
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
        ];
    }

    public function fillAndSubmitLoginForm(Browser $browser, $user)
    {
        $browser
            ->type('email', $user->email)
            ->type('password', 'secret')
            ->press('Login')
            ;
    }
}
