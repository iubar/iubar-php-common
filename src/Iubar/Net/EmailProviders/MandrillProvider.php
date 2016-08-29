<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

class MandrillProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}
	
	protected function getTransport(){
		$port = $this->smtp_port;
		if ($port === null){
			$port = 587;
		}
		
		// Which SMTP ports can I use with Mandrill ?
		// You can use port 25, 587, or 2525 if you're not encrypting the communication between
		// your system and Mandrill or if you want to use the STARTTLS extension (also known as TLS encryption).
		// SSL is supported on port 465.
		// ISPs may redirect traffic on certain ports, so it's up to you which port you use.
		
		// Create the Transport
		$transport = \Swift_SmtpTransport::newInstance("smtp.mandrillapp.com", $port, 'tls')
		->setUsername($this->smtp_usr)
		->setPassword($this->smtp_pwd)
		->setTimeout(8); // 8 secondi
		return $transport;
	}
	
}