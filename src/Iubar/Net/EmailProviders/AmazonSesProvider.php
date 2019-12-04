<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

/**
* @see https://docs.aws.amazon.com/ses/index.html
*/
class AmazonSesProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/smtp-connect.html
	 * {@inheritDoc}
	 * @see \Iubar\Net\AbstractEmailProvider::getTransport()
	 */
	protected function getTransport(){
		// Create the Transport
		// Port 587 or 2587
		$port = $this->smtp_port;
		if ($port === null){
			$port = 587;
		}		
		$transport = (new \Swift_SmtpTransport("email-smtp.eu-west-1.amazonaws.com", $port, 'tls'))
		->setUsername($this->smtp_usr)
		->setPassword($this->smtp_pwd)
		->setTimeout(self::TIMEOUT);
		return $transport;
	}
	
}
