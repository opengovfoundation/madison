<?php

namespace Tests\Browser\Pages\CustomPages;

use App\Models\Page as CustomPage;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class EditPage extends Page
{
    public $customPage;

    public function __construct(CustomPage $page)
    {
        $this->customPage = $page;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('pages.edit', $this->customPage, false);
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
