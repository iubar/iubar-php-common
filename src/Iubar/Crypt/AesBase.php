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

	/**
     * Generate an "initialization vector" (This too needs storing for decryption but we can append it to the encrypted data)
	 */
	public function generateRandomIv(){
		$iv = openssl_random_pseudo_bytes(16);		
		// $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES_256_CBC')); // NON VA, PERCHE' ?
		// https://stackoverflow.com/questions/34871579/how-to-encrypt-plaintext-with-aes-256-cbc-in-php-using-openssl
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