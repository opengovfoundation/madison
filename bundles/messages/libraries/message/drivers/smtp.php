<?php namespace Swiftmailer\Drivers;

use Swift_SmtpTransport;

class SMTP extends Driver {

	/**
	 * Register the Swift Mailer message and transport instances.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct($config)
	{
		parent::__construct($config);

		$this->transport = Swift_SmtpTransport::newInstance();

		$this->transport->setHost($config['host'])
						->setPort($config['port'])
						->setUsername($config['username'])
						->setPassword($config['password'])
						->setEncryption($config['encryption']);
	}
}