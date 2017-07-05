<?php

interface AesInterface {

	abstract public function encrypt($plaintext, $iv);
	
	abstract public function decrypt($crypted, $iv);

}