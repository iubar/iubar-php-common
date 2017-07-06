<?php

namespace Iubar\Crypt;

/*
 *  Implementa l'algoritmo AES/CBC/PKCS5Padding
 */
class AesCbcPkcs5Padding extends AesBase implements AesInterface {

 	protected $method = 'aes-128-cbc';
	protected $option = OPENSSL_CIPHER_AES_128_CBC;

	// Generate a 256-bit encryption key (This needs storing somewhere)
	// $encryption_key = openssl_random_pseudo_bytes(32);

	public function __construct($key){
		parent::__construct($key);
	}

	public function encrypt($plaintext, $iv) {
		$enc = openssl_encrypt($plaintext, $this->method, $this->key, $this->option, $iv);
		return base64_encode($enc);
	}

	public function decrypt($encrypted, $iv) {
		$encrypted = base64_decode($encrypted);
		$dec = openssl_decrypt($encrypted, $this->method, $this->key, $this->option, $iv);
		return $dec;
	}

}