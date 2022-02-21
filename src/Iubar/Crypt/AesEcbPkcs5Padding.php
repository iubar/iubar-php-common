<?php

namespace Iubar\Crypt;

/**
 * Implementa l'algoritmo AES/CBC/PKCS5Padding
 */
class AesEcbPkcs5Padding extends AesBase implements AesInterface {

	public function __construct($key){
		parent::__construct($key);
	}

	public function encrypt($input, $key) {
 
	}

	private static function pkcs5_pad ($text, $blocksize) {
 
	}

	public function decrypt($sStr, $sKey) {
 
	}
}