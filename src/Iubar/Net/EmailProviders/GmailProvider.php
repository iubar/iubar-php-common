<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;

class GmailProvider extends AbstractEmailProvider implements IEmailProvider {

	public function __construct(){
		parent::__construct();
	}
	
	protected function getTransport($port=null){
		// Attenzione: dal 2016 non si può più utilizzare l'smtp di Google con le opzioni di default.
		// Vedere :
		// https://www.google.com/settings/security/lesssecureapps
		// https://support.google.com/accounts/answer/6010255?hl=it
		
		// 		I limiti di utilizzo dei servizi SMTP di google sembrerebbe il seguente (i dati non sono ufficiali)
		//
		// 		== Gmail ==
		// 			500 per day 20 emails / hour
		//
		// 		== Google Apps ==
		// 			Messages per day 2000
		// 			Messages auto-forwarded 10,000
		// 			Auto-forward mail filters 20
		// 			Recipients per message 2000(500 external)
		// 			Total recipients per day 10,000
		// 			External recipients per day 3000
		// 			Unique recipients per day 3000(2000 external)
		// 			Recipients per message (sent via SMTP by POP or IMAP users) 99
		
		// Create the Transport
		$transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
		->setUsername($this->smtp_usr)
		->setPassword($this->smtp_pwd)
		->setTimeout(self::TIMEOUT);
		return $transport;
	}
	
}