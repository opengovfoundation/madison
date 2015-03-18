<?php

/**
* This is an example credential file.
* Make a copy named oauth_creds.yml to add your credentials.
*
* The raw credentials are separated from the config file in the case that
* a change is needed in the database config file
*/

$consumers = array(
    'Facebook'  => array(
        'client_id'     => getenv('FB_CLIENT_ID') ?: '',
        'client_secret' => getenv('FB_CLIENT_SECRET') ?: '',
        'scope'         => array('email')
    ),
    'Twitter'   => array(
        'client_id'     => getenv('TW_CLIENT_ID') ?: '',
        'client_secret' => getenv('TW_CLIENT_SECRET') ?: '',
    ),
    'Linkedin'  => array(
        'client_id'     => getenv('LI_CLIENT_ID') ?: '',
        'client_secret' => getenv('LI_CLIENT_SECRET') ?: '',
        'scope'         => array('r_basicprofile', 'r_emailaddress')
    ),
);
