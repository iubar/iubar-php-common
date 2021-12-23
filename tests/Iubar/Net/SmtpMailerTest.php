<?php

namespace Iubar\Net;

use Iubar\Misc\Bench;
use Iubar\Misc\MiscUtils;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class SmtpMailerTest extends TestCase {

	// ARUBA
    private static $aruba_password = "";
    
    // AMAZON SES
    private static $amazonses_port 		= "";
    private static $amazonses_user 		= "";
    private static $amazonses_password 		= "";

	public static function setUpBeforeClass() : void {
		$ini_file = __DIR__ . "/secure-folder/passwords.config.ini";
		if (!is_file($ini_file)){
		    echo "Loading config from enviroment vars..." . PHP_EOL;

            self::$aruba_password = getenv('aruba_password');
            self::$amazonses_port = getenv('amazonses_port');
            self::$amazonses_user = getenv('amazonses_user');
            self::$amazonses_password = getenv('amazonses_password');
		} else {
		    echo "Loading config from file $ini_file" . PHP_EOL;

    		$ini_array = parse_ini_file($ini_file);
            self::$aruba_password = $ini_array['aruba_password'];
            self::$amazonses_port = $ini_array['amazonses_port'];
            self::$amazonses_user = $ini_array['amazonses_user'];
            self::$amazonses_password = $ini_array['amazonses_password'];
		}
	}


	public function testArubaSsl(){ // Aruba Ssl
	    $bench_name = 'testArubassl';
	    Bench::startTimer($bench_name);
	    $m = $this->factorySmtpMailer('aruba');
	    $m->subject = "TEST ARUBA";
	    $m->smtp_usr = "info@iubar.it";
	    $m->smtp_pwd = self::$aruba_password;
	    if (!$m->smtp_pwd) {
	        $this->markTestSkipped('Credentials for Aruba are not available.');
	    }
	    $m->smtp_ssl = true;
	    $m->setFrom('info@iubar.it', 'Iubar');
	    $m->addTo('daniele.montesi@iubar.it', 'Daniele Montesi');
	    $result = $m->send();
	    echo Bench::stopTimer($bench_name, true) . PHP_EOL;
	    $this->assertEquals(1, $result);
    }
    
    public function testAmazonSes1(){
        $bench_name = 'testAmazonSes1';
        Bench::startTimer($bench_name);
        $result = $this->sendAmazonSes('test');
        echo Bench::stopTimer($bench_name, true) . PHP_EOL;
	    $this->assertEquals(1, $result);
    }

    public function testAmazonSes2(){
        $bench_name = 'testAmazonSes2';
        Bench::startTimer($bench_name);
        $result = $this->sendAmazonSes('non-align');
        echo Bench::stopTimer($bench_name, true) . PHP_EOL;
	    $this->assertEquals(1, $result);
    }

	private function factorySmtpMailer($type){
		$logger = MiscUtils::loggerFactory("my_logger", LogLevel::DEBUG, null);

		$m = SmtpMailer::factory($type);
		$m->setLogger($logger);
		$m->body_txt = "Questa è una prova.";
		$m->body_html = "<h2>Questo è un <b>test</b></h2>";
		return $m;
	}

    private function sendAmazonSes($subject){
        $from = 'info@iubar.it';
        $to = 'tester@email-test.had.dnsops.gov';
        $m = $this->factorySmtpMailer('amazonses');
        $m->setFrom($from);
        $m->addTo($to);
        $m->subject = $subject;
	    $m->smtp_usr = self::$amazonses_user;
	    $m->smtp_pwd = self::$amazonses_password;
        $m->smtp_port = self::$amazonses_port;
        $result = $m->send();
        return $result;
    }

}

