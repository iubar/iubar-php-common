<?php

use Iubar\Crypt\AesEcbPkcs5Padding;

class AesEcbPkcs5PaddingTest extends \PHPUnit_Framework_TestCase {

	private $config = [];

	public function __construct(){
		$this->config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
	}

	public function testDecrypt(){
		$aes = new AesEcbPkcs5Padding($this->config['key']);
		$encrypted = $aes->encrypt($this->config['plaintext'], $this->config['iv']);
		$data = $aes->getCryptedDataFromSignature($encrypted);

		$this->assertEquals($encrypted, $data);
	}

}