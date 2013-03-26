<?php namespace Swiftmailer\Drivers;

use stdClass;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;

use Laravel\View;

class Driver {

	/**
	 * The instance of the SwiftMailer message.
	 *
	 * @var Swift_Mailer
	 */
	protected $swift;

	/**
	 * The instance of the SwiftMailer transport.
	 *
	 * @var mixed
	 */
	public $transport;

	/**
	 * The instance of the SwiftNailer mailer.
	 *
	 * @var Swift_Mailer
	 */
	protected $mailer;

	/**
	 * The email body.
	 *
	 * @var mixed
	 */
	public $body = array();

	/**
	 * The number of successfully sent emails.
	 *
	 * @var int
	 */
	public $result;

	/**
	 * The email addresses that the message will be sent to.
	 *
	 * @var array
	 */
	public $emails = array();

	/**
	 * The email addresses that did not successfully receive the message.
	 *
	 * @var array
	 */
	public $failed = array();

	/**
	 * Register the Swift Mailer message and transport instances.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct($config)
	{
		$this->body = new stdClass;
	}

	/**
	 * Prepare the Swift Message class
	 *
	 * @return Swift_Message
	 */
	public function swift()
	{
		if(is_null($this->swift))
		{
			$this->swift = Swift_Message::newInstance();
		}

		return $this->swift;
	}

	/**
	 * Prepare the Swift Mailer class
	 *
	 * @return Swift_Mailer
	 */
	public function mailer()
	{
		if(is_null($this->mailer))
		{
			$this->mailer = Swift_Mailer::newInstance($this->transport);
		}

		return $this->mailer;
	}

	/**
	 * Set the HTML content type.
	 *
	 * @param  bool    $use_html
	 * @return Driver
	 */
	public function html($use_html = true)
	{
		$content_type = ($use_html) ? 'text/html' : 'text/plain';

		$this->swift()->setContentType($content_type);

		return $this;
	}

	/**
	 * Set the subject.
	 *
	 * @param  string  $subject
	 * @return Driver
	 */
	public function subject($subject)
	{
		$this->swift()->setSubject($subject);

		return $this;
	}

	/**
	 * Add an email address to the from list.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return Driver
	 */
	public function from($email, $name = null)
	{
		if( ! is_array($email))
		{
			$this->swift()->addFrom($email, $name);
		}

		else
		{
			$this->swift()->setFrom($email, $name);
		}

		return $this;
	}

	/**
	 * Add an email address to reply to.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return Driver
	 */
	public function reply($email, $name = null)
	{
		$this->swift()->setReplyTo($email, $name);
		
		return $this;
	}

	/**
	 * Add an email address to the list of emails to send the email to.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return Driver
	 */
	public function to($email, $name = null)
	{
		if( ! is_array($email))
		{
			$this->swift()->addTo($email, $name);

			$this->emails[] = $email;
		}

		else
		{
			foreach($email as $key => $value)
			{
				// If a name isn't given, the key will be an int and the value
				// will be the email address.
				if(is_int($key))
				{
					$this->emails[] = $value;

					$this->swift()->addTo($value, null);
				}

				// If a name is given, the key will be the email address and
				// the value will be the name.
				else
				{
					$this->swift()->addTo($key, $value);

					$this->emails[] = $key;
				}
			}
		}

		return $this;
	}

	/**
	 * Add an email address to the list of emails the email should be copied to.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return Driver
	 */
	public function cc($email, $name = null)
	{
		if( ! is_array($email))
		{
			$this->swift()->addCc($email, $name);

			$this->emails[] = $email;
		}

		else
		{
			foreach($email as $key => $value)
			{
				// If a name isn't given, the key will be an int and the value
				// will be the email address.
				if(is_int($key))
				{
					$this->emails[] = $value;

					$this->swift()->addCc($value, null);
				}

				// If a name is given, the key will be the email address and
				// the value will be the name.
				else
				{
					$this->swift()->addCc($key, $value);

					$this->emails[] = $key;
				}
			}
		}

		return $this;
	}

	/**
	 * Add an email address to the list of emails the email should be
	 * blind-copied to.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return Driver
	 */
	public function bcc($email, $name = null)
	{
		if( ! is_array($email))
		{
			$this->swift()->addBcc($email, $name);

			$this->emails[] = $email;
		}

		else
		{
			foreach($email as $key => $value)
			{
				// If a name isn't given, the key will be an int and the value
				// will be the email address.
				if(is_int($key))
				{
					$this->emails[] = $value;

					$this->swift()->addBcc($value, null);
				}

				// If a name is given, the key will be the email address and
				// the value will be the name.
				else
				{
					$this->swift()->addBcc($key, $value);

					$this->emails[] = $key;
				}
			}
		}

		return $this;
	}

	/**
	 * Set the body of the email.
	 *
	 * @param  string  $message
	 * @return Driver
	 */
	public function body($body)
	{
		if(strpos($body, 'view: ') === 0)
		{
			$body = substr($body, 6);

			$body = View::make($body, (array) $this->body);
		}

		$this->body = $body;

		return $this;
	}

	/**
	 * Prepare the body and send it to the Swiftmailer.
	 *
	 * @return null
	 */
	protected function prepareBody()
	{
		// If the body is a view, we'll need to render it.
		if($this->body instanceof View)
		{
			$body = $this->body->render();
		}

		else
		{
			$body = $this->body;
		}

		$this->swift()->setBody($body);
	}

	/**
	 * Attach a file to the email.
	 *
	 * @param  string  $file_data
	 * @param  string  $file_name
	 * @param  string  $mime_type
	 * @return Driver
	 */
	public function attach($file_data, $file_name = '', $mime_type = '')
	{
		if(file_exists($file_data))
		{		
			$attachment = Swift_Attachment::fromPath($file_data);
			if($file_name != '')
			{
				$attachment->setFilename($file_name);
			}
			if($mime_type != '')
			{
				$attachment->setContentType($mime_type);
			}
		} 
		
		else 
		{
			$attachment = Swift_Attachment::newInstance($file_data, $file_name, $mime_type);
		}

		$this->swift()->attach($attachment);

		return $this;
	}

	/**
	* Set a custom header
	*
	* @param  string  $header
	* @param  string  $value
	* @return mixed
	*/
	public function header($header, $value = null)
	{
		$headers = $this->swift()->getHeaders();

		if($value == null)
		{
			return $headers->get($header);
		}

		else
		{
			$headers->addTextHeader($header, $value);

			return $this;
		}
	}

	/**
	 * Send the email.
	 *
	 * @return Driver
	 */
	public function send()
	{
		// Before we send anything we need to prepare the body.
		$this->prepareBody();

		// Now let's send the email.
		$this->result = $this->mailer()->send($this->swift(), $this->failed);

		// Now that the email is sent, let's clear the Swift_Message instance
		// so that it can be reinstantiated later if another message is created.
		$this->swift = null;

		return $this;
	}

	/**
	 *  Get the number of successfully sent emails.
	 *
	 * @return null|int
	 */
	public function result()
	{
		return $this->result;
	}

	/**
	 * Check if at least one email was sent. If an email address is provided,
	 * this will check if the email was successfully sent to that email
	 * address
	 *
	 * @param  string  $email
	 * @return bool
	 */
	public function was_sent($email = null)
	{
		if( ! is_null($email))
		{
			$sent = array_diff($this->emails, $this->failed);

			return in_array($email, $sent);
		}

		else
		{
			if( ! is_null($this->result))
			{
				return ($this->result > 0);
			}
		}

		return false;
	}

	/**
	 * Call a Swiftmailer method.
	 *
	 * @param  string  $name
	 * @param  array   $arguments
	 * @return Driver
	 */
	public function __call($name, $arguments)
	{
		call_user_func_array(array($this->swift(), $name), $arguments);

		return $this;
	}
}