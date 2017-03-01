<?php

namespace Tests\Browser\Pages\CustomPages;

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
        return route('pages.create', [], false);
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
