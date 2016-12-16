<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

class MailgunProvider extends AbstractEmailProvider implements IEmailProvider {
   
	public function __construct(){
		parent::__construct();
	}
	
	protected function getTransport($port=null){
		// Create the Transport
		// Mailgun servers listen on ports 25, 465 (SSL/TLS), 587 (STARTTLS), and 2525
	    
	    $port = $this->smtp_port;
	    if ($port === null){
	        $port = 587;
	    }
	    
		$transport = \Swift_SmtpTransport::newInstance ('smtp.mailgun.org', $port)
		->setUsername($this->smtp_usr)
		->setPassword($this->smtp_pwd)
		->setTimeout(self::TIMEOUT);
	
		// Helps for sending mail locally during development
		// $transport->setLocalDomain ( '[127.0.0.1]' );
	
		return $transport;
	}
	
}