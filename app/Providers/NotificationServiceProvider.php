<?php

namespace App\Providers;

use App\Notification\Notifier;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
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
        $this->app->singleton('notifier', function ($app) {
            $notifier = new Notifier($app['view'], $app['mailer'], $app['events']);

            $this->setNotifierDependencies($notifier, $app);

            return $notifier;
        });

        $this->app->singleton('App\Notification\Notifier', function ($app) {
            return $app['notifier'];
        });
    }

    /**
     * Set a few dependencies on the notifier instance.
     *
     * @param  \App\Notification\Notifier  $notifier
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function setNotifierDependencies($notifier, $app)
    {
        $notifier->setContainer($app);

        if ($app->bound('queue')) {
            $notifier->setQueue($app['queue.connection']);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['notifier', 'App\Notification\Notifier'];
    }
}
