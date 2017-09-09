<?php

namespace Iubar\Crypt;

class CryptUtils {

	private static $PADDING = true; // Se true, qualora $data non contenga i caratteri di padding, li aggiungo

	public static function base64UrlEncode($data) {
		return strtr(base64_encode($data), '+/', '-_');
	}

	public static function base64UrlDecode($data) {
		return base64_decode(self::base64SpecialCharsDecode($data));
	}

	public static function base64SpecialCharsDecode($data) {
		if(self::$PADDING){
			return str_pad(strtr($data, '-_', '+/'), strlen($data) + (4 - strlen($data) % 4) % 4, '=', STR_PAD_RIGHT);
		}else{
			return strtr($data, '-_', '+/');
		}
	}

}