<?php

namespace Iubar\Net;

use PHPUnit\Framework\TestCase;
use Iubar\Net\EmailValidator;

class EmailValidatorTest extends TestCase {
	public function testEmailValidator() {
		$email1 = 'pippo@iubar.it';
		$email2 = 'pippo@iubarzzxxx.it';
		$email4 = 'pippo@iubarzzxxx';
		$validator = new EmailValidator();
		$b1 = $validator->validate($email1, true, false);
		$this->assertTrue($b1);
		$b2 = $validator->validate($email2, true, false);
		$this->assertFalse($b2);
		// $b3 = $validator->validate($email1, true, true);
		// $this->assertFalse($b3);
		$b4 = $validator->validate($email2, false, false);
		$this->assertTrue($b4);
		$b5 = $validator->validate($email4, false, false);
		$this->assertFalse($b5);
	}
}
