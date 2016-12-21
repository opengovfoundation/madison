<?php

namespace App\Providers;

use File;
use Illuminate\Support\ServiceProvider;
use Image;
use Log;
use Storage;

class DocumentsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Services\Documents', function ($app) {
            $service = new \App\Services\Documents();

            return $service;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['App\Services\Documents'];
    }
}
