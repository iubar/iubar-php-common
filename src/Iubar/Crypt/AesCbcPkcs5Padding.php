<?php

namespace Iubar\Crypt;

/*
 *  Implementa l'algoritmo AES/CBC/PKCS5Padding
 */
class AesCbcPkcs5Padding extends AesBase implements AesInterface {
	protected string $method = 'AES-128-CBC';
	protected int $option = OPENSSL_CIPHER_AES_128_CBC; // oppure OPENSSL_RAW_DATA

	public function __construct(string $key) {
		parent::__construct($key);
	}

	public function encrypt(string $plaintext, string $iv): string|false {
		$enc = openssl_encrypt($plaintext, $this->method, $this->key, $this->option, $iv);
		return base64_encode($enc);
	}

	public function decrypt(string $encrypted, string $iv): string|false {
		$encrypted = base64_decode($encrypted);
		$dec = openssl_decrypt($encrypted, $this->method, $this->key, $this->option, $iv);
		return $dec;
	}
}
