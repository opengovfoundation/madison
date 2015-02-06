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
    'client_id'     => '',
    'client_secret' => '',
    'scope'         => array('email')
  ),
  'Twitter'   => array(
    'client_id'     => '',
    'client_secret' => ''
  ),
  'Linkedin'  => array(
    'client_id'     => '',
    'client_secret' => '',
    'scope'         => array('r_basicprofile', 'r_emailaddress')
  )
);
