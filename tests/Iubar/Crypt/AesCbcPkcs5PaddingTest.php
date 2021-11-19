<?php

declare(strict_types=1);

namespace Iubar\Crypt;

use PHPUnit\Framework\TestCase;
use Iubar\Crypt\AesCbcPkcs5Padding;

class AesCbcPkcs5PaddingTest extends TestCase {

	private static $config = [];

	public static function setUpBeforeClass() : void{
		$config_file = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
		if(!is_file($config_file)){
			die("Config not found: " . $config_file . PHP_EOL);
		}
		self::$config = include $config_file;
	}
    
    public function testDecrypt(){
        $aes = new AesCbcPkcs5Padding(self::$config['key']);
		$encrypted = $aes->encrypt(self::$config['plaintext'], self::$config['iv']);
		$data = $aes->getCryptedDataFromSignature($encrypted);
		$this->assertEquals($encrypted, $data);
        $this->assertTrue(true);
	}

}
