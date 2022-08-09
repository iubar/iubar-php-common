<?php

namespace Iubar\Crypt;

interface AesInterface {
	function encrypt($plaintext, $iv);

	function decrypt($crypted, $iv);
}
