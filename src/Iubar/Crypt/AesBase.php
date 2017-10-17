<?php

namespace Iubar\Crypt;

abstract class AesBase {

	public $key = null;
	
	private $sig_delimiter = ':';

	abstract public function encrypt($plaintext, $iv);

	abstract public function decrypt($crypted, $iv);

	public function __construct($key){
		$this->key = $key;
	}

	// Generate an "initialization vector" (This too needs storing for decryption but we can append it to the encrypted data)
	// $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
	public function generateRandomIv(){
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
		$str = null;
		if ($sig !== null){
			$array = explode($this->sig_delimiter, $sig);
			if (isset($array[1])){
				$str = $array[1];
			}
		}

		return $str;
	}

	public function getCryptedDataFromSignature($sig){
		$str = null;
		if ($sig !== null){
			$array = explode($this->sig_delimiter, $sig);
			if (isset($array[0])){
				$str = $array[0];
			}
		}

		return $str;
	}

}