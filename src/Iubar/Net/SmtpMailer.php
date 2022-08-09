<?php

namespace Iubar\Net;

use Iubar\Net\EmailProviders\AmazonSesProvider;
use Iubar\Net\EmailProviders\ArubaProvider;

/**
 * Usage:
 *
 * $mailer = SmtpMailer::factory('amazonses');
 * $mailer->smtp_usr = ...;
 * $mailer->smtp_pwd = ...;
 * $mailer->setFrom($config['from']);
 * $mailer->setTo($config['to']);
 * $mailer->setLogger($logger);
 * $mailer->setSubject("...");
 * $mailer->setBodyHtml("...");
 * $numSent = $mailer->send();
 *
 *
 * @author Borgo
 *
 */
class SmtpMailer {
	public static function factory($provider_name) {
		$provider = null;
		switch ($provider_name) {
			case 'aruba':
				$provider = new ArubaProvider();
				break;
			case 'amazonses':
				$provider = new AmazonSesProvider();
				break;
			default:
				throw new \Exception('Provider not supported: ' . $provider_name);
				break;
		}
		return $provider;
	}

	public static function getDomainFromEmail($email) {
		// Get the data after the @ sign
		$array = explode('@', $email);
		$domain = $array[1];
		// oppure
		// $domain = substr(strrchr($email, "@"), 1);
		return $domain;
	}
}
