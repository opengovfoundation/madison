<?php

Auth::extend('madison-provider', function($app) {
    $provider = new \Illuminate\Auth\EloquentUserProvider($app['hash'], $app['config']['auth.model']);

    return new \Madison\Auth\Guard($provider, $app['session.store']);
});
