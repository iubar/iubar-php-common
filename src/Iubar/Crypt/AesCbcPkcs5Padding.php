<?php
/*
 *  Implementa l'algoritmo AES/CBC/PKCS5Padding
 */
class AesCbcPkcs5Padding extends AesBase implements AesInterface {
	
	
protected $key;
protected $method = 'aes-128-cbc';
protected $iv = '1010101010101010';
protected $option = OPENSSL_CIPHER_AES_128_CBC;


function __construct($key)
{
	// Generate a 256-bit encryption key (This needs storing somewhere)
	// $encryption_key = openssl_random_pseudo_bytes(32);
	
	// Generate an "initialization vector" (This too needs storing for decryption but we can append it to the encrypted data)
	// $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
	
	$this->key = $key;
}

public function encrypt($data) {
	$enc = openssl_encrypt($data, $this->method, $this->key, $this->option, $this->iv);
	return base64_encode($enc);
}

public function decrypt($data) {
	$data = base64_decode($data);
	$dec = openssl_decrypt($data, $this->method, $this->key, $this->option, $this->iv);
	return $dec;
}

}