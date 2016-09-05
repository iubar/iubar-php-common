<?php

namespace Iubar\Net;

use Psr\Log\LogLevel;
use Iubar\Net\SmtpMailer;
use Iubar\Misc\MiscUtils;
use Iubar\Common\LangUtil;
use Iubar\Common\ConsoleUtil;
use Iubar\Common\FileUtil;
use Ubench;

// == Common SMTP responses and what they mean ==

// SMTP replies will vary based on the server or ISP that issues them, though there are some general guidelines. Each response will include (at the very least) a 3 digit code. The first digit typically indicates whether the server was able to accept the message or not. For example:
// •2: A response that begins with a '2' generally means that the message was accepted without error.
// •4: Responses that start with '4' typically indicate a temporary error. These types of responses typically result in a soft bounce.
// •5: Responses with a '5' at the beginning of the code indicate permanent failure. These usually result in a hard bounce.

// The second and third digits can provide additional info, but are usually highly-contextual and specific to the particular mail server.


///////////////////////////////// FUNCTIONS

class SmtpMailerTest extends \PHPUnit_Framework_TestCase {
	
	// MANDRILL
	private static $mandrill_api_key = "";
	// https://bitbucket.org/mailchimp/mandrill-api-php
	// Api doc: https://mandrillapp.com/api/docs/index.php.html
	
	// MAILGUN
	private static $mailgun_api_key = "";	// FIXME: non utilizzato
	private static $mailgun_password = "";
	// Api base url: "https://api.mailgun.net/v3/" . $mailgun_domain;
	 	
	// ARUBA
	private static $aruba_password = "";
	
	// GMAIL	
	private static $gmail_password = "";
	
	// MAILJET
	private static $mailjet_api_key = ""; 		// smtp username
	private static $mailjet_secret_key = ""; 	// smtp password
	// 	https://github.com/mailjet/mailjet-apiv3-php
	// 	https://github.com/mailjet/mailjet-apiv3-php-simple
	
	// SENDGRID
	private static $sendgrid_password = "";	
	
	// SPARKPOST
	private static $sparkpost_password = "";	
	
	public static function setUpBeforeClass(){
		
		
		///////////////////////////////////////////////////////////////////////////////////
		
		// Configuro di seguito i certificati usati da CURL
		
		// $ch = curl_init("https://www.paypal.com/cgi-bin/webscr");
		// curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cacert.pem')
		// curl.cainfo=<path-to>cacert.pem
		
	    echo "setUpBeforeClass()" . PHP_EOL;
		
		if ($file = ini_get('curl.cainfo')) {
			if($error = FileUtil::checkFile($file)){
				die("CERT FILE IS CONFIGURED IN PHP.INI BUT WAS NOT FOUND ON DISK: " . $error);
			}else{
				echo "CERT IS OK: " . $file . PHP_EOL;
			}
		}else{
			echo "CERT FILE IS NOT CONFIGURED IN PHP.INI" - PHP_EOL;
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		
		
		$ini_file = __DIR__ . "/secure_folder/passwords.config.ini";
		if(!is_file($ini_file)){
			die("Passwords file not found: " . $ini_file . PHP_EOL);
		}
		$ini_array = parse_ini_file($ini_file);
		
		self::$mandrill_api_key 	= $ini_array['mandrill_api_key'];
		self::$mailgun_api_key 		= $ini_array['mailgun_api_key'];		// FIXME: non utilizzato
		self::$mailgun_password 	= $ini_array['mailgun_password'];
		self::$aruba_password 		= $ini_array['aruba_password'];
		self::$gmail_password 		= $ini_array['gmail_password'];
		self::$mailjet_api_key 		= $ini_array['mailjet_api_key']; 		// smtp username
		self::$mailjet_secret_key 	= $ini_array['mailjet_secret_key']; 	// smtp password
		self::$sendgrid_password 	= $ini_array['sendgrid_password'];
		self::$sparkpost_password 	= $ini_array['sparkpost_password'];		
	}
	
	
	public function testAruba(){ // Aruba
		$bench_name = 'testAruba';
		$bench = $this->startBench($bench_name);
		$m = $this->factorySmtpMailer('aruba');
		$m->subject = "TEST ARUBA";
		$m->smtp_usr = "info@iubar.it";
		$m->smtp_pwd = self::$aruba_password;
		$m->smtp_ssl = false;
		$result = $m->send();
		$this->stopBench($bench, $bench_name);
		$this->assertEquals(1, $result);
	}

// 	public function testGmail(){ // GMAIL
// 		$bench_name = 'testGmail';
// 		$bench = $this->startBench($bench_name);
// 		$m = $this->factorySmtpMailer('gmail');
// 		$m->subject = "TEST GMAIL";
// 		$m->smtp_usr = "borgogelli@iubar.it";
// 		$m->smtp_pwd = self::$gmail_password;		
// 		$result = $m->send();
// 		$this->stopBench($bench, $bench_name);
// 		$this->assertEquals(1, $result);
// 	}
	
// 	public function testMandrill(){ // Mandril
// 		$bench_name = 'testMandrill';
// 		$bench = $this->startBench($bench_name);
// 		$m = $this->factorySmtpMailer('mandrill');
// 		$m->smtp_usr  = "info@iubar.it";
// 		$m->smtp_pwd = self::$mandrill_api_key;
// 		$m->smtp_port = 587;
// 		$m->subject = "TEST MANDRILL";
// 		$result = $m->send();
// 		$this->stopBench($bench, $bench_name);
// 		$this->assertEquals(1, $result);
// 	}		
	
	private function getDomain($from_array){
	    // Recupero il nome di dominio dall'indirizzo email del mittente
	    $from_email = null;
	    if(LangUtil::isAnAssociativeArray($from_array)){
	        reset($from_array);
	        $from_email = key($from_array);
	    }else{
	        $from_email = $from_array[0];
	    }
	    $domain = SmtpMailer::getDomainFromEmail($from_email);
	    return $domain;
	}
	
	public function testMailgun(){ // Mailgun	
		$bench_name = 'testMailgun';
		$bench = $this->startBench($bench_name);		 		
		$m = $this->factorySmtpMailer('mailgun');
		$m->subject = "TEST MAILGUN";		
		$m->smtp_usr  = "postmaster@" . $this->getDomain($m->getFrom());
		$m->smtp_pwd = self::$mailgun_password;
		$result = $m->send();
		$this->stopBench($bench, $bench_name);
		$this->assertEquals(1, $result);
	}
	
	public function testSendGrid(){ // SendGrid
		$bench_name = 'testSendGrid';
		$bench = $this->startBench($bench_name);
		$m = $this->factorySmtpMailer('sendgrid');
		$m->subject = "TEST SENDGRID";		
		$m->smtp_usr = "iubar"; // utente registrato con indirizzo info@iubar.it
		$m->smtp_pwd = self::$sendgrid_password;
		$result = $m->send();
		$this->stopBench($bench, $bench_name);
		$this->assertEquals(1, $result);
	}
	
	public function testMailJet(){ // MailJet	
		$bench_name = 'testMailJet';
		$bench = $this->startBench($bench_name);
		$m = $this->factorySmtpMailer('mailjet');
		$m->subject = "TEST MAILJET";		
		$m->smtp_usr = self::$mailjet_api_key;
		$m->smtp_pwd = self::$mailjet_secret_key;
		$result = $m->send();
		$this->stopBench($bench, $bench_name);
		$this->assertEquals(1, $result);
	}
	
// 	public function testSparkPost(){ // SparkPost
// 		$bench_name = 'testSparkPost';
// 		$bench = $this->startBench($bench_name);	
// 		$m = $this->factorySmtpMailer('sparkpost');	
// 		$m->subject = "TEST SPARKPOST";	
// 		$m->smtp_usr = "SMTP_Injection"; // utente registrato con indirizzo info@iubar.it
// 		$m->smtp_pwd = self::$sparkpost_password;
// 		$result = $m->send();
// 		$this->stopBench($bench, $bench_name);
// 		$this->assertEquals(1, $result);
// 	}
	
	private function factorySmtpMailer($type){

		$logger = MiscUtils::loggerFactory("my_logger", LogLevel::DEBUG, null);				
		$from = array("info@fatturatutto.it" => "FatturaTutto.it");
		// $from = array("info@reteprofessionisti.it" => "ReteProf");			
		$to = array("daniele.montesi@iubar.it" => "Daniele");		
		// $to = array("borgogelli@iubar.it" => "Andrea Borgogelli Avveduti");
				
		$m = SmtpMailer::factory($type);
		$m->setLogger($logger);
		$m->enableAgentLogger(true);
		$m->setFrom($from);
		$m->setToList($to);
		$m->body_txt = "Questa è una prova.";
		$m->body_html = "<h2>Questo è un <b>test</b></h2>";
		return $m;
	}
	
	private function startBench($str, $log = true){
		if ($log){
			echo 'Start bench ' . $str . PHP_EOL;
		}
		$bench = new Ubench;		
		$bench->start();
		
		return $bench;
	}
	
	private function stopBench(Ubench $bench, $str, $log = true){
		$bench->end();
		if ($log){
			echo 'Stop bench ' . $str . PHP_EOL;
			echo 'Elapsed time: ' . $bench->getTime() . PHP_EOL;
			echo 'Memory peak: ' . $bench->getMemoryPeak() . PHP_EOL;
			echo 'Memory usage: ' . $bench->getMemoryUsage() . PHP_EOL;
		
			echo '--------' . PHP_EOL;
		}
	}

	private function interactive(){
		
		// Di seguito non è previsto il test SSL (non interessa)
		$providers = array( 
			"mandrill" => array(25, 587, 2525), // You can use port 25, 587, or 2525
			"mailgun" => array(25, 587, 2525), 	// Mailgun servers listen on ports 25, 587 (STARTTLS), and 2525	
			"mailjet" => array(25, 587), 		// Port 25 or 587
			"sendgrid" => array(587, 2525),		// Use port 587 for TLS connectionsor and 2525
			"sparkpost" => array(25, 587, 2525),// Use port 25 or 587 for unencrypted / TLS connections
			"aruba" => array(), 				// Nessuna selezione possibile per Aruba
			"gmail" => array()					// Nessuna selezione possibile per Gmail
		);

		$from_choices = array(
				"info@iubar.it" => "Iubar.it",
				"borgogelli@iubar.it" => "Andrea Borgogelli Avveduti",
				"daniele.montesi@iubar.it" => "Daniele Montesi",
				"systems@iubar.it" => "Andrea Borgogelli Avveduti",
				"info@reteprofessionisti.it" => "ReteProfessionisti.it",
				"forum@reteprofessionisti.it" => "ReteProfessionisti.it (Forum)",
				"info@fatturatutto.it" => "FatturaTutto.it",			
				"info@dasdadasdassdasdassdasd.it" => "Error"
		);
		$to_choices = $from_choices;
				
		$from = ConsoleUtil::showMenu($from_choices, "Mittente");
		$to = ConsoleUtil::showMenu($from_choices, "Destinatario");
		$provider = ConsoleUtil::showMenu($providers, "Provider");
		$ports = $providers[$provider]; 
		$port = ConsoleUtil::showMenu($ports, "Porta");
		
		echo ConsoleUtil::printTitle("Riepilogo");
		echo "From: " . $from . PHP_EOL;
		echo "To: " . $to . PHP_EOL;
		echo "Through: " . $provider . PHP_EOL;
		echo "Port: " . $port . PHP_EOL;
		ConsoleUtil::writeSeparator();
		$msg = "Provo a inviare la mail...";
		echo $msg . PHP_EOL;
	
		$bench_name = 'send';
		$bench = $this->startBench($bench_name);
		
		$m = SmtpMailer::factory($provider);		
		
		switch ($provider) {
		    case "mandrill":
		        $m->smtp_usr  = "info@iubar.it";
				$m->smtp_pwd = self::$mandrill_api_key;		
		        break;
		    case "mailjet":
				$m->smtp_usr = self::$mailjet_api_key;
				$m->smtp_pwd = self::$mailjet_secret_key;
		        break;
	        case "mailgun":
	        	$m->smtp_usr  = "postmaster@" . $domain;
				$m->smtp_pwd = self::$mailgun_password;
	        	break;		        
		    case "sendgrid":
				$m->smtp_usr = "iubar"; // utente registrato con indirizzo info@iubar.it
				$m->smtp_pwd = self::$sendgrid_password;
		        break;
	        case "sparkpost":
	        	$m->smtp_usr = "SMTP_Injection"; // utente registrato con indirizzo info@iubar.it
	        	$m->smtp_pwd = self::$sparkpost_password;
	        	break;
	        case "aruba":
				$m->smtp_usr = "info@iubar.it";
				$m->smtp_pwd = self::$aruba_password;
				$m->ssl = true;
	        	break;
        	case "gmail":
				$m->smtp_usr = "borgogelli@iubar.it";
				$m->smtp_pwd = self::$gmail_password;		
				
        		break;
        	default:
        		$error = "Errore: situazione imprevista";
        		die($error . PHP_EOL);
		}
		
		$m->smtp_port($port);
		$m->setFrom($from);
		$m->setToList(array($to));
		$m->subject = "TEST";
		$m->body_txt = "Questo è un test";
		$m->body_html = "<h2>Questo è un <b>test</b></h2>";	
		$m->enableAgentLogger(true);
		$result = $m->send();
		$this->stopBench($bench, $bench_name);
		
		return $result;
		
	}
	
}

?>
