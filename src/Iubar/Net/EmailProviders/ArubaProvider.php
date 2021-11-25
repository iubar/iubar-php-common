<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class ArubaProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}

	protected function getTransport(){
		// Create the Transport
		$transport = null;
		if ($this->smtp_ssl){			
			$transport = new EsmtpTransport('smtps.aruba.it', 465);
			$transport->setUsername($this->smtp_usr);
			$transport->setPassword($this->smtp_pwd);
		}
			
		return $transport;
	}

}