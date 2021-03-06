<?php

namespace Iubar\Crypt;

// Implementa l'algoritmo AES/CBC/PKCS5Padding

/**
 * @deprecated mcrypt was DEPRECATED in PHP 7.1.0, and REMOVED in PHP 7.2.0.
 */
class AesEcbPkcs5Padding extends AesBase implements AesInterface {

	public function __construct($key){
		parent::__construct($key);
	}

	public function encrypt($input, $key) {
		$size = @mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$input = self::pkcs5_pad($input, $size);
		$td = @mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
		$iv = @mcrypt_create_iv (@mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		@mcrypt_generic_init($td, $key, $iv);
		$data = @mcrypt_generic($td, $input);
		@mcrypt_generic_deinit($td);
		@mcrypt_module_close($td);
		$data = base64_encode($data);
		return $data;
	}

	private static function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	public function decrypt($sStr, $sKey) {
		$decrypted= @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, base64_decode($sStr), MCRYPT_MODE_ECB);
		$dec_s = strlen($decrypted);
		$padding = ord($decrypted[$dec_s-1]);
		$decrypted = substr($decrypted, 0, -$padding);
		return $decrypted;
	}
}