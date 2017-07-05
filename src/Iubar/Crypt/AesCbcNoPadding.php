<?php

// Implementa l'algoritmo "AES/CBC/NoPadding"

class AesCbcNoPadding extends AesBase implements AesInterface {
	
	
	public function encrypt($plaintext, $key, $iv){
		$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);	
		return (base64_encode($encrypted));
}

public function decrypt($encrypted, $key, $iv){
	$decrypted = mcrypt_decrypt(
		MCRYPT_RIJNDAEL_128,
		$key,
		base64_decode($encrypted),
		MCRYPT_MODE_CBC,
		$iv
		);
	return $decrypted;
	
}

}
