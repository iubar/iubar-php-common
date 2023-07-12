<?php

namespace Iubar\Net;

use Psr\Log\LogLevel;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 *
 * RIFERIMENTI
 * Codici erorri: http://www.greenend.org.uk/rjk/tech/smtpreplies.html SMTP reply codes
 * Peronalizzazione degli headers smtp: http://help.mandrill.com/entries/21688056-Using-SMTP-Headers-to-customize-your-messages
 *
 * La seguente classe utilizza la classe Symfony Mailer: https://github.com/symfony/mailer
 * port 25 or 587 for unencrypted / TLS connections
 * port 465 for SSL connections
 *
 */
abstract class AbstractEmailProvider {
	const TIMEOUT = 4; // 4 secondi

	protected string $smtp_user = '';
	protected bool $smtp_ssl = false;
	protected string $smtp_pwd = '';
	protected int $smtp_port = 0;

	protected string $subject = '';
	protected string $body_txt = '';
	protected string $body_html = '';
	protected array $attachments = []; // ie: array('/path/to/image.jpg'=>'image/jpeg');

	protected array $to_array = [];
	protected ?Address $from_address = null;
	protected ?Address $reply_to_address = null;

	protected $logger = null;

	abstract protected function getTransport();

	public function __construct() {
	}

	public function getFromAddress(): Address {
	    return $this->from_address;
	}
	
	public function send() : int {
		return $this->sendThrough($this->getTransport());
	}

	protected function sendThrough($transport) : int{
		$result = 0;

		$smtp_user = $transport->getUsername();
		$smtp_pwd = $transport->getPassword();

		if (!$smtp_user || !$smtp_pwd) {
			die('QUIT: smtp user or password not set' . PHP_EOL);
		} else {
			$host = $transport->getStream()->getHost();
			$port = $transport->getStream()->getPort();

			$fp = fsockopen($host, $port, $errno, $errstr, 5);
			if (!$fp) {
				// Port is closed or blocked
				$error = 'Impossibile contattare il server smtp ' . $host . ' sulla porta ' . $port;
				$this->log(LogLevel::ERROR, $error);
				throw new \Exception($error);
			} else {
				// Port is open and available
				fclose($fp);
			}

			// Create the Mailer using your created Transport
			$mailer = new Mailer($transport);

			foreach ($this->to_array as $email => $name) {
				$to_address = new Address($email, $name);
				// Send the message
				try {
					$mailer->send($this->createMessage($to_address));
					$result++;
				} catch (\Exception $e) {
					$error = $e->getMessage();
					$this->log(LogLevel::ERROR, $error);
					throw new \Exception('Impossibile inviare il messaggio: ' . $error);
				}
			}
		}

		return $result;
	}

	private function log($level, string $msg) : void {
		if ($this->logger) {
			$this->logger->log($level, $msg);
		}
	}

	public function setLogger($logger) : void {
		$this->logger = $logger;
	}

	public function setFrom(string $email, string $name = '') : void {
		$this->from_address = new Address($email, $name);
	}

	public function setReplyTo(string $email, string $name = '') : void {
		$this->reply_to_address = new Address($email, $name);
	}

	public function addTo(string $email, string $name = '') : void {
		$this->to_array[$email] = $name;
	}

	public function setSubject(string $subject): void {
		$this->subject = $subject;
	}

	public function setBodyHtml(string $html): void {
		$this->body_html = $html;
	}

	public function setBodyTxt(string $txt) : void {
		$this->body_txt = $txt;
	}

	public function setSmtpUser(string $user) : void {
		$this->smtp_user = $user;
	}

	public function setSmtpPassword(string $password) : void {
		$this->smtp_pwd = $password;
	}
	
	public function setSmtpPort(int $smtp_port) : void {
	    $this->smtp_port = $smtp_port;
	}
	
	public function addAttachment($filename, $type = null) {
		$this->attachments[$filename] = $type;
	}

	public function addAttachments($files = []) {
		foreach ($files as $file) {
			$this->addAttachment($file);
		}
	}

	private function createMessage(Address $to_address) {
		// Create a message
		$message = (new Email())
			->subject($this->subject)
			->from($this->from_address) // From: addresses specify who actually wrote the email
			->to($to_address);

		if ($this->reply_to_address !== null) {
			$message->replyTo($this->reply_to_address);
		}

		if (!$this->body_html) {
			$message->text($this->body_txt);
		} else {
			$message->html($this->body_html);
		}

		// ATTACHMENTS
		foreach ($this->attachments as $filename => $type) {
			// ie: $type = 'application/pdf'
			if (is_file($filename)) {
				$message->attachFromPath($filename);
			} else {
				$this->log(LogLevel::ERROR, 'Attachment not found: ' . $filename);
			}
		}

		return $message;
	}
}
