<?php namespace Swiftmailer\Drivers;

use Swift_SendmailTransport;

class Sendmail extends Driver {

	/**
	 * Register the Swift Mailer message and transport instances.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct($config)
	{
		parent::__construct($config);

		$this->transport = Swift_SendmailTransport::newInstance($config['command']);
	}
}