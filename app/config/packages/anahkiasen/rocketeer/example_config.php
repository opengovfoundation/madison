<?php return array(

    // Remote access
    //
    // You can either use a single connection or an array of connections
    //////////////////////////////////////////////////////////////////////

    // The default remote connection(s) to execute tasks on
    'default' => array('staging'),

    // The various connections you defined
    // You can leave all of this empty or remove it entirely if you don't want
    // to track files with credentials : Rocketeer will prompt you for your credentials
    // and store them locally
    'connections' => array(
        'staging' => array(
            'host'      => '{host}',
            'username'  => '{username}',
            'password'  => '{password}',
            'key'       => '{key}',
            'keyphrase' => '{keyphrase}',
        ),
    ),

    // Contextual options
    //
    // In this section you can fine-tune the above configuration according
    // to the stage or connection currently in use.
    // Per example :
    // 'stages' => array(
    // 	'staging' => array(
    // 		'scm' => array('branch' => 'staging'),
    // 	),
    //  'production' => array(
    //    'scm' => array('branch' => 'master'),
    //  ),
    // ),
    ////////////////////////////////////////////////////////////////////

    'on' => array(

        // Stages configurations
        'stages' => array(
            'demo' => array(
                'scm' => array('branch' => 'demo'),
            ),
            'production' => array(
                'scm' => array('branch', 'master'),
            ),
        ),

        // Connections configuration
        'connections' => array(
        ),

    ),

);
