<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page as BasePage;

abstract class Page extends BasePage
{
    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@headerNav' => '.navbar-static-top .navbar-right',
            '@footerNav' => 'footer.nav .navbar-nav',
        ];
    }
}
