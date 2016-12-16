<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

class ArubaProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}

	protected function getTransport(){
		// Create the Transport
		$transport = null;
		if($this->smtp_ssl){
			$transport = \Swift_SmtpTransport::newInstance("smtps.aruba.it", 465, 'ssl');
		}else{
			$transport = \Swift_SmtpTransport::newInstance("smtp.iubar.it", 25);
		}
		$transport->setUsername($this->smtp_usr)->setPassword($this->smtp_pwd)->setTimeout(self::TIMEOUT);	
		return $transport;
	}
	
}