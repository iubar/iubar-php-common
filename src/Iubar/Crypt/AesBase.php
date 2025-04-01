<?php

namespace Iubar\Crypt;

abstract class AesBase {
	public string $key = '';

	private $sig_delimiter = ':';

	abstract public function encrypt(string $plaintext, string $iv): string|false;

	abstract public function decrypt(string $crypted, string $iv): string|false;

	public function __construct(string $key) {
		$this->key = $key;
	}

	/**
	 * Generate an "initialization vector" (This too needs storing for decryption but we can append it to the encrypted data)
	 */
	public function generateRandomIv(): string {
		$iv = openssl_random_pseudo_bytes(16);
		// $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES_256_CBC')); // NON VA, PERCHE' ?
		// https://stackoverflow.com/questions/34871579/how-to-encrypt-plaintext-with-aes-256-cbc-in-php-using-openssl
		return $iv;
	}

	public function getIvsFromSignature(string $sig): string {
		$str = '';
		if ($sig) {
			$array = explode($this->sig_delimiter, $sig);
			if (isset($array[1])) {
				$str = $array[1];
			}
		}
		return $str;
	}

	public function getCryptedDataFromSignature(string $sig): string {
		$str = '';
		if ($sig) {
			$array = explode($this->sig_delimiter, $sig);
			$str = $array[0];
		}
		return $str;
	}
}
