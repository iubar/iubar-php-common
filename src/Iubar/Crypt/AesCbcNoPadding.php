<?php

namespace Iubar\Crypt;

/**
 * Implementa l'algoritmo "AES/CBC/NoPadding"
 */ 
class AesCbcNoPadding extends AesBase implements AesInterface {

	public function __construct($key){
		parent::__construct($key);
	}

	public function encrypt($plaintext, $iv){
		 
	}

	public function decrypt($encrypted, $iv){
	 
	}

}
