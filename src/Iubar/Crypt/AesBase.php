<?php


abstract class AesBase {
	
	private $key = null;
		
	abstract public function encrypt($plaintext, $iv);
	
	abstract public function decrypt($crypted, $iv);

	function __construct($key){
		$this->$key = $key;
	}
	
	// Generate an "initialization vector" (This too needs storing for decryption but we can append it to the encrypted data)
	// $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));	
	function generateRandomIv(){
		$wasItSecure = false;
		$iv = openssl_random_pseudo_bytes(16, $wasItSecure);
		if ($wasItSecure) {
			// We're good to go!
		} else {
			// Insecure result. Fail closed, do not proceed.
		}
		return $iv;
	}
	
	public function getIvsFromSignature($sig){
		// TODO: ...	
	}
	
	public function getCryptedDataFromSignature($sig){
		// TODO: ...
	}

}