<?php

namespace Iubar\Crypt;

/**
 * Implementa l'algoritmo AES/CBC/PKCS5Padding
 */
class AesEcbPkcs5Padding extends AesBase implements AesInterface {
	public function __construct($key) {
		parent::__construct($key);
	}

	public function encrypt(string $plaintext, string $iv) : string|false {
		return '';
	}

	private static function pkcs5_pad($text, $blocksize) {
	}

	public function decrypt(string $encrypted, string $iv) : string|false {
		return '';
	}
}
