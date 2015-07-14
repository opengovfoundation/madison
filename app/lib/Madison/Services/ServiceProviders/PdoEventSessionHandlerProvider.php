<?php

namespace Madison\Services\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Madison\Session\Storage\Handler\PdoEventSessionHandler;
use Illuminate\Support\Facades\Session;

class PdoEventSessionHandlerProvider extends ServiceProvider
{
    public function register()
    {
        Session::extend('database_event', function ($app) {
                $connection = $app['config']['session.connection'];
                $db = $app['db']->connection($connection);
                $table = $db->getTablePrefix().$app['config']['session.table'];
                return new PdoEventSessionHandler($db->getPdo(), array(
                    'db_table' => $table,
                    'db_id_col' => 'id',
                    'db_data_col' => 'payload',
                    'db_time_col' => 'last_activity')
                );
        });
    }

    public function provides()
    {
        return array(
           'Madison\\Session\\Storage\\Handler\\PdoEventSessionHandler',
        );
    }
}
