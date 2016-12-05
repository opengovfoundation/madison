<?php

return array(
    'seeder' => array(
        'admin_email' => 'admin@example.com',
        'admin_fname' => 'First',
        'admin_lname' => 'Last',
        'admin_password' => 'password',

        'user_email' => 'user@example.com',
        'user_fname' => 'John',
        'user_lname' => 'Appleseed',
        'user_password' => 'password',

        'unconfirmed_email' => 'user2@example.com',
        'unconfirmed_fname' => 'Jane',
        'unconfirmed_lname' => 'Doe',
        'unconfirmed_password' => 'password'
    ),
    'image_sizes' => env('IMAGE_SIZES') ? json_decode(env('IMAGE_SIZES'), true) : array(
        'featured' => array(
            'height' => 300,
            'width' => 633,
            'crop' => true
        )
    )
);
