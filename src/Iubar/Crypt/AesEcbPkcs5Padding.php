<?php

namespace Iubar\Crypt;

/**
 * Implementa l'algoritmo AES/CBC/PKCS5Padding
 */
class AesEcbPkcs5Padding extends AesBase implements AesInterface {
    public function __construct(string $key) {
		parent::__construct($key);
	}

	public function encrypt(string $input, string $key) : string|false {
	    throw new \BadMethodCallException("Questo metodo non è ancora implementato.");
 	}

	protected static function pkcs5_pad(string $text, string $blocksize)  : string|false {
	    throw new \BadMethodCallException("Questo metodo non è ancora implementato.");
	}
 
	public function decrypt(string $str, string $sKey)  : string|false {
	    throw new \BadMethodCallException("Questo metodo non è ancora implementato.");
 
	}
}
