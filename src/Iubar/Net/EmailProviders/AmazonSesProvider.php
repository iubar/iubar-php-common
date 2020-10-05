<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

/**
* @see https://docs.aws.amazon.com/ses/index.html
* In alternativa ll'utilizzo del client Smtp (come implementato da questa classe), potrei istanziare il client dell'sdk di AmazonSes, vedi i seguenti documeti
* @see https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/send-using-sdk-php.html
* @see https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/getting-started_basic-usage.html
* @see https://github.com/aws/aws-sdk-php
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
		$transport = (new \Swift_SmtpTransport("email-smtp.eu-west-1.amazonaws.com", $port, 'tls')) // see https://eu-west-1.console.aws.amazon.com/ses/home?region=eu-west-1#smtp-settings:
		->setUsername($this->smtp_usr)
		->setPassword($this->smtp_pwd)
		->setTimeout(self::TIMEOUT);
		return $transport;
	}
	
}
