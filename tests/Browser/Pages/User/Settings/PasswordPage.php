<?php

namespace Tests\Browser\Pages\User\Settings;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class PasswordPage extends Page
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
        return route('users.settings.password.edit', $this->user, false);
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
