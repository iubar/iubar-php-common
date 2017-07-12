<?php

namespace Iubar\Net;

use Psr\Log\LogLevel;

abstract class AbstractEmailProvider {

	// RIFERIMENTI
	// Codici erorri: http://www.greenend.org.uk/rjk/tech/smtpreplies.html SMTP reply codes
	// Peronalizzazione degli headers smtp: http://help.mandrill.com/entries/21688056-Using-SMTP-Headers-to-customize-your-messages
	// La seguente classe utilizza la classe Swift Mailer: http://swiftmailer.org/

	// port 25 or 587 for unencrypted / TLS connections
	// port 465 for SSL connections

    const TIMEOUT = 4; // 4 secondi

	public $smtp_usr = null;
	public $smtp_ssl = false;
	public $smtp_pwd = null;
	public $smtp_port = null;

	public $subject = null;
	public $to_array = array(); // ie: array('some@address.tld' => 'The Name')
	public $body_txt = null;
	public $body_html = null;
	public $attachments = array(); // ie: array('/path/to/image.jpg'=>'image/jpeg');
	public $from_array = array();


	private $agent_logger_enabled = false;
	private $logger = null;

	abstract protected function getTransport();

	public function __construct(){
		// nothing to do
	}

	public function send(){
		return $this->sendThrough($this->getTransport());
	}

	private function fillMPartAlt($message){
		if(!$this->body_html){
			$message->setBody($this->body_txt);
		}else{
		    $message->setBody($this->body_html, 'text/html');
			if(!$this->body_txt){
				$message->addPart($this->body_html, 'text/plain');	 // see Spamassassin MPART_ALT_DIFF attribute
			}else{
			    $message->addPart($this->body_txt, 'text/plain');
			}
		}
		return $message;
	}


	protected function sendThrough($transport){

		$result = 0;

		$smtp_usr = $transport->getUsername();
		$smtp_pwd = $transport->getPassword();

		if(!$smtp_usr || !$smtp_pwd){
			die("QUIT: smtp user or password not set" . PHP_EOL);
		}else{
			$host = $transport->getHost();
			$port = $transport->getPort();

			$fp = fsockopen($host, $port, $errno, $errstr, 5);
			if (!$fp) {
				// Port is closed or blocked
				$error = "Impossibile contattare il server smtp " . $host. " sulla porta " . $port;
				$this->log($error);
				throw new \Exception($error);
			} else {
				// Port is open and available
				fclose($fp);
			}

			// Create the Mailer using your created Transport
			$mailer = \Swift_Mailer::newInstance($transport);

			if($this->agent_logger_enabled){

				// To use the ArrayLogger
				$mail_logger = new \Swift_Plugins_Loggers_ArrayLogger(); // Keeps a collection of log messages inside an array. The array content can be cleared or dumped out to the screen.
				$mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($mail_logger));

				// Or to use the Echo Logger
				// $mail_logger = new \Swift_Plugins_Loggers_EchoLogger();
				// $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($mail_logger));
			}

			// Send the message
			$failures = array();
			try{
				$result = $mailer->send($this->createMessage(), $failures); // The return value is the number of recipients who were accepted for delivery.
				if(count($failures)>0){
					$this->log(LogLevel::WARNING, "Result: " . $result);
					$this->log(LogLevel::ERROR, "There was an error");
					foreach ($failures as $key=>$value){
						$this->log(LogLevel::ERROR, $key . " ==> " . $value);
					}
				}else{
				    if($this->agent_logger_enabled){
    					$this->log(LogLevel::INFO, "Message successfully sent!");
    					$this->log(LogLevel::INFO, "Result: " . $result);
				    }
				}

				if($this->agent_logger_enabled){
					// Dump the log contents
					// NOTE: The EchoLogger dumps in realtime so dump() does nothing for it
					$this->log(LogLevel::INFO, "Logger output is:");
					$this->log(LogLevel::INFO, $mail_logger->dump());
					$this->log(LogLevel::INFO, "Done.");
				}
			}catch(\Swift_TransportException $e){
				// Il messaggio non è stato inviato
				$this->log(LogLevel::ERROR, $e->getMessage());
				throw new \Exception('Impossibile inviare il messaggio, problema di servizio');
			}catch(\Exception $e){
				$this->log(LogLevel::ERROR, $e->getMessage());
				throw new \Exception('Impossibile inviare il messaggio, errore sconosciuto');
			}

		}

		return $result;
	}

	private function log($level, $msg){
		if($this->logger){
			$this->logger->log($level, $msg);
		}else{
		    if ($this->agent_logger_enabled){
                echo "LOG: " . $msg . PHP_EOL;
		    }
		}
	}

	public function setLogger($logger){
		$this->logger = $logger;
	}
	public function enableAgentLogger($agent_logger_enabled){
		$this->agent_logger_enabled = $agent_logger_enabled;
	}

	public function getFrom(){
		return $this->from_array;
	}

	public function setFrom($email, $name=""){
		if(is_array($email)){
			// in questa situazione il valore di $name viene ignorato
			$this->from_array = $email;
		}else{
			if($name){
				$this->from_array[$email] = $name;
			}else{
				$this->from_array[] = $email;
			}
		}
	}

	public function setSubject($subject){
		$this->subject = $subject;
	}

	public function setToList($array){
		$this->to_array = $array;
	}

	public function setTo($to){
		$this->setToList($to);
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

	private function createMessage(){
		// Create a message
		// Deafult Character Set is UTF8 (http://swiftmailer.org/docs/messages.html)
		$message = \Swift_Message::newInstance($this->subject)
		->setFrom($this->from_array) 				// From: addresses specify who actually wrote the email
		->setTo($this->to_array);
		// ->setBcc(array('some@address.tld' => 'The Name'))
		// ->setSender('your@address.tld') 			// Sender: address specifies who sent the message
		// ->setReturnPath('bounces@address.tld') 	// The Return-Path: address specifies where bounce notifications should be sent

        $message = $this->fillMPartAlt($message);

		// Or set it like this
		// $message->setBody('My <em>amazing</em> body', 'text/html');
		// Add alternative parts with addPart()
		// $message->addPart('My amazing body in plain text', 'text/plain');

		// The priority of a message is an indication to the recipient what significance it has. Swift Mailer allows you to set the priority by calling the setPriority method. This method takes an integer value between 1 and 5:
		// 	Highest
		// 	High
		// 	Normal
		// 	Low
		// 	Lowest
		// $message->setPriority(2);


		// ATTACHMENTS
		foreach($this->attachments as $filename=>$type){ // ie: $type = 'application/pdf'
			// * Note that you can technically leave the content-type parameter out
			$attachment = \Swift_Attachment::fromPath($filename, $type); // ...->setFilename('cool.jpg');
			// Create the attachment with your data
			// $data = create_my_pdf_data();
			// $attachment = \Swift_Attachment::newInstance($data, 'my-file.pdf', 'application/pdf');
			// Attach it to the message
			if(is_file($filename)){
				$message->attach($attachment);
			}else{
				$this->log(LogLevel::ERROR, "Attachment not found: " . $filename);
			}
		}
		return $message;
	}

}