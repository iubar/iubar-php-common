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

	public $smtp_usr = null;
	public $smtp_ssl = false;
	public $smtp_pwd = null;
	public $smtp_port = null;

	public $subject = null;
	public $body_txt = null;
	public $body_html = null;
	public $attachments = []; // ie: array('/path/to/image.jpg'=>'image/jpeg');
	
	public $to_array = [];
	public $from_address = null;
	public $reply_to_address = null;

	private $logger = null;

	abstract protected function getTransport();
	
	public function __construct() {
	    
	}

	public function send(){
		return $this->sendThrough($this->getTransport());
	}

	protected function sendThrough($transport){
		$result = 0;

		$smtp_usr = $transport->getUsername();
		$smtp_pwd = $transport->getPassword();

		if(!$smtp_usr || !$smtp_pwd){
			die("QUIT: smtp user or password not set" . PHP_EOL);
		}else{
			$host = $transport->getStream()->getHost();
			$port = $transport->getStream()->getPort();

			$fp = fsockopen($host, $port, $errno, $errstr, 5);
			if (!$fp) {
				// Port is closed or blocked
				$error = "Impossibile contattare il server smtp " . $host. " sulla porta " . $port;
				$this->log(LogLevel::ERROR, $error);
				throw new \Exception($error);
			} else {
				// Port is open and available
				fclose($fp);
			}
			

			// Create the Mailer using your created Transport
			$mailer = new Mailer($transport);
			
			foreach ($this->to_array as $email => $name){
			    $to_address = new Address($email, $name);
			    // Send the message
			    try{
			        $mailer->send($this->createMessage($to_address));
			        $result++;
			    } catch(\Exception $e){
			        $error = $e->getMessage();
			        $this->log(LogLevel::ERROR, $error);
			        throw new \Exception('Impossibile inviare il messaggio: ' . $error);
			    }
			}
		}

		return $result;
	}

	private function log($level, $msg){
		if ($this->logger){
			$this->logger->log($level, $msg);
		}
	}

	public function setLogger($logger){
		$this->logger = $logger;
	}

	public function setFrom($email, $name = ''){
	    $this->from_address = new Address($email, $name);
	}

	public function setReplyTo($email, $name = ''){		
		$this->reply_to_address = new Address($email, $name);
	}
	
	public function setTo($to){
	    $email = null;
	    $name = '';
	    
	    if (is_array($to)){
	        foreach ($to as $value){
	            foreach ($value as $to_email => $to_name){
	                $email = $to_email;
	                $name = $to_name;
	                
	                if ($name == null){
	                    $name = '';
	                }
	                
	                $this->to_array[$email] = $name;
	            }
	        }
	    } else {
	        $email = $to;
	        $this->to_array[$email] = $name;
	    }
	}
	
	public function setSubject($subject){
		$this->subject = $subject;
	}

	public function setBodyHtml($html){
		$this->body_html = $html;
	}

	public function setBodyTxt($txt){
		$this->body_txt = $txt;
	}

	public function setSmtpUser($user){
		$this->smtp_usr = $user;
	}

	public function setSmtpPassword($password){
		$this->smtp_pwd = $password;
	}

	public function addAttachment($filename, $type=null){
		$this->attachments[$filename] = $type;
	}

	public function addAttachments($files = array()){
	    foreach ($files as $file){
	        $this->addAttachment($file);
	    }
	}

	private function createMessage(Address $to_address){
		// Create a message
		$message = (new Email())
		  ->subject($this->subject)
		  ->from($this->from_address)	// From: addresses specify who actually wrote the email
		  ->to($to_address);
		
		  if ($this->reply_to_address !== null){
		      $message->replyTo($this->reply_to_address);
		  }

		  if (!$this->body_html){
		      $message->text($this->body_txt);
		  } else {
		      $message->html($this->body_html);
		  }

		// ATTACHMENTS
		foreach($this->attachments as $filename=>$type){ // ie: $type = 'application/pdf'
			if (is_file($filename)){
			    $message->attachFromPath($filename);
			} else {
				$this->log(LogLevel::ERROR, "Attachment not found: " . $filename);
			}
		}
		
		return $message;
	}

}
