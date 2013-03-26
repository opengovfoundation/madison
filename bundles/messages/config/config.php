<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Swift Mailer Transport
	|--------------------------------------------------------------------------
	|
	| The name of your default Swift Mailer Transport. This transport will used
	| as the default for all mailing operations unless a different name is
	| given when performing said operation. This transport name should be
	| listed in the array of transports below.
	|
	*/

	'default' => 'smtp',

	/*
	|--------------------------------------------------------------------------
	| Swift Mailer Transports
	|--------------------------------------------------------------------------
	|
	| Below is the configuration for each of the Swift Mailer transports. For
	| more information refer to:
	|
	|	http://swiftmailer.org/docs/sending.html
	| 
	|
	| If you want to use Gmail as your email transport, set
	|	'host'       =>	'smtp.gmail.com', 
	|	'username'   =>	'username@gmail.com',
	|	'password'   =>	'password',
	|	'port'       =>	465,
	|	'encryption' =>	'ssl',
	*/

	'transports' => array(

		'smtp' => array(
			'host'       => 'smtp.gmail.com',
			'port'       => 465,
			'username'   => 'cmbirk@gmail.com',
			'password'   => '$n!ck1954',
			'encryption' => 'ssl',
		),

		'sendmail' => array(
			'command' => '/usr/sbin/sendmail -bs',
		),

		'mail',

	),
);
