<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class RegisterPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('register', [], false);
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

    public function fillAndSubmitRegisterForm(Browser $browser, $user)
    {
        $browser
            ->type('fname', $user->fname)
            ->type('lname', $user->lname)
            ->type('email', $user->email)
            ->type('password', 'secret')
            ->type('password_confirmation', 'secret')
            ->press('Register')
            ;
    }
}
