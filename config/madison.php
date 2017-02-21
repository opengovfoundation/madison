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
        'unconfirmed_password' => 'password',

        'num_docs' => env('SEEDER_NUM_DOCS', 5),
        'num_comments_per_doc_min' => env('SEEDER_NUM_COMMENTS_MIN', 1),
        'num_comments_per_doc_max' => env('SEEDER_NUM_COMMENTS_MAX', 50),
        'comments_percentage_notes' => env('SEEDER_PERCENTAGE_NOTES', 0.3333),
        'comments_percentage_replied' => env('SEEDER_PERCENTAGE_REPLIED', 0.2),
        'num_replies_per_comment_min' => env('SEEDER_NUM_REPLIES_MIN', 1),
        'num_replies_per_comment_max' => env('SEEDER_NUM_REPLIES_MAX', 3),
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
