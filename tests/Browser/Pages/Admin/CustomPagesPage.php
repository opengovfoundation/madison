<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class CustomPagesPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('admin.pages.index', [], false);
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@newPageBtn' => 'a.new',
            '@editPageBtn' => '.edit',
            '@destroyPageBtn' => '.destroy',
        ];
    }
}
