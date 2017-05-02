<?php

namespace Tests\Browser\Pages\Document;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class ListPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('documents.index', [], false);;
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@deleteBtn' => '#content .destroy',
        ];
    }
}
