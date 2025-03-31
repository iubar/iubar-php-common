<?php

namespace Iubar\Crypt;

/**
 * Implementa l'algoritmo "AES/CBC/NoPadding"
 */
class AesCbcNoPadding extends AesBase implements AesInterface {
	public function __construct($key) {
		parent::__construct($key);
	}

 	public function encrypt(string $plaintext, string $iv) : string|false{
 	    throw new \BadMethodCallException("Questo metodo non è ancora implementato.");
	}

	public function decrypt(string $encrypted, string $iv) : string|false{
	    throw new \BadMethodCallException("Questo metodo non è ancora implementato.");
	}
}
