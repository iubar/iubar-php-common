<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

class MailjetProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}
	
	protected function getTransport(){
		// Create the Transport
		// 	Port 25 or 587 (some providers block port 25)
		// 	If TLS on port 587 doesn't work, try using port 465 and/or using SSL instead
		
		$transport = (new \Swift_SmtpTransport("in-v3.mailjet.com", 587, 'tls'))
		->setUsername($this->smtp_usr) // API KEY
		->setPassword($this->smtp_pwd) // SECRET KEY
		->setTimeout(self::TIMEOUT);
		return $transport;
	}
	
}