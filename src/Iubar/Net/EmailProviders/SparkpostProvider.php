<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

class SparkpostProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}
	
	protected function getTransport(){
		// Create the Transport
		// Port 587 (Alternative Port: 2525)
		$port = $this->smtp_port;
		if ($port === null){
			$port = 587;
		}		
		$transport = \Swift_SmtpTransport::newInstance("smtp.sparkpostmail.com", $port, 'tls')
		->setUsername($this->smtp_usr)
		->setPassword($this->smtp_pwd);
		return $transport;
	}
	
}