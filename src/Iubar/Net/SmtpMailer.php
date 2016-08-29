<?php

namespace Iubar\Net;

use Iubar\Net\EmailProviders\ArubaProvider;
use Iubar\Net\EmailProviders\GmailProvider;
use Iubar\Net\EmailProviders\MailgunProvider;
use Iubar\Net\EmailProviders\MailjetProvider;
use Iubar\Net\EmailProviders\MandrillProvider;
use Iubar\Net\EmailProviders\SendgridProvider;
use Iubar\Net\EmailProviders\SparkpostProvider;

class SmtpMailer {
	
	public static function factoryDefault(){
		return new EmailProviders\MailgunProvider();
	}
	
	public static function factory($provider_name){
		$provider = null;
		switch ($provider_name) {
			case 'aruba':
				$provider = new ArubaProvider();
				break;
			case 'gmail':
				$provider = new GmailProvider();
				break;
			case 'mailgun':
				$provider = new MailgunProvider();
				break;
			case 'mailjet':
				$provider = new MailjetProvider();
				break;
			case 'mandrill':
				$provider = new MandrillProvider();
				break;
			case 'sendgrid':
				$provider = new SendgridProvider();
				break;
			case 'sparkpost':
				$provider = new SparkpostProvider();
				break;
			default:
				throw new \Exception('Provider not supported: ' . $provider_name);
				break;				
		}
		return $provider;
	}	

	public static function getDomainFromEmail($email){
		// Get the data after the @ sign
		$array = explode('@', $email);
		$domain = $array[1];
		// oppure
		// $domain = substr(strrchr($email, "@"), 1);
		return $domain;
	}
	
} // end class
?>