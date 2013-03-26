<?php namespace Swiftmailer\Drivers;

use Swift_MailTransport;

class Mail extends Driver {

	/**
	 * Register the Swift Mailer message and transport instances.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct($config)
	{
		parent::__construct($config);

		$this->transport = Swift_MailTransport::newInstance();
	}
}