<?php

interface AesInterface {

	abstract public function encrypt($plaintext, $iv);
	
	abstract public function decrypt($crypted, $iv);
	
	abstract public function getIvsFromSignature($sig);
	
	abstract public function getEncryptedDataFromSignature($sig);
	
	abstract public function validateSignature($sig);

}