<?php

namespace Iubar\Net\EmailProviders;

use Iubar\Net\AbstractEmailProvider;
use Iubar\Net\IEmailProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

/**
 * Amazon SES: @see https://docs.aws.amazon.com/ses/index.html
 * Eventualmente, in alternativa all'utilizzo di Symfony Mailer, implementato in questa classe (!),
 * si potrebbe istanziare direttamente il client dell'sdk di AmazonSes come illustrato nella documentazione seguente
 *  - https://docs.aws.amazon.com/en_us/ses/latest/DeveloperGuide/send-using-sdk-php.html
 *  - https://docs.aws.amazon.com/en_us/sdk-for-php/v3/developer-guide/getting-started_basic-usage.html
 *  - https://github.com/aws/aws-sdk-php
 */
class AmazonSesProvider extends AbstractEmailProvider implements IEmailProvider {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Il metodo implementa accesso SMPT al servizio SES.
	 * @see http://docs.aws.amazon.com/ses/latest/DeveloperGuide/smtp-connect.html
	 *
	 * Per un'eventuale utilizzo alternativo tramite REST API, si veda
	 *   - https://github.com/symfony/amazon-mailer/blob/5.4/Tests/Transport/SesApiTransportTest.php
	 *   - https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport
	 *
	 * {@inheritDoc}
	 * @see \Iubar\Net\AbstractEmailProvider::getTransport()
	 */
	protected function getTransport() {
		// Create the Transport
		// Port 587 or 2587
		$port = $this->smtp_port;
		if (!$port) {
			$port = 587;
		}

		$transport = new EsmtpTransport('email-smtp.eu-west-1.amazonaws.com', $port);
		$transport->setUsername($this->smtp_user);
		$transport->setPassword($this->smtp_pwd);

		return $transport;
	}
}
