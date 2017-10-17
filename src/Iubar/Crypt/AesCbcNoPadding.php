<?php

namespace Iubar\Crypt;

// Implementa l'algoritmo "AES/CBC/NoPadding"

use Iubar\Crypt\AesBase;
use Iubar\Crypt\AesInterface;

class AesCbcNoPadding extends AesBase implements AesInterface {

	public function __construct($key){
		parent::__construct($key);
	}

	public function encrypt($plaintext, $iv){
		$encrypted = @mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $plaintext, MCRYPT_MODE_CBC, $iv);
		return (base64_encode($encrypted));
	}

	public function decrypt($encrypted, $iv){
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv);
		return $decrypted;
	}

}
