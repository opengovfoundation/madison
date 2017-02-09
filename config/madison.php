<?php

return [
    'seeder' => [
        'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'admin_fname' => 'First',
        'admin_lname' => 'Last',
        'admin_password' => env('ADMIN_PASSWORD', 'secret'),

        'user_email' => 'user@example.com',
        'user_fname' => 'John',
        'user_lname' => 'Appleseed',
        'user_password' => 'password',

        'unconfirmed_email' => 'user2@example.com',
        'unconfirmed_fname' => 'Jane',
        'unconfirmed_lname' => 'Doe',
        'unconfirmed_password' => 'password'
    ],

    'image_sizes' => env('IMAGE_SIZES') ? json_decode(env('IMAGE_SIZES'), true) : [
        'featured' => [
            'height' => 300,
            'width' => 633,
            'crop' => true
        ]
    ],

    'date_format' => 'Y-m-d',
    'time_format' => 'H:i',

    'google_analytics_property_id' => env('GA', ''),
 ];
