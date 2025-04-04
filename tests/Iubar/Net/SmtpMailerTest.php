<?php

namespace Iubar\Net;

use Iubar\Misc\Bench;
use Iubar\Misc\MiscUtils;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class SmtpMailerTest extends TestCase {
	// ARUBA
	private static $aruba_password = '';

	// AMAZON SES
	private static $amazonses_port = '';
	private static $amazonses_user = '';
	private static $amazonses_password = '';

	public static function setUpBeforeClass(): void {
		$ini_file = __DIR__ . '/secure-folder/passwords.config.ini';
		if (!is_file($ini_file)) {
			echo 'Loading config from enviroment vars...' . PHP_EOL;

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

	public function testArubaSsl() {
		// Aruba Ssl
		$bench_name = 'testArubassl';
		Bench::startTimer($bench_name);

		if (!self::$aruba_password) {
			$this->markTestSkipped('Credentials for Aruba are not available.');
		}

		$m = $this->factorySmtpMailer('aruba');
		$m->setSubject('TEST ARUBA');
		$m->setSmtpUser('info@iubar.it');
		$m->setSmtpPassword(self::$aruba_password);
		$m->setSmtpSsl(true);
		$m->setFrom('info@iubar.it', 'Iubar');
		$m->addTo('daniele.montesi@iubar.it', 'Daniele Montesi');
		$result = $m->send();
		echo Bench::stopTimer($bench_name, true) . PHP_EOL;
		$this->assertEquals(1, $result);
	}

	public function testAmazonSes1() {
		$bench_name = 'testAmazonSes1';
		Bench::startTimer($bench_name);
		$result = $this->sendAmazonSes('test');
		echo Bench::stopTimer($bench_name, true) . PHP_EOL;
		$this->assertEquals(1, $result);
	}

	public function testAmazonSes2() {
		$bench_name = 'testAmazonSes2';
		Bench::startTimer($bench_name);
		$result = $this->sendAmazonSes('non-align');
		echo Bench::stopTimer($bench_name, true) . PHP_EOL;
		$this->assertEquals(1, $result);
	}

	private function factorySmtpMailer(string $type): IEmailProvider {
		$logger = MiscUtils::loggerFactory('my_logger', LogLevel::DEBUG);

		$m = SmtpMailer::factory($type);
		$m->setLogger($logger);
		$m->setBodyTxt('Questa è una prova.');
		$m->setBodyHtml('<h2>Questo è un <b>test</b></h2>');
		return $m;
	}

	private function sendAmazonSes(string $subject): int {
		$from = 'info@iubar.it';
		$to = 'tester@email-test.had.dnsops.gov';
		$m = $this->factorySmtpMailer('amazonses');
		$m->setFrom($from, 'Iubar.it');
		$m->addTo($to);
		$m->setSubject($subject);
		$m->setSmtpUser(self::$amazonses_user);
		$m->setSmtpPassword(self::$amazonses_password);
		$m->setSmtpPort(self::$amazonses_port);

		$result = $m->send();
		return $result;
	}
}
