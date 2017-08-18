<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

class SendgridProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}
	
	protected function getTransport(){
		// Create the Transport		
		// 	Integrate with Sendgrid using SMTP
			
		// 	Change your SMTP authentication username and password to your SendGrid username and password, or set up a Multiple Credential with “mail” enabled.
		// 	Set the server host to smtp.sendgrid.net. This setting can sometimes be referred to as the external SMTP server, or relay, by some programs and services.
		// 	Ports
		
		// You can connect via unencrypted or TLS on ports 25, 2525, and 587.
		// You can also connect via SSL on port 465. Keep in mind that many hosting providers and ISPs block port 25 as a default practice. If this is the case, contact your host/ISP to find out which ports are open for outgoing smtp relay.
		// We recommend port 587 to avoid any rate limiting that your server host may apply.
		
	    $port = $this->smtp_port;
	    if ($port === null){
	        $port = 587;
	    }
	    
	    $transport = (new \Swift_SmtpTransport("smtp.sendgrid.net", $port, 'tls'))
		->setUsername($this->smtp_usr)
		->setPassword($this->smtp_pwd)
		->setTimeout(self::TIMEOUT);
		return $transport;
	}
	
}