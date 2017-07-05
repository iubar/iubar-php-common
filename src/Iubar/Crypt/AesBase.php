<?php


abstract class AesBase {
	
	private $key = null;
		
	abstract public function encrypt($plaintext, $iv);
	
	abstract public function decrypt($crypted, $iv);

	function __construct($key){
		$this->$key = $key;
	}
	
	public function getIvsFromSignature($sig){
		// TODO: ...	
	}
	
	public function getEncryptedDataFromSignature($sig){
		// TODO: ...
	}
	
	public function validateSignature($sig){
		// TODO: ...
	}

}