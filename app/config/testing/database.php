<?php

return array(
 
    'default' => 'mysql',
 
    'connections' => array(
        'mysql' => array(
        'driver'    => 'mysql',
        'host'      => getenv('DB_HOST'),
        'database'  => getenv('DB_NAME'),
        'username'  => getenv('DB_USER'),
        'password'  => getenv('DB_PASS'),
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
      )
    )
);