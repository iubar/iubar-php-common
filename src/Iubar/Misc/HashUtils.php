<?php

namespace Iubar\Misc;

class HashUtils {
	
	private static $salt = '2138765&';
    private static $pepper = 'anything|else';
    private static $plainPassword = 'test';
	
	public static function hashedPassword($plainPassword) {
		
		// USAGE:		
		// 
		// if ($stored === HashUtils::hashedPassword('my password')) {
		//	...
		// }

		return sha1(self::$salt . sha1(self::$plainPassword . self::$pepper));
	}

}
