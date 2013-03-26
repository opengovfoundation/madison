<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Autoloader and dependency injection initialization for Swift Mailer.
 */

if (defined('SWIFT_REQUIRED_LOADED'))
{
	return;	
}

define('SWIFT_REQUIRED_LOADED', true);

// Load Swift utility class
require __DIR__.DS.'swiftmailer'.DS.'classes'.DS.'Swift.php';

// Start the autoloader
Swift::registerAutoload();

// Load the init script to set up dependency injection
require __DIR__.DS.'swiftmailer'.DS.'swift_init.php';

// Register the native quoted printable encoder to achieve much better 
// performance. This requires PHP 5.3, but since this is a Laravel bundle
// I think it's safe to assume that that shouldn't be a problem.
Swift::init(function()
{
	Swift_DependencyContainer::getInstance()
		->register('mime.qpcontentencoder')
		->asAliasOf('mime.nativeqpcontentencoder');
});

// Map the Message classes.
Autoloader::map(array(
	'Message' => __DIR__.DS.'libraries'.DS.'message.php',

	'Swiftmailer\\Drivers\\Driver'    => __DIR__.DS.'libraries'.DS.'message'.DS.'drivers'.DS.'driver.php',
	'Swiftmailer\\Drivers\\SMTP'      => __DIR__.DS.'libraries'.DS.'message'.DS.'drivers'.DS.'smtp.php',
	'Swiftmailer\\Drivers\\Sendmail'  => __DIR__.DS.'libraries'.DS.'message'.DS.'drivers'.DS.'sendmail.php',
	'Swiftmailer\\Drivers\\Mail'      => __DIR__.DS.'libraries'.DS.'message'.DS.'drivers'.DS.'mail.php',
));