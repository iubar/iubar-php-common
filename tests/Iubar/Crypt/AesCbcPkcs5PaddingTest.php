<?php

use Iubar\Crypt\AesCbcPkcs5Padding;

class AesCbcPkcs5PaddingTest extends \PHPUnit_Framework_TestCase {

	private $config = [];

	public function __construct(){
		$this->config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
	}

	public function testDecrypt(){
		$aes = new AesCbcPkcs5Padding($this->config['key']);
		$encrypted = $aes->encrypt($this->config['plaintext'], $this->config['iv']);
		$data = $aes->getCryptedDataFromSignature($encrypted);

		$this->assertEquals($encrypted, $data);

		$this->assertEquals($this->config['plaintext'], $aes->decrypt($encrypted, $this->config['iv']));
	}

	public function testSign(){
		$aes = new AesCbcPkcs5Padding('ThisIsASecretKey');
		$sig = "c14QKgy+Gb0l6F35KHS4ig==:WhIkiX8wOPbZjS8k";
		$data = $aes->getCryptedDataFromSignature($sig);
		$iv = $aes->getIvsFromSignature($sig);

		$plaintext = $aes->decrypt($data, $iv);
		// $plaintext = $aes->decrypt("c14QKgy+Gb0l6F35KHS4ig==", "WhIkiX8wOPbZjS8k");
		$this->assertEquals($plaintext, "unknown");
	}

}