<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->bind(
            'Madison\\Storage\\Interfaces\\UserRepositoryInterface',
            'Madison\\Storage\\UserRepository'
        );
    }

    public function provides()
    {
        return array(
            'Madison\\Storage\\Interfaces\\UserRepositoryInterface',
        );
    }
}
